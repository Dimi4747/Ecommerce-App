<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class PaymentService
{
    public function processPayment(Order $order, string $paymentMethod, array $paymentDetails): Payment
    {
        return DB::transaction(function () use ($order, $paymentMethod, $paymentDetails) {
            if ($order->payment_status === 'paid') {
                throw new \Exception('Order is already paid');
            }

            $payment = Payment::create([
                'order_id' => $order->id,
                'payment_method' => $paymentMethod,
                'amount' => $order->total_amount,
                'currency' => 'USD',
                'status' => 'pending',
                'payment_details' => $paymentDetails
            ]);

            try {
                $result = $this->processWithProvider($paymentMethod, $paymentDetails, $order);
                
                if ($result['success']) {
                    $payment->update([
                        'status' => 'completed',
                        'transaction_id' => $result['transaction_id'] ?? null,
                        'paid_at' => now()
                    ]);

                    $order->update(['payment_status' => 'paid']);
                } else {
                    $payment->update([
                        'status' => 'failed',
                        'payment_details' => array_merge($paymentDetails, [
                            'error' => $result['error'] ?? 'Payment failed'
                        ])
                    ]);
                }

                return $payment;
            } catch (\Exception $e) {
                $payment->update([
                    'status' => 'failed',
                    'payment_details' => array_merge($paymentDetails, [
                        'error' => $e->getMessage()
                    ])
                ]);

                throw $e;
            }
        });
    }

    public function processRefund(Payment $payment, float $amount, string $reason): array
    {
        if ($payment->status !== 'completed') {
            throw new \Exception('Payment cannot be refunded');
        }

        if ($amount > $payment->amount) {
            throw new \Exception('Refund amount cannot exceed payment amount');
        }

        try {
            $result = $this->processRefundWithProvider($payment, $amount, $reason);
            
            if ($result['success']) {
                if ($amount >= $payment->amount) {
                    $payment->update(['status' => 'refunded']);
                }

                return [
                    'success' => true,
                    'refund_id' => $result['refund_id'] ?? null,
                    'amount' => $amount
                ];
            } else {
                throw new \Exception($result['error'] ?? 'Refund failed');
            }
        } catch (\Exception $e) {
            throw new \Exception('Refund processing failed: ' . $e->getMessage());
        }
    }

    public function handleWebhook(string $provider, array $payload): void
    {
        switch ($provider) {
            case 'stripe':
                $this->handleStripeWebhook($payload);
                break;
            case 'paypal':
                $this->handlePaypalWebhook($payload);
                break;
            default:
                throw new \Exception("Unsupported payment provider: {$provider}");
        }
    }

    private function processWithProvider(string $paymentMethod, array $paymentDetails, Order $order): array
    {
        switch ($paymentMethod) {
            case 'stripe':
                return $this->processStripePayment($paymentDetails, $order);
            case 'paypal':
                return $this->processPaypalPayment($paymentDetails, $order);
            case 'credit_card':
                return $this->processCreditCardPayment($paymentDetails, $order);
            default:
                throw new \Exception("Unsupported payment method: {$paymentMethod}");
        }
    }

    private function processStripePayment(array $paymentDetails, Order $order): array
    {
        // Mock Stripe integration
        // In real implementation, you would use Stripe SDK
        
        if (empty($paymentDetails['token'])) {
            return ['success' => false, 'error' => 'Invalid payment token'];
        }

        // Simulate payment processing
        if (rand(0, 10) > 1) { // 90% success rate for demo
            return [
                'success' => true,
                'transaction_id' => 'ch_' . uniqid()
            ];
        } else {
            return ['success' => false, 'error' => 'Payment declined'];
        }
    }

    private function processPaypalPayment(array $paymentDetails, Order $order): array
    {
        // Mock PayPal integration
        // In real implementation, you would use PayPal SDK
        
        if (empty($paymentDetails['payment_id'])) {
            return ['success' => false, 'error' => 'Invalid PayPal payment ID'];
        }

        // Simulate payment processing
        return [
            'success' => true,
            'transaction_id' => 'PAY-' . uniqid()
        ];
    }

    private function processCreditCardPayment(array $paymentDetails, Order $order): array
    {
        // Mock credit card processing
        // In real implementation, you would use a payment gateway
        
        $required = ['card_number', 'expiry', 'cvv', 'name'];
        foreach ($required as $field) {
            if (empty($paymentDetails[$field])) {
                return ['success' => false, 'error' => "Missing {$field}"];
            }
        }

        // Simulate payment processing
        return [
            'success' => true,
            'transaction_id' => 'CC-' . uniqid()
        ];
    }

    private function processRefundWithProvider(Payment $payment, float $amount, string $reason): array
    {
        // Mock refund processing
        // In real implementation, you would use the payment provider's API
        
        return [
            'success' => true,
            'refund_id' => 'REF-' . uniqid()
        ];
    }

    private function handleStripeWebhook(array $payload): void
    {
        // Handle Stripe webhooks
        $type = $payload['type'] ?? null;
        
        switch ($type) {
            case 'payment_intent.succeeded':
                $this->handlePaymentSuccess($payload);
                break;
            case 'payment_intent.payment_failed':
                $this->handlePaymentFailure($payload);
                break;
        }
    }

    private function handlePaypalWebhook(array $payload): void
    {
        // Handle PayPal webhooks
        $eventType = $payload['event_type'] ?? null;
        
        switch ($eventType) {
            case 'PAYMENT.CAPTURE.COMPLETED':
                $this->handlePaymentSuccess($payload);
                break;
            case 'PAYMENT.CAPTURE.DENIED':
                $this->handlePaymentFailure($payload);
                break;
        }
    }

    private function handlePaymentSuccess(array $payload): void
    {
        // Update payment status to completed
        $transactionId = $payload['data']['object']['id'] ?? null;
        
        if ($transactionId) {
            Payment::where('transaction_id', $transactionId)
                   ->update([
                       'status' => 'completed',
                       'paid_at' => now()
                   ]);
        }
    }

    private function handlePaymentFailure(array $payload): void
    {
        // Update payment status to failed
        $transactionId = $payload['data']['object']['id'] ?? null;
        
        if ($transactionId) {
            Payment::where('transaction_id', $transactionId)
                   ->update(['status' => 'failed']);
        }
    }

    public function getPaymentStats(): array
    {
        return [
            'total_payments' => Payment::count(),
            'successful_payments' => Payment::where('status', 'completed')->count(),
            'failed_payments' => Payment::where('status', 'failed')->count(),
            'total_revenue' => Payment::where('status', 'completed')->sum('amount'),
            'pending_payments' => Payment::where('status', 'pending')->count(),
        ];
    }
}
