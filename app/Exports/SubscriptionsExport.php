<?php

namespace App\Exports;

use App\Models\Subscription;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use App\Enums\SubscriptionTypeEnum;
use App\Enums\SubscriptionStatusEnum;
use App\Enums\SubscriptionPaymentMethodEnum;

class SubscriptionsExport implements FromCollection, WithHeadings, WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Subscription::with('subscriber:id,first_name,last_name,email')
            ->get()
            ->map(function ($item) {
                return [
                    'Subscriber' => $item?->subscriber?->first_name . ' ' . $item?->subscriber?->last_name,
                    'Subscription_id_email' => $item?->subscriber?->email,
                    'Start' => $item?->subscription_start,
                    'End' => $item?->subscription_end,
                    'Type' => $item?->type ? SubscriptionTypeEnum::from($item?->type)->label() : '',
                    'Status' => $item?->status ? SubscriptionStatusEnum::from($item?->status)->label() : '',
                    'Amount' => $item?->amount,
                    'Payment Method' => $item?->payment_method ? SubscriptionPaymentMethodEnum::from($item?->payment_method)->label() : '',
                ];
            });
    }

    public function headings(): array
    {
        return [
            'SUBSCRIBER',
            'SUBSCRIPTION ID- EMAIL',
            'SUBSCRIPTION START',
            'SUBSCRIPTION END',
            'SUBSCRIPTION TYPE',
            'SUBSCRIPTION STATUS',
            'SUBSCRIPTION AMOUNT',
            'PAYMENT_METHOD',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet;
                $rowCount = $sheet->getHighestRow();

                // Helper to add validation
                $addValidation = function($column, $options) use ($sheet, $rowCount) {
                    $validation = $sheet->getCell("{$column}2")->getDataValidation();
                    $validation->setType(DataValidation::TYPE_LIST);
                    $validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
                    $validation->setAllowBlank(false);
                    $validation->setShowInputMessage(true);
                    $validation->setShowErrorMessage(true);
                    $validation->setShowDropDown(true);
                    $validation->setErrorTitle('Input error');
                    $validation->setError('Value is not in list.');
                    $validation->setPromptTitle('Pick from list');
                    $validation->setPrompt('Please start typing to get options.');
                    
                    // Options as comma separated string or explicitly set as formula
                    // Note: Excel validation list length limit is 255 chars usually. 
                    // If enums are long, we might need a separate sheet, but assuming they correspond to simple labels.
                    $optionsString = '"' . implode(',', $options) . '"';
                    $validation->setFormula1($optionsString);

                    // Apply to entire column
                    for ($i = 2; $i <= $rowCount + 100; $i++) { // +100 to allow adding new rows
                        $sheet->getCell("{$column}{$i}")->setDataValidation(clone $validation);
                    }
                };

                // Type Column (E)
                $types = array_map(fn($case) => $case->label(), SubscriptionTypeEnum::cases());
                $addValidation('E', $types);

                // Status Column (F)
                $statuses = array_map(fn($case) => $case->label(), SubscriptionStatusEnum::cases());
                $addValidation('F', $statuses);

                // Payment Method Column (H)
                $paymentMethods = array_map(fn($case) => $case->label(), SubscriptionPaymentMethodEnum::cases());
                $addValidation('H', $paymentMethods);
            },
        ];
    }
}
