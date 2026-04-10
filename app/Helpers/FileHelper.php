<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

class FileHelper
{
    public static function fileUrl(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        // External URLs - return as-is
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        $disk = config('filesystems.default', 'public');
        
        // Clean up path - remove leading slashes
        $path = ltrim($path, '/');
        
        // For local/public disks
        if ($disk === 'local' || $disk === 'public') {
            return asset('storage/' . $path);
        }
        
        // For S3 disk - construct URL manually to ensure bucket name is included
        if ($disk === 's3') {
            $awsUrl = rtrim(config('app.aws_url'), '/');
            $bucket = config('filesystems.disks.s3.bucket');
            
            // Always add bucket name to the URL (Object Storage requires it in the path)
            return $awsUrl . '/' . $bucket . '/' . $path;
        }
        
        // Fallback
        return asset('storage/' . $path);
    }
}
