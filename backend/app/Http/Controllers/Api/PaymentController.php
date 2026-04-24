<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Order;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PaymentController extends Controller
{
    public function __construct(private PaymentService $paymentService)
    {
    }

    public function process(Request $request, Order $order): JsonResponse
    {
        $this->authorize('update', $order);

        if ($order->payment_status === 'paid') {
            return response()->json([
                'message' => 'Order is already paid'
            ], 422);
        }

        $validated = $request->validate([
            'payment_method' => 'required|string',
            'payment_details' => 'required|array'
        ]);

        try {
            $payment = $this->paymentService->processPayment(
                $order,
                $validated['payment_method'],
                $validated['payment_details']
            );

            return response()->json($payment);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Payment failed',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    public function webhook(Request $request, string $provider): JsonResponse
    {
        try {
            $this->paymentService->handleWebhook($provider, $request->all());
            
            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Webhook processing failed',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    public function show(Payment $payment): JsonResponse
    {
        $this->authorize('view', $payment);
        
        $payment->load('order');

        return response()->json($payment);
    }

    public function refund(Request $request, Payment $payment): JsonResponse
    {
        $this->authorize('refund', $payment);

        if ($payment->status !== 'completed') {
            return response()->json([
                'message' => 'Payment cannot be refunded'
            ], 422);
        }

        $validated = $request->validate([
            'amount' => 'sometimes|numeric|min:0.01|max:' . $payment->amount,
            'reason' => 'required|string'
        ]);

        try {
            $refund = $this->paymentService->processRefund(
                $payment,
                $validated['amount'] ?? $payment->amount,
                $validated['reason']
            );

            return response()->json($refund);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Refund failed',
                'error' => $e->getMessage()
            ], 422);
        }
    }
}
