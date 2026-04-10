<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;
use Random\RandomException;

class Payment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'entity',
        'account_id',
        'event',
        'payment_id',
        'amount',
        'currency',
        'status',
        'order_id',
        'invoice_id',
        'international',
        'method',
        'amount_refunded',
        'payment_object',
        'refund_status',
        'description',
        'card_id',
        'bank',
        'wallet',
        'vpa',
        'email',
        'contact',
        'notes',
        'fee',
        'tax',
        'error_code',
        'error_description',
        'error_source',
        'error_step',
        'error_reason',
        'acquirer_data',
        'razorpay_created_at',
        'user_id',
        'subscription_plan_id',
        'subscription_current_start',
        'subscription_current_end',
        'subscription_object',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'payment_object' => 'array',
        'notes' => 'array',
        'acquirer_data' => 'array',
        'subscription_object' => 'array',
        'international' => 'boolean',
        'razorpay_created_at' => 'datetime',
    ];


    /**
     * Create or update a payment from a Razorpay webhook payload
     *
     * @param array $payloadData
     * @return \App\Models\Payment
     * @throws RandomException
     */
    public static function createFromRazorpayWebhook(array $payload): self
    {
        // هل ده Webhook Event من Razorpay؟
        $isEvent = ($payload['entity'] ?? null) === 'event';

        // استخرج معلومات عامة
        $accountId = $isEvent ? ($payload['account_id'] ?? null) : ($payload['account_id'] ?? null);
        $eventName = $isEvent ? ($payload['event'] ?? null) : ($payload['event'] ?? null);

        // الكيانات المحتملة داخل الحدث
        $paymentEntity = $isEvent ? ($payload['payload']['payment']['entity'] ?? null) : ($payload['payment'] ?? ($payload['entity'] ?? null));
        $subscriptionEntity = $isEvent ? ($payload['payload']['subscription']['entity'] ?? null) : ($payload['subscription'] ?? null);

        // اسم الكيان اللي هنخزّنه
        $storedEntity = $paymentEntity['entity'] ?? ($subscriptionEntity['entity'] ?? ($payload['entity'] ?? null));

        // --- تطبيع الحقول الخاصة بالـ payment ---
        // payment_id: في payment عادي اسمه id، في subscription_payment اسمه payment_id
        $paymentId = null;
        if (is_array($paymentEntity)) {
            $paymentId = $paymentEntity['id'] ?? $paymentEntity['payment_id'] ?? null;
        }

        // amount / currency / method ... لو فيه paymentEntity هتتبني منه
        $amount = is_array($paymentEntity) ? ($paymentEntity['amount'] ?? null) : null;
        $currency = is_array($paymentEntity) ? ($paymentEntity['currency'] ?? null) : null;
        $status = is_array($paymentEntity) ? ($paymentEntity['status'] ?? null) : null;
        $orderId = is_array($paymentEntity) ? ($paymentEntity['order_id'] ?? null) : null;
        $invoiceId = is_array($paymentEntity) ? ($paymentEntity['invoice_id'] ?? null) : null;
        $international = is_array($paymentEntity) ? ($paymentEntity['international'] ?? false) : false;
        $method = is_array($paymentEntity) ? ($paymentEntity['method'] ?? null) : null;
        $amountRefunded = is_array($paymentEntity) ? ($paymentEntity['amount_refunded'] ?? 0) : 0;
        $refundStatus = is_array($paymentEntity) ? ($paymentEntity['refund_status'] ?? null) : null;
        $description = is_array($paymentEntity) ? ($paymentEntity['description'] ?? null) : null;
        $cardId = is_array($paymentEntity) ? ($paymentEntity['card_id'] ?? null) : null;
        $bank = is_array($paymentEntity) ? ($paymentEntity['bank'] ?? null) : null;
        $wallet = is_array($paymentEntity) ? ($paymentEntity['wallet'] ?? null) : null;
        $vpa = is_array($paymentEntity) ? ($paymentEntity['vpa'] ?? null) : null;
        $email = is_array($paymentEntity) ? ($paymentEntity['email'] ?? null) : null;
        $contact = is_array($paymentEntity) ? ($paymentEntity['contact'] ?? null) : null;
        $notes = is_array($paymentEntity) ? ($paymentEntity['notes'] ?? null) : null;
        $fee = is_array($paymentEntity) ? ($paymentEntity['fee'] ?? null) : null;
        $tax = is_array($paymentEntity) ? ($paymentEntity['tax'] ?? null) : null;
        $errorCode = is_array($paymentEntity) ? ($paymentEntity['error_code'] ?? null) : null;
        $errorDesc = is_array($paymentEntity) ? ($paymentEntity['error_description'] ?? null) : null;
        $acquirerData = is_array($paymentEntity) ? (!empty($paymentEntity['acquirer_data']) ? $paymentEntity['acquirer_data'] : null) : null;

        // created_at: لو موجود تحت payment؛ وإلا من event
        $rpCreatedAt = null;
        if (is_array($paymentEntity) && isset($paymentEntity['created_at'])) {
            $rpCreatedAt = date('Y-m-d H:i:s', (int)$paymentEntity['created_at']);
        } elseif ($isEvent && isset($payload['created_at'])) {
            $rpCreatedAt = date('Y-m-d H:i:s', (int)$payload['created_at']);
        }

        // --- تطبيع الاشتراك ---
        $subscriptionObject = $subscriptionEntity ?: [];
        $subscriptionPlanId = $subscriptionEntity['plan_id'] ?? null;
        $subscriptionCurrentStart = $subscriptionEntity['current_start'] ?? null;
        $subscriptionCurrentEnd = $subscriptionEntity['current_end'] ?? null;
        $userIdFromNotes = $subscriptionEntity['notes']['user_id'] ?? null;

        // --- قيم افتراضية آمنة لو مفيش payment أصلاً (زي subscription.authenticated) ---
        // لتفادي NOT NULL: حط amount=0 ، واعمل payment_id صناعي قابل للتتبع
        if ($amount === null) {
            $amount = 0;
        }
        if ($paymentId === null) {
            $suffix = $subscriptionEntity['id'] ?? ($eventName ?? 'unknown');
            $paymentId = 'evt_' . preg_replace('/[^a-zA-Z0-9_]/', '', (string)$suffix);
        }
        if ($currency === null) {
            // Razorpay الافتراضي الشائع INR لو مش معروف
            $currency = 'INR';
        }
        if ($status === null) {
            $status = $isEvent ? 'event' : 'created';
        }
        if ($description === null) {
            $description = $isEvent ? 'Razorpay Event' : 'Razorpay Payment';
        }

        // خزّن الـpayload الخام لـ debugging
        $paymentObject = $isEvent ? $payload : ($paymentEntity ?: $payload);

        return self::create([
            'entity' => $storedEntity,
            'account_id' => $accountId ?? 'unknown',
            'event' => $eventName ?? ($storedEntity ?: 'unknown'),

            'subscription_object' => $subscriptionObject,
            'subscription_plan_id' => $subscriptionPlanId,
            'subscription_current_start' => $subscriptionCurrentStart,
            'subscription_current_end' => $subscriptionCurrentEnd,

            'payment_object' => $paymentObject,

            // قيم الدفع بعد التطبيع/الافتراض
            'amount' => $amount,
            'payment_id' => $paymentId,
            'currency' => $currency,
            'status' => $status,
            'order_id' => $orderId,
            'invoice_id' => $invoiceId,
            'international' => (bool)$international,
            'method' => $method ?? 'card',
            'amount_refunded' => (int)$amountRefunded,
            'refund_status' => $refundStatus ?? 'none',
            'description' => $description,
            'card_id' => $cardId,
            'bank' => $bank ?? 'Razorpay',
            'wallet' => $wallet ?? 'Razorpay',
            'vpa' => $vpa ?? 'Razorpay',
            'email' => $email ?? 'Razorpay',
            'contact' => $contact ?? 'Razorpay',
            'notes' => $notes,
            'fee' => $fee,
            'tax' => $tax,
            'error_code' => $errorCode,
            'error_description' => $errorDesc,
            'acquirer_data' => $acquirerData,
            'razorpay_created_at' => $rpCreatedAt,

            'user_id' => $userIdFromNotes,
        ]);
    }

}
