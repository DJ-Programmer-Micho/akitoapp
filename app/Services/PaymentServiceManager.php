<?php
namespace App\Services;

use Stripe\Stripe;
use Firebase\JWT\JWT;
use App\Models\Payment;
use App\Models\Customer;
use App\Models\Transaction;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Stripe\Checkout\Session as StripeSession;

class PaymentServiceManager
{
    private $order;
    private $paymentId = null;
    private $exchange;
    private $delivery;
    private $paymentMethod;
    private $amount;
    private $currency = 'IQD';
    private $transactionId;
    private static $instance = false;

    private bool $isTopup = false;
    private ?Customer $topupCustomer = null;
    private ?Payment  $topupPayment  = null;
    private $mode = 'order'; // 'order' or 'wallet_topup'
    private $topupNetAmountMinor = 0;
    // private $topupCustomer = null;

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
        $this->mode  = 'order';
        return $this;
    }

    public function setTopupCustomer($customer)
    {
        $this->topupCustomer = $customer;
        return $this;
    }

    public function setTopupContext(Customer $customer, Payment $payment)
    {
        $this->topupCustomer = $customer;
        $this->topupPayment  = $payment;
        $this->mode          = 'wallet_topup';

        return $this;
    }

    public function setTopupNetAmount(int $netAmountMinor)
    {
        $this->topupNetAmountMinor = $netAmountMinor;
        $this->mode = 'wallet_topup';
        return $this;
    }

    public function processTopup()
    {
        $this->mode = 'wallet_topup';
        return $this->processPayment();
    }
    
    public function setDelivery($delivery) // IN IQD
    {
        $this->delivery = $delivery;
        return $this;
    }

    public function setPaymentMethod($paymentMethod)
    {
        $this->paymentMethod = $paymentMethod;
        return $this;
    }

    public function setAmount($amount) // IN IQD minor units (integer)
    {
        $this->amount = $amount;
        return $this;
    }

    public function setTransactionId($transactionId)
    {
        $this->transactionId = $transactionId;
        return $this;
    }

    public function setPaymentId($paymentId)
    {
        $this->paymentId = $paymentId;
        return $this;
    }

    public function setExchange(?float $rate)
    {
        $this->exchange = $rate;
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
            $deliveryInDollar = round($this->delivery / $this->exchange);
            // Convert to cents if in dollars
            $amountInCents = (int) round($this->amount * 100);
            $finalAmount = $amountInCents + $deliveryInDollar;
            $session = StripeSession::create([
                'payment_method_types' => ['card'],
                'mode' => 'payment',
                'line_items' => [[
                    'price_data' => [
                        'currency'    => strtolower(config('stripe.currency', 'usd')),
                        'unit_amount' => $finalAmount,
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

            $dbAmount = $this->amount + $deliveryInDollar;
            // Create transaction row
            Transaction::create([
                'id'                => Str::uuid(),        // local primary key
                'stripe_session_id' => $session->id,       // store the real Stripe session
                'order_id'          => $this->order->id,
                'provider'          => 'Stripe',
                'amount'            => $dbAmount,
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
    private function processFIBPayment()
    {
        $fib = app(\App\Services\FibService::class);

        if ($this->mode === 'wallet_topup') {
            $description  = "Wallet top-up #{$this->topupPayment?->id} (customer #{$this->topupCustomer?->id})";
            $orderIdForTxn = null;
        } else {
            $description  = "Order #{$this->order->id}";
            $orderIdForTxn = $this->order->id;
        }

        $payload = [
            "monetaryValue" => [
                "amount"   => (int) $this->amount,
                "currency" => "IQD",
            ],
            "statusCallbackUrl" => url('/api/payment/callback/fib'),
            "description"       => $description,
            "expiresIn"         => "PT15M",
            "category"          => "ECOMMERCE",
            "refundableFor"     => "PT24H",
        ];

        $resp = $fib->createPayment($payload);

        if (!$resp || empty($resp['paymentId'])) {
            Log::error('FIB Payment Error: createPayment failed', ['resp' => $resp]);
            return false;
        }

        Transaction::create([
            'id'           => $resp['paymentId'],
            'payment_id'   => $this->paymentId ?? null,
            'order_id'     => $orderIdForTxn,
            'provider'     => 'FIB',
            'amount_minor' => (int) $this->amount,
            'amount'       => (int) $this->amount,
            'currency'     => 'IQD',
            'status'       => 'pending',
            'response'     => $resp,
        ]);

        session([
            'qrCode'          => $resp['qrCode'] ?? null,
            'readableCode'    => $resp['readableCode'] ?? null,
            'personalAppLink' => $resp['personalAppLink'] ?? null,
        ]);

        return [
            'paymentId'     => $resp['paymentId'],
            'qrCode'        => $resp['qrCode'] ?? null,
            'readableCode'  => $resp['readableCode'] ?? null,
            'appLink'       => $resp['personalAppLink'] ?? null,
        ];
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
            Log::error('FIB Token Error: ' . $tokenResponse->body());
            return false;
        }

        return $tokenResponse->json()['access_token'];
    }

    // STEP - 2: CALLED BY CALLBACK
    // public function fetchFIBPaymentStatus($paymentId)
    // {
    //     try {
    //         $accessToken = $this->getFIBToken();
    //         if (!$accessToken) return false;

    //         $statusResponse = Http::withToken($accessToken)
    //             ->get("https://fib.stage.fib.iq/protected/v1/payments/{$paymentId}/status");

    //         if (!$statusResponse->successful()) return false;

    //         return $statusResponse->json();
    //     } catch (\Exception $e) {
    //         Log::error("FIB Payment Status Exception: " . $e->getMessage());
    //         return false;
    //     }
    // }
    public function fetchFIBPaymentStatus($paymentId)
    {
        return app(\App\Services\FibService::class)->fetchPaymentStatus((string) $paymentId);
    }

    //////////////////////////////
    // AREEBA METHODE
    //////////////////////////////
    public function processAreebaPayment()
    {
        $apiKey = env('AREEBA_API_KEY');
        $url    = "https://gateway.areebapayment.com/api/v3/transaction/$apiKey/debit";

        $data = [
            "merchantTransactionId" => $this->transactionId ?? Str::uuid(),
            "amount"   => (int)$this->amount,  // IQD minor
            "currency" => "IQD",
            "successUrl" => url('/payment/success'),
            "cancelUrl"  => url('/payment/cancel'),
            "errorUrl"   => url('/payment/error'),
            "callbackUrl"=> route('payment.callback', ['provider' => strtolower($this->paymentMethod)]),
            "customer"   => [
                "firstName" => $this->order->first_name,
                "lastName"  => $this->order->last_name,
                "ipAddress" => request()->ip(),
            ],
            "language" => "ar",
        ];

        $response = Http::withBasicAuth(env('AREEBA_USERNAME'), env('AREEBA_PASSWORD'))
                        ->post($url, $data);

        if (!$response->successful()) {
            Log::error('Areeba Payment Error: ' . $response->body());
            return false;
        }

        $resp = $response->json();

        Transaction::create([
            'id'           => $data['merchantTransactionId'],
            'payment_id'   => $this->paymentId,
            'order_id'     => $this->order->id,
            'provider'     => 'Areeba',
            'amount_minor' => (int)$this->amount,
            'currency'     => 'IQD',
            'status'       => 'pending',
            'response'     => $resp,
        ]);

        return [
            'paymentId'   => $data['merchantTransactionId'],
            'checkout_url'=> $resp['redirectUrl'] ?? null,
        ];
    }

    //////////////////////////////
    // ZAINCASH METHODE
    //////////////////////////////
    public function processZainCashPayment()
    {
        $payload = [
            'amount'      => (int)$this->amount,   // IQD minor
            'serviceType' => 'ecommerce',
            'msisdn'      => env('ZAINCASH_SECRET_MSISDN'),
            'orderId'     => $this->transactionId ?? Str::uuid(),
            'redirectUrl' => route('payment.callback', ['provider' => strtolower($this->paymentMethod)]),
            'iat'         => time(),
            'exp'         => time() + 60*60*4
        ];

        $token = JWT::encode($payload, env('ZAINCASH_SECRET_KEY'), 'HS256');

        $response = Http::asForm()->post('https://api.zaincash.iq/transaction/init', [
            'token'      => $token,
            'merchantId' => env('ZAINCASH_MERCHANT_ID'),
            'lang'       => 'ar'
        ]);

        if (!$response->successful()) {
            Log::error('ZainCash Payment Error: ' . $response->body());
            return false;
        }

        $resp = $response->json();

        Transaction::create([
            'id'           => $payload['orderId'],
            'payment_id'   => $this->paymentId,
            'order_id'     => $this->order->id,
            'provider'     => 'ZainCash',
            'amount_minor' => (int)$this->amount,
            'currency'     => 'IQD',
            'status'       => 'pending',
            'response'     => $resp,
        ]);

        return [
            'paymentId'   => $payload['orderId'],
            'checkout_url'=> 'https://api.zaincash.iq/transaction/pay?id=' . ($resp['id'] ?? ''),
        ];
    }
    
}
