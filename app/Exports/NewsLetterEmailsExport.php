<?php

namespace App\Exports;


use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Carbon\Carbon;

class NewsLetterEmailsExport implements FromCollection, WithMapping, WithHeadings
{

    public $items;

    public function __construct($items)
    {
        $this->items = $items;


    }


    public function map($item): array
    {

        $data = [
            $item->email,

        ];


        return $data;

    }


    public function headings(): array
    {


        $headings = [
            'Email',

        ];

        return $headings;

    }

    public function collection()
    {

        return $this->items;
    }
}
