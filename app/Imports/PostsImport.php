<?php

namespace App\Imports;

use App\Enums\PublishEnum;
use App\Enums\TagTypeEnum;
use App\Models\Category;
use App\Models\Files;
use App\Models\Post;
use App\Models\PostRelation;
use App\Models\Tag;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PostsImport implements ToCollection, WithHeadingRow
{

    public function collection(Collection $rows)
    {

        foreach ($rows as $row) {

            $post = Post::create(
                [
                    'publish_date' => $row['publish_date'],
                    'title' => $row['title'],
                    'slug' => $row['slug'],
                    'image_alt' => $row['image_alt'],
                    'description' => $row['description'],
                    'body' => $row['body'],
                    'publish_status' => PublishEnum::PUBLISHED->value,
                    // 'order_number' => $row['order number'],
                    'user_id' => $row['user_id'],
                ]
            );

            if (!empty($row['image'])) {
                $file = Files::create([
                    'path' => str_replace('post_photo/', 'uploads/', $row['image']),
                    'original_name' => str_replace('post_photo/', '', $row['image']),
                    'file_name' => str_replace('post_photo/', '', $row['image']),
                    'size' => '0',
                    'extension' => strtolower(pathinfo($row['image'], PATHINFO_EXTENSION)),
                ]);
                $post->files()->create([
                    'file_id' => $file?->id ?? null,
                    'model_type' => Post::class,
                    'model_column' => 'image',
                ]);
            }

            if (!empty($row['category'])) {
                $category = Category::query()->firstOrCreate([
                    'category_title' => $row['category'],
                ],[
                    'category_type' => TagTypeEnum::NEWS->value
                ]);
                PostRelation::create([
                    'post_id' => $post->id,
                    'relationable_id' => $category->id,
                    'relationable_type' => Category::class,
                    'relationable_is_main' => 1,
                ]);
            }
            if (!empty($row['tags'])) {
                $tags = explode(',', $row['tags']);
                foreach ($tags as $key => $row) {
                    $tag = Tag::firstOrCreate(['tag_name' => trim($row),]);
                    PostRelation::create([
                        'post_id' => $post->id,
                        'relationable_id' => $tag->id,
                        'relationable_type' => Tag::class,
                        'relationable_is_main' => $key == 0 ? 1 : 0,
                    ]);
                }
            }


        }
    }
}
