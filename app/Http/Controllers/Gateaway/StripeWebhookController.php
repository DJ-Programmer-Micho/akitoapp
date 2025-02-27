<?php

namespace App\Http\Controllers\Gateaway;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use Illuminate\Support\Facades\Log;


class StripeWebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        $payload    = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');
        $secret     = env('STRIPE_WEBHOOK_SECRET'); // from Stripe Dashboard

        Log::info('Stripe Was Here');
        try {
            $event = \Stripe\Webhook::constructEvent($payload, $sig_header, $secret);

            if ($event->type === 'checkout.session.completed') {
                $session = $event->data->object;
                if ($session->payment_status === 'paid') {
                    $transaction = Transaction::where('stripe_session_id', $session->id)->first();
                    if ($transaction) {
                        $transaction->update(['status' => 'paid']);
                        if ($transaction->order) {
                            $transaction->order->update(['payment_status' => 'successful']);
                        }
                    }
                }
            }

            return response()->json(['status' => 'success'], 200);
        } catch (\Exception $e) {
            // log or handle error
            return response()->json(['error' => 'Invalid webhook'], 400);
        }
    }
}
