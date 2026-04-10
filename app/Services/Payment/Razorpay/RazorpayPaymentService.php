<?php

namespace App\Services\Payment\Razorpay;

use App\Models\Payment;
use App\Models\Plan;
use App\Services\Payment\Contract\PaymentGatewayContract;
use Illuminate\Config\Repository;
use Illuminate\Foundation\Application;
use Razorpay\Api\Api;

class RazorpayPaymentService implements PaymentGatewayContract
{

    /**
     * @var Repository|\Illuminate\Contracts\Foundation\Application|Application|mixed
     */
    private mixed $razorpaySecret;
    /**
     * @var Repository|\Illuminate\Contracts\Foundation\Application|Application|mixed
     */
    private mixed $razorpayKey;
    private Api $api;

    public function __construct()
    {
        $this->razorpayKey = config('services.razorpay.key');
        $this->razorpaySecret = config('services.razorpay.secret');
        $this->api = new Api($this->razorpayKey, $this->razorpaySecret);
    }

    public function fetchPlans()
    {
        return $this->api->plan->all();
    }
    /**
     * @param array $data
     *
     * @return bool
     */
    public function storePayment(array $data): bool
    {
        // Create or update a payment from a Razorpay webhook payload
        return (bool) Payment::createFromRazorpayWebhook($data);
    }

    /**
     * @param string $paymentId
     * @return bool
     */
    public function verifyPayment(string $paymentId): bool
    {
        // When the payment status is changed to captured , the payment is verified as complete by Razorpay . The amount is settled to your account as per the settlement schedule.

        $payment = $this->api->payment->fetch($paymentId);

        Payment::where('payment_id', $paymentId)->update(['status' => $payment->status]);

        return true;

    }

    /**
     * Create a payment order for a plan
     *
     * @param string $planId The plan ID
     * @param array $customerInfo Customer information (name, email, contact)
     * @return array Order details including order_id and other metadata
     */
    public function createOrderForPlan(string $planId, array $customerInfo = []): array
    {
        // Get plan details
        $plan = Plan::where('plan_id', $planId)->firstOrFail();
        
        // Create order payload
        $orderPayload = [
            'amount' => (int) $plan->item_amount,
            'currency' => $plan->item_currency,
            'receipt' => 'order_' . uniqid(),
            'notes' => [
                'plan_id' => $planId,
                'plan_name' => $plan->item_name,
                'plan_period' => $plan->period,
                'customer_name' => $customerInfo['name'] ?? '',
                'customer_email' => $customerInfo['email'] ?? '',
                'customer_contact' => $customerInfo['contact'] ?? '',
            ]
        ];
        
        // Create order
        $order = $this->api->order->create($orderPayload);
        
        return [
            'order_id' => $order->id,
            'amount' => $order->amount / 100, // Convert from paise/cents to actual currency
            'currency' => $order->currency,
            'plan' => $plan,
            'razorpay_key' => $this->razorpayKey
        ];
    }
    
    /**
     * Create a subscription for a plan
     *
     * @param string $planId The plan ID from Razorpay
     * @param int|null $userId The user ID (if authenticated)
     * @return array Subscription details including subscription_id and other metadata
     */
    public function createSubscription(string $planId, ?int $userId = null): array
    {
        // Get plan details
        $plan = Plan::where('plan_id', $planId)->firstOrFail();
        
        // Create subscription payload
        $subscriptionPayload = [
            'plan_id' => $planId,
            'total_count' => 12, // Number of billing cycles (default to 12 months)
            'customer_notify' => 1, // Notify customer about subscription charges
            'quantity' => 1,
            'notes' => [
                'plan_name' => $plan->item_name,
                'plan_period' => $plan->period,
                'user_id' => $userId,
            ]
        ];
        
        // Create subscription
        $subscription = $this->api->subscription->create($subscriptionPayload);
        
        return [
            'subscription_id' => $subscription->id,
            'plan_id' => $subscription->plan_id,
            'status' => $subscription->status,
            'current_start' => $subscription->current_start,
            'current_end' => $subscription->current_end,
            'plan' => $plan,
            'razorpay_key' => $this->razorpayKey,
            'user_id' => $userId,
        ];
    }
    
    /**
     * Cancel a subscription
     *
     * @param string $subscriptionId
     * @param bool $cancelAtCycleEnd Whether to cancel immediately or at cycle end
     * @return array
     */
    public function cancelSubscription(string $subscriptionId, bool $cancelAtCycleEnd = true): array
    {
        $subscription = $this->api->subscription->fetch($subscriptionId);
        
        if ($cancelAtCycleEnd) {
            $subscription = $subscription->cancel(true);
        } else {
            $subscription = $subscription->cancel(false);
        }
        
        return [
            'subscription_id' => $subscription->id,
            'status' => $subscription->status,
            'ended_at' => $subscription->ended_at
        ];
    }
    
    /**
     * Get the Razorpay API instance
     *
     * @return Api
     */
    public function getApi(): Api
    {
        return $this->api;
    }
}
