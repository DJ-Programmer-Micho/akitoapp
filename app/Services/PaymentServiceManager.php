<?php
namespace App\Services;

use Stripe\Stripe;
use App\Models\User;
use Firebase\JWT\JWT;
use Stripe\PaymentIntent;
use App\Models\WebSetting;
use App\Models\Transaction;
use Illuminate\Support\Str;
use App\Models\Gateaway\Payment;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Stripe\Checkout\Session as StripeSession;

class PaymentServiceManager
{
    private $order;
    private $paymentMethod;
    private $amount;
    private $currency = 'IQD';
    private $transactionId;
    private static $instance = false;

    //////////////////////////////
    // GET THE DATA NEED
    //////////////////////////////
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new PaymentServiceManager();
        }
        return self::$instance;
    }

    public function setOrder($order)
    {
        $this->order = $order;
        return $this;
    }

    public function setPaymentMethod($paymentMethod)
    {
        $this->paymentMethod = $paymentMethod;
        return $this;
    }

    public function setAmount($amount)
    {
        $this->amount = $amount;
        return $this;
    }

    public function setTransactionId($transactionId)
    {
        $this->transactionId = $transactionId;
        return $this;
    }

    //////////////////////////////
    // CHECK THE METHOD THAT CLIENT CHOOCES
    //////////////////////////////
    public function processPayment()
    {
        // Log::info($this->paymentMethod);
        switch ($this->paymentMethod) {
            case 'Areeba':
                return $this->processAreebaPayment();
            case 'ZainCash':
                return $this->processZainCashPayment();
            case 'FIB':
                return $this->processFIBPayment();
            case 'Stripe':
                return $this->processStripePayment();
            default:
                return false;
        }
    }

    //////////////////////////////
    // STRIPE METHODE
    //////////////////////////////
    private function processStripePayment()
    {
        try {
            Stripe::setApiKey(config('stripe.stripe.secret'));

            // Convert to cents if in dollars
            $amountInCents = (int) round($this->amount * 100);

            $session = StripeSession::create([
                'payment_method_types' => ['card'],
                'mode' => 'payment',
                'line_items' => [[
                    'price_data' => [
                        'currency'    => strtolower(config('stripe.currency', 'usd')),
                        'unit_amount' => $amountInCents,
                        'product_data' => [
                            'name' => 'Order #' . $this->order->id,
                        ],
                    ],
                    'quantity' => 1,
                ]],
                'success_url' => route('digit.payment.success', ['locale' => app()->getLocale()]) 
                                 . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url'  => route('digit.payment.cancel',  ['locale' => app()->getLocale()]),
            ]);

            // Create transaction row
            Transaction::create([
                'id'                => Str::uuid(),        // local primary key
                'stripe_session_id' => $session->id,       // store the real Stripe session
                'order_id'          => $this->order->id,
                'provider'          => 'Stripe',
                'amount'            => $this->amount,
                'currency'          => strtoupper(config('stripe.currency', 'USD')),
                'status'            => 'pending',
                // // store the checkout URL in "response" as JSON
                'response'          => json_encode([
                    'checkout_url' => $session->url
                ])
            ]);

            // Return session details to the BusinessController
            // dd($session->id, $session->url);
            return [
                'checkout_url' => $session->url,
                'paymentId'    => $session->id, // used in route('payment.process')
            ];
        } catch (\Exception $e) {
            Log::error("Stripe Checkout Error: " . $e->getMessage());
            return false;
        }
    }

    // STEP - 2: CALLED BY CALLBACK

    
    
    //////////////////////////////
    // FIB METHODE
    //////////////////////////////

    // MAIN FUNTION FOR POST SEND TO FIB PROVIDER
    private function processFIBPayment() {
        $accessToken = $this->getFIBToken();
        $exchangeRate = WebSetting::find(1)->exchange_price;
        if (!$accessToken) {
            return false;
        }

        $iq_currency = round($this->amount * $exchangeRate);
        Log::info($iq_currency);
        $paymentData = [
            "monetaryValue" => [
                "amount" => $iq_currency,
                "currency" => "IQD"
            ],
            // "statusCallbackUrl" => url('https://webhook-test.com/da1aa38397e2e61742c7861ba24e7570'),
            "statusCallbackUrl" => url('/api/payment/callback/fib'),
            "description" => "Order Payment",
            "expiresIn" => "PT15M",
            "category" => "ECOMMERCE",
            "refundableFor" => "PT24H",
        ];
        $paymentResponse = Http::withOptions([
            'verify' => true // SSL BYPASS
        ])->withHeaders([
            'Authorization' => "Bearer $accessToken",
            'Content-Type' => 'application/json'
            ])->post('https://fib.stage.fib.iq/protected/v1/payments', $paymentData);
            
        if (!$paymentResponse->successful()) {
            Log::error('FIB Payment Error: ' . $paymentResponse->body());
            return false;
        }
        $responseData = $paymentResponse->json();
        // Log::info("FIB UUID: ", ['pID' => $responseData['paymentId']]);
        
        Transaction::create([
            'id' => $responseData['paymentId'],
            'order_id' => $this->order->id,
            'provider' => 'FIB',
            'amount' => $iq_currency,
            'currency' => 'IQD',
            'status' => 'pending',
        ]);

        session([
            'qrCode' => $responseData['qrCode'],
            'readableCode' => $responseData['readableCode'],
            'personalAppLink' => $responseData['personalAppLink']
        ]);
        return $responseData;
    }

    // STEP - 1: GET THE BEARER TOKEN
    private function getFIBToken()
    {
        $tokenResponse = Http::withOptions([
            'verify' => true // SSL BYPASS
        ])->asForm()->post('https://fib.stage.fib.iq/auth/realms/fib-online-shop/protocol/openid-connect/token', [
            'grant_type' => 'client_credentials',
            'client_id' => env('FIB_CLIENT_ID'),
            'client_secret' => env('FIB_CLIENT_SECRET'),
        ]);

        if (!$tokenResponse->successful()) {
            // Log::error('FIB Token Error: ' . $tokenResponse->body());
            return false;
        }

        return $tokenResponse->json()['access_token'];
    }

    // STEP - 2: CALLED BY CALLBACK
    public function fetchFIBPaymentStatus($paymentId)
    {
        try {
            $accessToken = $this->getFIBToken(); // Securely get token

            if (!$accessToken) {
                Log::error("FIB Payment Status Error: Failed to get access token");
                return false;
            }

            $statusResponse = Http::withToken($accessToken)
                // ->withoutVerifying() // SSL BYPASS
                ->get("https://fib.stage.fib.iq/protected/v1/payments/{$paymentId}/status");

            if (!$statusResponse->successful()) {
                
                return false;
            }

            return $statusResponse->json();
        } catch (\Exception $e) {
            Log::error("FIB Payment Status Exception: " . $e->getMessage());
            return false;
        }
    }

    //////////////////////////////
    // AREEBA METHODE
    //////////////////////////////
    public function processAreebaPayment(){
        // $user = self::getUser();
        $apiKey = env('AREEBA_API_KEY');
        $url = "https://gateway.areebapayment.com/api/v3/transaction/$apiKey/debit";    
        $data = [
            "merchantTransactionId" => $this->transactionId,
            "amount" => $this->amount,
            "currency" => $this->currency,
            "successUrl" => url('/payment/success'),
            "cancelUrl" =>  url('/payment/cancel'),
            "errorUrl" => url('/payment/error'),
            // "callbackUrl" => route('areeba.callback'),
            // "callbackUrl" => url('/api/areeba/callback'),
            // "callbackUrl" => route('payment.callback', ['provider' => 'Areeba']),
            "callbackUrl" => route('payment.callback', ['provider' => strtolower($this->paymentMethod)]),
            // "callbackUrl" => url('https://webhook.site/ed86da25-5202-4449-afbf-c6618dbcb526'), 
            "customer" => [
                "firstName" => $this->order->customer->first_name,
                "lastName" => $this->order->customer->last_name,
                "ipAddress" => request()->ip(),
            ],
            "language" => "ar", 
        ];
        $response = Http::withBasicAuth(env('AREEBA_USERNAME'), env('AREEBA_PASSWORD'))
            ->post($url, $data);

        if ($response->successful()) {
            return $response->json()['redirectUrl'];
        } else {
            // Log::error('Areeba Payment Error: ' . $response->body());
            return false;
        }
    }
    //////////////////////////////
    // ZAINCASH METHODE
    //////////////////////////////
    public function processZainCashPayment(){
        //building data
        $data = [
        'amount' => $this->amount,
        'serviceType' => 'ecommerce',
        'msisdn' => env('ZAINCASH_SECRET_MSISDN'),
        'orderId' => $this->transactionId,
        // 'redirectUrl' => route('payment.callback', ['provider' => 'ZainCash']),
        'redirectUrl' => route('payment.callback', ['provider' => strtolower($this->paymentMethod)]),
        'iat'  => time(),
        'exp'  => time()+60*60*4
        ];

        $token = JWT::encode($data, env('ZAINCASH_SECRET_KEY'), 'HS256');

        $response = Http::asForm()->post('https://api.zaincash.iq/transaction/init', [
            'token' => $token,
            'merchantId' => env('ZAINCASH_MERCHANT_ID'),
            'lang' => 'en'
        ]);

        if ($response->successful()) {
            return 'https://api.zaincash.iq/transaction/pay?id=' . $response->json()['id'];
        } else {
            // Log::error('ZainCash Payment Error: ' . $response->body());
            return false;
        }
    }    
}
