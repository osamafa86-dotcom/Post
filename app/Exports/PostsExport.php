<?php

namespace App\Exports;

use App\Models\Post;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PostsExport implements FromQuery,  WithMapping, WithHeadings
{

    public function __construct()
    {

    }

    /**
     * @return object
     */
//    public function collection(): object
//    {
//        return $this->data;
//    }

    public function query()
    {

        return Post::query();
    }

    public function map($item): array
    {

        $namesString = implode(',', $item->tags?->pluck('tag_name')->toArray());

        $data = [
            $item->publish_date,
            $item->title,
            $item->slug,
            $item->image,
            $item->image_alt,
            $item->description,
            $item->body,
            $item->category?->category_title,
            $namesString,
            $item->order_number,
            '1'
        ];

        return $data;

    }


    public function headings(): array
    {


        $headings = [
            'publish date',
            'title',
            'slug',
            'image',
            'image_alt',
            'description' ,
            'body' ,
            'category',
            'tags',
            'order number',
            'user_id'
        ];

        return $headings;

    }
}
