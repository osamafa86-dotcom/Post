<?php

namespace App\Models;

use App\Enums\SubscriptionPaymentMethodEnum;
use App\Enums\SubscriptionStatusEnum;
use App\Enums\SubscriptionTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Carbon;

class Subscription extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $casts = [
      'subscription_start'    =>  'date',
      'subscription_end'    =>  'date',
//        'type' => SubscriptionTypeEnum::class,
    ];
    public function user_logs(): MorphMany
    {
        return $this->morphMany(UserLog::class, 'actionable');
    }

    public function subscriber(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * فعّل/جدّد اشتراك مستخدم.
     *
     * @param  int         $userId
     * @param  'monthly'|'yearly' $planPeriod
     * @param  int|null    $paidAtUnix   وقت الدفع (unix). إن لم يُمرّر → now()
     * @param  string|null $gatewayMethod مثل 'card' | 'paypal'
     * @param  int|float|null $amountMinor  المبلغ بأصغر وحدة من Razorpay (10000 = 100.00)
     * @param  bool        $useExactCalendar لو true: من تاريخ البداية إلى نفس التاريخ الشهر/السنة القادمة ناقص يوم
     * @return static
     */
    public static function activateForUser(
        int $userId,
        string $planPeriod,
        ?int $paidAtUnix = null,
        ?string $gatewayMethod = null,
        int|float|null $amountMinor = null,
        bool $useExactCalendar = false
    ): self {
        $paidAt = $paidAtUnix ? Carbon::createFromTimestamp($paidAtUnix) : now();

        // البداية: دائمًا من تاريخ الدفع/اليوم
        $start = (clone $paidAt)->startOfDay();

        // نوع وخانة المدة
        $typeEnum = match ($planPeriod) {
            'monthly' => SubscriptionTypeEnum::MONTHLY,
            'yearly'  => SubscriptionTypeEnum::YEARLY,
            default   => SubscriptionTypeEnum::MONTHLY,
        };

        // نهاية الاشتراك
        if ($useExactCalendar) {
            // “من نفس اليوم إلى نفس اليوم القادم ناقص يوم”
            $end = match ($typeEnum) {
                SubscriptionTypeEnum::MONTHLY => (clone $start)->addMonth()->subDay()->endOfDay(),
                SubscriptionTypeEnum::YEARLY  => (clone $start)->addYear()->subDay()->endOfDay(),
            };
        } else {
            // 30 يوم للشهري / 365 يوم للسنوي
            $days = $typeEnum === SubscriptionTypeEnum::MONTHLY ? 30 : 365;
            $end  = (clone $start)->addDays($days - 1)->endOfDay();
        }

        // طريقة الدفع إلى Enumك
        $methodEnum = match (strtolower((string)$gatewayMethod)) {
            'paypal' => SubscriptionPaymentMethodEnum::PAYPAL,
            default  => SubscriptionPaymentMethodEnum::CREDIT_CARD,
        };

        // تحويل أصغر وحدة إلى قيمة عشرية (/100 لمعظم العملات)
        $amount = is_null($amountMinor) ? null : ((float)$amountMinor / 100);

        // ✅ دائمًا أنشئ سجل جديد
        return static::create([
            'subscriber_id'      => $userId,
            'subscription_start' => $start->toDateString(),
            'subscription_end'   => $end->toDateString(),
            'type'               => $typeEnum->value,
            'status'             => SubscriptionStatusEnum::ACTIVE->value,
            'amount'             => $amount,
            'payment_method'     => (string)$methodEnum->value,
        ]);
    }

}
