<?php

namespace Database\Seeders;

use App\Models\Files;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class FileLibrarySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $files = [
            'assets/main/almashhad/img/articles/Horizontal/3.jpg',
            'assets/main/almashhad/img/articles/Horizontal/2.jpg',
            'assets/main/almashhad/img/articles/Horizontal/1.jpg',
            'assets/main/music.mp3',
            'assets/main/almashhad/video/2247164-hd_1920_1080_30fps.mp4'
        ];
        $destinationDir = storage_path('app/public/uploads');
        if (!file_exists($destinationDir)) {
            mkdir($destinationDir, 0755, true);
        }
        foreach ($files as $filePath) {
            $fullPath = public_path($filePath);

            if (!file_exists($fullPath)) {
                $this->command->error(__('messages.file_not_found' ,[
                    'attribute' => $filePath,
                ]));
                continue;
            }

            $originalName = basename($fullPath);
            $extension = pathinfo($fullPath, PATHINFO_EXTENSION);
            $size = filesize($fullPath);
            $randomName = Str::random(20) . '.' . $extension;

            // Optional: copy to storage/app/public/files
            $newStoragePath = "{$randomName}";
            copy($fullPath, storage_path("app/public/uploads/{$newStoragePath}"));

            Files::create([
                'path' => "/uploads/{$newStoragePath}",
                'original_name' => $originalName,
                'file_name' => $randomName,
                'size' => $size,
                'extension' => $extension,
                'team_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
