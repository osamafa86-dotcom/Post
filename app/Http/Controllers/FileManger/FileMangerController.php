<?php

namespace App\Http\Controllers\FileManger;

use App\Http\Controllers\Controller;
use App\Models\Files;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use ZipStream\ZipStream;

class FileMangerController extends Controller
{
    public function uploadFiles(Request $request)
    {
        if ($request->has('chunk')) {
            return $this->handleChunkedUpload($request);
        }

        $request->validate([
            'file' => 'required|file|mimes:jpg,jpeg,png,mp4,mov,pdf,mp3,ogg,wav|max:512000',
        ]);

        $disk = config('filesystems.default') === 'local' ? 'public' : config('filesystems.default');

        $file = $request->file('file');
        $originalExtension = $file->getClientOriginalExtension();
        $uniqueFileName = 'file_' . uniqid() . '.' . $originalExtension;
        $relativePath = 'uploads/' . now()->year . '/' . now()->format('m');

        try {
            $baseFolder = config('app.aws_base_folder');
            
            // تحديد المسار الكامل بناءً على وجود base folder
            if ($disk === 's3' && !empty($baseFolder)) {
                $baseFolder = trim($baseFolder, '/');
                $fullPath = $baseFolder . '/' . $relativePath . '/' . $uniqueFileName;
                $uploadDir = $baseFolder . '/' . $relativePath;
            } else {
                $fullPath = $relativePath . '/' . $uniqueFileName;
                $uploadDir = $relativePath;
            }

            // رفع الملف
            $putOk = Storage::disk($disk)->putFileAs(
                $uploadDir,
                $file,
                $uniqueFileName,
                [
                    'visibility' => 'public',
                    'mimetype' => $this->guessMime($originalExtension),
                    'ContentType' => $this->guessMime($originalExtension),
                ]
            );

            if (!$putOk) {
                Log::error('Failed to store file');
                return response()->json(['success' => false, 'message' => 'Failed to store file.'], 500);
            }

            // حفظ المسار الكامل في قاعدة البيانات (مع أو بدون base folder)
            $fileRecord = Files::create([
                'file_name' => $file->getClientOriginalName(),
                'original_name' => $uniqueFileName,
                'path' => $fullPath,
                'size' => $file->getSize(),
                'extension' => $originalExtension,
            ]);

            $url = Storage::disk($disk)->url($fullPath);

            return response()->json([
                'success' => true,
                'name' => $uniqueFileName,
                'storage' => $disk,
                'path' => $fullPath,
                'url' => $url,
                'fileId' => $fileRecord->id
            ]);
        } catch (\Exception $e) {
            Log::error('File upload error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function exportBulk(Request $request)
    {
        $selectedFile = json_decode($request->selectedFile);

        if (empty($selectedFile) || !is_array($selectedFile)) {
            return response()->json(['error' => 'No files selected'], 400);
        }

        $files = Files::whereIn('id', $selectedFile)->get();
        $fileName = 'exported_files_' . now()->format('Y_m_d_H_i_s') . '.zip';
        if (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: application/octet-stream');
        header("Content-Disposition: attachment; filename=\"{$fileName}\"");
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');

        $zip = new ZipStream(
            outputName: $fileName,
            sendHttpHeaders: false
        );
        foreach ($files as $file) {
            $storageDisk = config('filesystems.default') === 'local' ? 'public' : config('filesystems.default');
            $path = 'uploads/' . $file->file_name;
            if (Storage::disk($storageDisk)->exists($path)) {
                $stream = Storage::disk($storageDisk)->readStream($path);
                if ($stream) {
                    $zip->addFileFromStream(
                        fileName: $file->original_name,
                        stream: $stream
                    );
                    fclose($stream);
                }
            }
        }

        $zip->finish();
        exit;
    }

    protected function handleChunkedUpload(Request $request)
    {
        try {
            $file = $request->file('file');
            $dzChunkIndex = (int)$request->input('dzchunkindex', 0);
            $dzTotalChunks = (int)$request->input('dztotalchunkcount', 1);
            $dzUuid = $request->input('dzuuid');
            $originalName = $file->getClientOriginalName();
            $originalExt = $file->getClientOriginalExtension();
            $fileId = $dzUuid ?: md5($originalName . microtime());

            $chunksDir = storage_path('app/chunks/' . $fileId);
            if (!is_dir($chunksDir)) {
                mkdir($chunksDir, 0755, true);
            }

            // استخدم move من Laravel بدلاً من move_uploaded_file
            $file->move($chunksDir, 'chunk-' . $dzChunkIndex);

            $uploadedChunks = count(glob($chunksDir . '/chunk-*'));

            if ($uploadedChunks >= $dzTotalChunks) {
                return $this->combineChunks($fileId, $originalName, $originalExt);
            }

            return response()->json([
                'success' => true,
                'message' => 'Chunk ' . ($dzChunkIndex + 1) . ' uploaded',
                'fileId' => $fileId,
            ]);
        } catch (\Exception $e) {
            Log::error('Chunk upload error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    protected function combineChunks($fileId, $originalName, $originalExtension)
    {
        try {
            $chunksDir = storage_path('app/chunks/' . $fileId);
            $chunks = glob($chunksDir . '/chunk-*');

            // رتب القطع
            usort($chunks, function ($a, $b) use ($chunksDir) {
                $indexA = intval(str_replace($chunksDir . '/chunk-', '', $a));
                $indexB = intval(str_replace($chunksDir . '/chunk-', '', $b));
                return $indexA <=> $indexB;
            });

            $disk = config('filesystems.default') === 'local' ? 'public' : config('filesystems.default');
            $uniqueFileName = 'file_' . uniqid() . '.' . $originalExtension;
            $relativePath = 'uploads/' . now()->year . '/' . now()->format('m');
            $baseFolder = config('app.aws_base_folder');

            // تحديد المسار الكامل بناءً على وجود base folder
            if ($disk === 's3' && !empty($baseFolder)) {
                $baseFolder = trim($baseFolder, '/');
                $fullPath = $baseFolder . '/' . $relativePath . '/' . $uniqueFileName;
            } else {
                $fullPath = $relativePath . '/' . $uniqueFileName;
            }

            // اجمع القطع في ملف مؤقت (stream) بدل الذاكرة
            $tmpFile = tempnam(sys_get_temp_dir(), 'up_');
            $out = fopen($tmpFile, 'w');
            foreach ($chunks as $chunk) {
                $in = fopen($chunk, 'r');
                stream_copy_to_stream($in, $out);
                fclose($in);
                @unlink($chunk);
            }
            fclose($out);
            @rmdir($chunksDir);

            // ارفع الملف كستريم إلى الديسك (S3/محلي)
            $mime = $this->guessMime($originalExtension);

            $putOk = Storage::disk($disk)->put(
                $fullPath,
                fopen($tmpFile, 'r'),
                [
                    'visibility' => 'public',
                    'mimetype' => $mime,
                    'ContentType' => $mime,
                ]
            );

            @unlink($tmpFile);

            if (!$putOk) {
                throw new \RuntimeException('Failed to put file to storage.');
            }

            // احسب الحجم من الديسك بعد الرفع
            $size = Storage::disk($disk)->size($fullPath);

            // حفظ المسار الكامل في قاعدة البيانات
            $fileRecord = Files::create([
                'file_name' => $originalName,
                'original_name' => $uniqueFileName,
                'path' => $fullPath,
                'size' => $size,
                'extension' => $originalExtension,
            ]);

            $url = Storage::disk($disk)->url($fullPath);

            return response()->json([
                'success' => true,
                'name' => $uniqueFileName,
                'storage' => $disk,
                'path' => $fullPath,
                'url' => $url,
                'fileId' => $fileRecord->id,
                'message' => 'File uploaded successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Chunk combining error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function crop(Request $request)
    {
        $request->validate([
            'image' => 'required|file|image',
            'fileId' => 'required|integer',
        ]);

        $disk = config('filesystems.default') === 'local' ? 'public' : config('filesystems.default');

        $file = $request->file('image');
        $originalExtension = 'png'; // Cropper يرسل Canvas بصيغة PNG
        $uniqueFileName = 'file_' . uniqid() . '.' . $originalExtension;
        $relativePath = 'uploads/' . now()->year . '/' . now()->format('m');
        $baseFolder = config('app.aws_base_folder');

        // تحديد المسار الكامل بناءً على وجود base folder
        if ($disk === 's3' && !empty($baseFolder)) {
            $baseFolder = trim($baseFolder, '/');
            $fullPath = $baseFolder . '/' . $relativePath . '/' . $uniqueFileName;
        } else {
            $fullPath = $relativePath . '/' . $uniqueFileName;
        }

        try {
            $putOk = Storage::disk($disk)->put(
                $fullPath,
                fopen($file->getRealPath(), 'r'),
                [
                    'visibility' => 'public',
                    'mimetype' => 'image/png',
                    'ContentType' => 'image/png',
                ]
            );

            if (!$putOk) {
                return response()->json(['success' => false, 'message' => 'Failed to store cropped image.'], 500);
            }

            // تحديث السجل
            $fileRecord = Files::findOrFail($request->fileId);
            $fileRecord->update([
                'original_name' => $uniqueFileName,
                'path' => $fullPath,
                'size' => $file->getSize(),
                'extension' => $originalExtension,
            ]);

            $url = Storage::disk($disk)->url($fullPath);

            return response()->json([
                'success' => true,
                'url' => $url,
                'fileId' => $fileRecord->id,
            ]);
        } catch (\Exception $e) {
            Log::error('Image crop error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function cropAsNew(Request $request)
    {
        $request->validate([
            'image' => 'required|file|image',
            'fileId' => 'required|integer',
        ]);

        $disk = config('filesystems.default') === 'local' ? 'public' : config('filesystems.default');

        $file = $request->file('image');
        $originalExtension = 'png'; // Cropper يرسل Canvas بصيغة PNG
        $uniqueFileName = 'file_' . uniqid() . '.' . $originalExtension;
        $relativePath = 'uploads/' . now()->year . '/' . now()->format('m');
        $baseFolder = config('app.aws_base_folder');

        // تحديد المسار الكامل بناءً على وجود base folder
        if ($disk === 's3' && !empty($baseFolder)) {
            $baseFolder = trim($baseFolder, '/');
            $fullPath = $baseFolder . '/' . $relativePath . '/' . $uniqueFileName;
        } else {
            $fullPath = $relativePath . '/' . $uniqueFileName;
        }

        try {
            $putOk = Storage::disk($disk)->put(
                $fullPath,
                fopen($file->getRealPath(), 'r'),
                [
                    'visibility' => 'public',
                    'mimetype' => 'image/png',
                    'ContentType' => 'image/png',
                ]
            );

            if (!$putOk) {
                return response()->json(['success' => false, 'message' => 'Failed to store cropped image.'], 500);
            }

            // Get original file to copy file_name
            $originalFile = Files::findOrFail($request->fileId);
            
            // Create new file record instead of updating
            $newFileRecord = Files::create([
                'file_name' => $originalFile->file_name . ' (cropped)',
                'original_name' => $uniqueFileName,
                'path' => $fullPath,
                'size' => $file->getSize(),
                'extension' => $originalExtension,
            ]);

            $url = Storage::disk($disk)->url($fullPath);

            return response()->json([
                'success' => true,
                'url' => $url,
                'fileId' => $newFileRecord->id,
            ]);
        } catch (\Exception $e) {
            Log::error('Image crop as new error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function proxyImage(Request $request)
    {
        $request->validate([
            'fileId' => 'required|integer',
        ]);

        try {
            $file = Files::findOrFail($request->fileId);
            $disk = config('filesystems.default') === 'local' ? 'public' : config('filesystems.default');
            
            // استخدام المسار كما هو من قاعدة البيانات
            $filePath = $file->path;

            if (!Storage::disk($disk)->exists($filePath)) {
                return response()->json(['error' => 'File not found'], 404);
            }

            $contents = Storage::disk($disk)->get($filePath);
            $mimeType = $this->guessMime($file->extension);

            return response($contents)
                ->header('Content-Type', $mimeType)
                ->header('Access-Control-Allow-Origin', '*')
                ->header('Access-Control-Allow-Methods', 'GET, OPTIONS')
                ->header('Access-Control-Allow-Headers', 'Content-Type')
                ->header('Cache-Control', 'public, max-age=3600');
        } catch (\Exception $e) {
            Log::error('Image proxy error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load image'], 500);
        }
    }

    private function guessMime(string $ext): string
    {
        return match (strtolower($ext)) {
            'mp4' => 'video/mp4',
            'mov' => 'video/quicktime',
            'mp3' => 'audio/mpeg',
            'ogg' => 'audio/ogg',
            'wav' => 'audio/wav',
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'pdf' => 'application/pdf',
            default => 'application/octet-stream',
        };
    }
}
