<?php

namespace App\Exports;

use App\Enums\ActionsEnum;
use App\Enums\LoggableModelsEnum;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Carbon\Carbon;

class UserLogsExport implements FromCollection, WithMapping, WithHeadings
{
    public $items;
    public array $models;
    public $model;
    public array|string $model_title; // 👈 صارت تقبل مصفوفة أو نص
    public string $export_lang;

    public function __construct($items, $model = null, $model_title = [], $export_lang = 'en')
    {
        $this->items = $items;
        $this->models = $this->mapEnumToArray(); // تحويل enum → array
        $this->model = $model;
        $this->model_title = $model_title;
        $this->export_lang = $export_lang;
    }

    private function mapEnumToArray(): array
    {
        return collect(LoggableModelsEnum::cases())
            ->mapWithKeys(fn($case) => [$case->value => $case->label()])
            ->toArray();
    }

    public function map($item): array
    {
        $data = [
            $item->user?->full_name,
            array_key_exists($item->actionable_type, $this->models)
                ? $this->models[$item->actionable_type] . "(" . $item->actionable_id . ")"
                : '',
            ActionsEnum::fromValue($item->action_status) ?? '',
            Carbon::parse($item->created_at)->format('Y-m-d H:i'),
        ];

        $modelBeforeData = json_decode($item?->actionable_data_before);
        $modelAfterData = json_decode($item?->actionable_data_after);

        if ($this->model && $this->model !== 'all' && $modelBeforeData && $modelAfterData) {
            if (is_array($this->model_title) && array_key_exists($this->model, $this->model_title)) {
                foreach ($this->model_title[$this->model] as $db_title => $ar_title) {
                    $data[] = $modelBeforeData->{$db_title} ?? 'لايوجد';
                }
                foreach ($this->model_title[$this->model] as $db_title => $ar_title) {
                    $data[] = $modelAfterData->{$db_title} ?? 'لايوجد';
                }
            }
        }

        return $data;
    }

    public function headings(): array
    {
        if ($this->export_lang === 'ar') {
            $headings = ['المستخدم', 'القائمة او المكان', 'العملية', 'تاريخ التعديل'];
        } else {
            $headings = ['User', 'Location', 'Operation', 'Action Date'];
        }

        if ($this->model && $this->model !== 'all' && is_array($this->model_title) && array_key_exists($this->model, $this->model_title)) {
            foreach ($this->model_title[$this->model] as $db_title => $ar_title) {
                $headings[] = $this->export_lang === 'ar'
                    ? $ar_title . ' قبل التعديل '
                    : $ar_title . ' before edit ';
            }
            foreach ($this->model_title[$this->model] as $db_title => $ar_title) {
                $headings[] = $this->export_lang === 'ar'
                    ? $ar_title . ' بعد التعديل '
                    : $ar_title . ' after edit ';
            }
        }

        return $headings;
    }

    public function collection()
    {
        return $this->items;
    }
}
