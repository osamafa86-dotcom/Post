<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Services\Payment\Contract\PaymentGatewayContract;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Razorpay\Api\Errors\SignatureVerificationError;
use App\Models\Subscription;
use Illuminate\Support\Facades\Cache;

class RazorpayController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentGatewayContract $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Verify payment after checkout completion
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function verifyPayment(Request $request): JsonResponse
    {
        $input = $request->all();

        try {
            // Get the order and payment IDs
            $razorpayPaymentId = $input['razorpay_payment_id'];
            $razorpayOrderId = $input['razorpay_order_id'];
            $razorpaySignature = $input['razorpay_signature'];

            // Get the Razorpay API instance from the payment service
            $api = $this->paymentService->getApi();

            // Verify the signature
            $attributes = [
                'razorpay_order_id' => $razorpayOrderId,
                'razorpay_payment_id' => $razorpayPaymentId,
                'razorpay_signature' => $razorpaySignature
            ];

            $api->utility->verifyPaymentSignature($attributes);

            // Verify the payment status
            $this->paymentService->verifyPayment($razorpayPaymentId);

            return response()->json([
                'success' => true,
                'message' => 'Payment verified successfully'
            ]);
        } catch (SignatureVerificationError $e) {
            // Log the error
            Log::error('Razorpay Signature Verification Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Payment verification failed: Invalid signature'
            ], 400);
        } catch (\Exception $e) {
            // Log the error
            Log::error('Razorpay Payment Verification Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Payment verification failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle successful payment redirect
     *
     * @return \Illuminate\View\View
     */
    public function paymentSuccess()
    {
        return view('payment.success');
    }

    /**
     * Verify subscription after checkout completion
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function verifySubscription(Request $request)
    {
        $input = $request->all();

        try {
            // Get the subscription and payment IDs
            $razorpayPaymentId = $input['razorpay_payment_id'];
            $razorpaySubscriptionId = $input['razorpay_subscription_id'];
            $razorpaySignature = $input['razorpay_signature'];

            // Get the Razorpay API instance from the payment service
            $api = $this->paymentService->getApi();

            // Verify the signature
            $attributes = [
                'razorpay_subscription_id' => $razorpaySubscriptionId,
                'razorpay_payment_id' => $razorpayPaymentId,
                'razorpay_signature' => $razorpaySignature
            ];

            $api->utility->verifyPaymentSignature($attributes);

            // Get subscription details
            $subscription = $api->subscription->fetch($razorpaySubscriptionId);
            $plan = $api->plan->fetch($subscription->plan_id);
            // Store subscription payment details
            $paymentData = [
                'entity' => 'subscription_payment',
                'payment_id' => $razorpayPaymentId ?? null,
                'subscription_id' => $razorpaySubscriptionId ?? null,
                'status' => 'authorized',
                'amount' => $plan?->item?->amount ?? null,
                'currency' => $plan?->item?->currency ?? null,
                'method' => $subscription?->payment_method ?? null,
                'description' => 'Subscription payment',
                'order_id' => null,
                'refund_status' => null,
                'amount_refunded' => 0,
                'user_id' => auth('web_user')->check() ? auth('web_user')->id() : null
            ];

            // Create or update a payment from subscription data
            Payment::createFromRazorpayWebhook($paymentData);

            if (auth('web_user')->check()) {
                Subscription::activateForUser(
                    userId:        auth('web_user')->id(),
                    planPeriod:    $subscription->notes['plan_period'] ?? ($plan?->period ?? 'monthly'),
                    paidAtUnix:    $subscription->current_start ?? time(),
                    gatewayMethod: $subscription->payment_method ?? 'card',
                    amountMinor:   $plan?->item?->amount,
                    useExactCalendar: false
                );
            }

            $dashboardUrl = route('main.maktoob.subscription_success');

            if ($request->ajax() || $request->expectsJson() || $request->wantsJson()
                || str_contains((string)$request->header('Accept'), 'application/json')) {
                return response()->json([
                    'success' => true,
                    'redirect_url' => $dashboardUrl,
                    'message' => 'Subscription verified successfully',
                ]);
            }

            // ✅ غير ذلك: Redirect عادي
            return redirect()->intended($dashboardUrl)
                ->with('success', 'Subscription verified successfully');

        } catch (SignatureVerificationError $e) {
            // Log the error
            Log::error('Razorpay Signature Verification Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Subscription verification failed: Invalid signature'
            ], 400);
        } catch (\Exception $e) {
            // Log the error
            Log::error('Razorpay Subscription Verification Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Subscription verification failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle successful subscription redirect
     *
     * @return \Illuminate\View\View
     */
    public function subscriptionSuccess()
    {
        return view('payment.subscription-success');
    }

    /**
     * Process webhook from Razorpay
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function webhook(Request $request): JsonResponse
    {
        $webhookSecret = config('services.razorpay.webhook_secret');

        try {
            // -------- Verify Signature --------
            $payload   = $request->getContent();
            $signature = $request->header('X-Razorpay-Signature');
            $computed  = hash_hmac('sha256', $payload, $webhookSecret);
            if ($computed !== $signature) {
                throw new SignatureVerificationError('Invalid webhook signature');
            }

            // -------- Idempotency (تجنب التكرار) --------
            $eventId = $request->header('X-Razorpay-Event-Id') ?? sha1($payload);
            $cacheKey = 'rzp_evt_' . $eventId;
            if (! Cache::add($cacheKey, 1, now()->addMinutes(10))) {
                // حدث مُعالج من قبل
                return response()->json(['success' => true, 'message' => 'Duplicate webhook ignored']);
            }

            // -------- Parse --------
            $data      = json_decode($payload, true);
            $eventType = $data['event'] ?? '';

            Log::info('Razorpay Webhook Event: ' . $eventType);

            // user_id من ملاحظات الاشتراك إن وُجد
            $userId = $data['payload']['subscription']['entity']['notes']['user_id']
                ?? $data['user_id'] // لو أضفته سابقًا
                ?? null;

            // احفظ/حدّث سجل الدفع (الدالة عندك أصبحت مطبّعة وتحمي من nulls)
            Payment::createFromRazorpayWebhook($data);

            // -------- تفعيل الاشتراك فقط عند الدفع المؤكد --------
            if (in_array($eventType, ['payment.captured', 'order.paid'], true)) {
                $api      = $this->paymentService->getApi();
                $paymentE = $data['payload']['payment']['entity'] ?? null;
                $subE     = $data['payload']['subscription']['entity'] ?? null;

                // لو مفيش subscription في الـpayload، جرّب تجيبه من payment->subscription_id
                if (! $subE && is_array($paymentE) && !empty($paymentE['subscription_id'])) {
                    try {
                        $subObj = $api->subscription->fetch($paymentE['subscription_id']);
                        $subE = $subObj?->toArray() ?? null;
                        // جرّب تجيب الخطة عشان الـamount/period
                        $planObj = $subE && !empty($subE['plan_id']) ? $api->plan->fetch($subE['plan_id']) : null;
                        $planArr = $planObj?->toArray() ?? null;
                    } catch (\Throwable $e) {
                        Log::warning('Failed to fetch subscription/plan for activation: ' . $e->getMessage());
                        $planArr = null;
                    }
                } else {
                    // جِهّز planArr لو متاح في ذاك السياق
                    $planArr = null;
                    try {
                        if ($subE && !empty($subE['plan_id'])) {
                            $planObj = $api->plan->fetch($subE['plan_id']);
                            $planArr = $planObj?->toArray() ?? null;
                        }
                    } catch (\Throwable $e) {
                        $planArr = null;
                    }
                }

                // استنتاج المعطيات للتفعيل
                $paidAtUnix  = $paymentE['created_at'] ?? ($data['created_at'] ?? time());
                $planPeriod  = $subE['notes']['plan_period']
                    ?? ($planArr['period'] ?? 'monthly'); // monthly/yearly
                $payMethod   = $subE['payment_method'] ?? ($paymentE['method'] ?? 'card');
                $amountMinor = $paymentE['amount']
                    ?? ($planArr['item']['amount'] ?? null);

                // user_id: لو لسه null، جرّبه من sub.notes بعد fetch
                if (! $userId && $subE && !empty($subE['notes']['user_id'])) {
                    $userId = (int)$subE['notes']['user_id'];
                }

                if ($userId) {
                    Subscription::activateForUser(
                        userId:        (int)$userId,
                        planPeriod:    $planPeriod,
                        paidAtUnix:    (int)$paidAtUnix,
                        gatewayMethod: $payMethod,
                        amountMinor:   $amountMinor,
                        // false = 30/365 يوم (حسب طلبك)
                        useExactCalendar: false
                    );
                } else {
                    Log::warning('Webhook captured/paid without user_id — subscription activation skipped.');
                }
            }

            return response()->json(['success' => true, 'message' => 'Webhook processed successfully']);
        } catch (SignatureVerificationError $e) {
            Log::error('Razorpay Signature Verification Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Webhook verification failed: Invalid signature'
            ], 400);
        } catch (\Exception $e) {
            Log::error('Razorpay Webhook Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to process webhook: ' . $e->getMessage()
            ], 500);
        }
    }
}
