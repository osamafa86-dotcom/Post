<?php

namespace App\Imports;

use App\Enums\SubscriptionPaymentMethodEnum;
use App\Enums\SubscriptionStatusEnum;
use App\Enums\SubscriptionTypeEnum;
use App\Enums\UserStatusEnum;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class SubscriptionImport implements ToCollection, WithHeadingRow, WithCalculatedFormulas
{
    public function collection(Collection $rows)
    {
        Log::info('Starting subscription import with ' . $rows->count() . ' rows');

        if ($rows->isEmpty()) {
            throw new \Exception('The uploaded file is empty or has no valid data.');
        }

        $imported = 0;
        $errors = [];

        foreach ($rows as $index => $row) {
            try {
                // Skip empty rows
                if ($this->isEmptyRow($row)) {
                    continue;
                }

                // 1. Get/Create User logic
                // Email from 'SUBSCRIPTION ID- EMAIL' (slug: subscription_id_email)
                $email = isset($row['subscription_id_email']) ? trim($row['subscription_id_email']) : null;

                // Subscriber Name from 'SUBSCRIBER' (slug: subscriber)
                $subscriberName = isset($row['subscriber']) ? trim($row['subscriber']) : '';

                if (empty($email)) {
                    $errors[] = "Row {$index}: Missing email in 'SUBSCRIPTION ID- EMAIL' column.";
                    continue;
                }

                // Name Parsing:
                // first_name -> first word before the space
                // last_name -> remaining text after the first space
                $parts = explode(' ', $subscriberName, 2);
                $firstName = $parts[0] ?? '';
                $lastName = $parts[1] ?? '';

                // User Creation/Update with transaction to ensure user details are created
                $user = DB::transaction(function () use ($email, $firstName, $lastName) {
                    // Create or update user
                    $user = User::updateOrCreate(
                        ['email' => $email],
                        [
                            'first_name' => $firstName,
                            'last_name'  => $lastName,
                            'password'   => Hash::make('12345678'),
                            'user_status' => UserStatusEnum::SUBSCRIBER->value,
                        ]
                    );

                    // Create or update user details with is_Active = 1
                    $user->details()->updateOrCreate(
                        ['user_id' => $user->id],
                        ['is_Active' => 1]
                    );

                    return $user;
                });

                // 2. Subscriptions Creation logic

                $start = $this->parseDate($row['subscription_start'] ?? null);
                $end   = $this->parseDate($row['subscription_end'] ?? null);

                if (!$start || !$end) {
                    $rawStart = $row['subscription_start'] ?? 'N/A';
                    $rawEnd = $row['subscription_end'] ?? 'N/A';
                    $errors[] = "Row {$index}: Invalid start ('{$rawStart}') or end ('{$rawEnd}') date. Supported formats: Y-m-d, d/m/Y, m/d/Y, or Excel Serial Date.";
                    continue;
                }

                // Determine type based on dates (monthly or yearly)
                $type = $this->calculateSubscriptionType($start, $end);

                // Determine status based on dates
                $status = $this->calculateSubscriptionStatus($end);

                // Parse amount
                $amount = $this->parseAmount($row['subscription_amount'] ?? null);

                // Handle payment method - default to CASH if null or empty
                $paymentVal = $row['payment_method'] ?? null;
                $paymentMethod = $this->resolveEnum(SubscriptionPaymentMethodEnum::class, $paymentVal);
                if (!$paymentMethod) {
                    $paymentMethod = SubscriptionPaymentMethodEnum::CASH;
                }

                // Create Subscription
                Subscription::create([
                    'subscriber_id'      => $user->id,
                    'subscription_start' => $start,
                    'subscription_end'   => $end,
                    'type'               => $type->value,
                    'status'             => $status->value,
                    'amount'             => $amount,
                    'payment_method'     => $paymentMethod->value,
                ]);

                $imported++;

            } catch (\Exception $e) {
                $errors[] = "Row {$index}: " . $e->getMessage();
                Log::error("Row {$index} import error: " . $e->getMessage());
            }
        }

        if ($imported === 0 && !empty($errors)) {
            throw new \Exception("No subscriptions imported. First error: " . ($errors[0] ?? 'Unknown'));
        }

        if (!empty($errors)) {
            session()->flash('warning',
                "Imported {$imported} successfully. Some errors occurred: " .
                implode(' | ', array_slice($errors, 0, 5))
            );
        } else {
            session()->flash('success', "Successfully imported {$imported} subscriptions.");
        }
    }
    private function calculateSubscriptionType($start, $end): SubscriptionTypeEnum
    {
        $startDate = Carbon::parse($start);
        $endDate = Carbon::parse($end);

        $diffInYears = $startDate->diffInYears($endDate);

        return $diffInYears < 1 ? SubscriptionTypeEnum::MONTHLY : SubscriptionTypeEnum::YEARLY;
    }

    /**
     * Calculate subscription status based on end date
     */
    private function calculateSubscriptionStatus($end): SubscriptionStatusEnum
    {
        $endDate = Carbon::parse($end);
        $today = Carbon::now();

        return $endDate->isFuture() ? SubscriptionStatusEnum::ACTIVE : SubscriptionStatusEnum::INACTIVE;
    }
    private function isEmptyRow($row): bool
    {
        foreach ($row as $value) {
            if (!empty($value) && $value !== null) {
                return false;
            }
        }
        return true;
    }

    private function resolveEnum(string $enumClass, mixed $value)
    {
        if (empty($value)) {
            return null;
        }
        $value = trim((string)$value);

        // 1. Try generic tryFrom (int)
        if (is_numeric($value)) {
            $fromVal = $enumClass::tryFrom((int)$value);
            if ($fromVal) return $fromVal;
        }

        // 2. Try matching case name (case-insensitive)
        foreach ($enumClass::cases() as $case) {
            if (strcasecmp($case->name, $value) === 0) {
                return $case;
            }
        }

        // 3. Try matching label (if available)
        if (method_exists($enumClass, 'tryFromLabel')) {
            $fromLabel = $enumClass::tryFromLabel($value);
            if ($fromLabel) return $fromLabel;
        }

        // 4. Try matching label via manual iteration if tryFromLabel doesn't exist or failed
        // (Just in case tryFromLabel isn't implemented on all enums)
        foreach ($enumClass::cases() as $case) {
            if (method_exists($case, 'label') && strcasecmp($case->label(), $value) === 0) {
                return $case;
            }
        }

        return null;
    }

    private function parseDate($value): ?string
    {
        if (empty($value)) return null;

        // If the value is a formula that failed to calculate (starts with =), we can't parse it.
        if (is_string($value) && str_starts_with($value, '=')) {
            return null;
        }

        $value = trim((string)$value);

        try {
            // 1. Excel Serial Date (Numeric)
            // Ensure we handle numeric strings like "45134"
            if (is_numeric($value)) {
                return Date::excelToDateTimeObject((float)$value)->format('Y-m-d');
            }

            // 2. Standard Carbon Parse
            try {
                return Carbon::parse($value)->format('Y-m-d');
            } catch (\Exception $ignore) {}

            // 3. Try Explicit Formats
            $formats = [
                'd/m/Y', 'd-m-Y', 'm/d/Y', 'Y/m/d', 'm-d-Y',
                'd-m-Y H:i', 'm-d-Y H:i', 'Y-m-d H:i',
                'd/m/Y H:i', 'm/d/Y H:i', 'Y/m/d H:i',
                'd-m-Y H:i:s', 'm-d-Y H:i:s', 'Y-m-d H:i:s',
                'Y-m-d', 'Y/m/d'
            ];

            foreach ($formats as $format) {
                try {
                    return Carbon::createFromFormat($format, $value)->format('Y-m-d');
                } catch (\Exception $ignore) {}
            }

            return null;

        } catch (\Exception $e) {
            // Log the specific error for debug
            Log::warning("Date parse failed for value '{$value}': " . $e->getMessage());
            return null;
        }
    }

    private function parseAmount($value): float
    {
        if (empty($value)) return 0.0;
        $clean = preg_replace('/[^\d\.\-]/', '', (string)$value);
        return (float)$clean;
    }
}
