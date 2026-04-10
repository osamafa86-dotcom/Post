<?php
namespace App\Jobs;

use ArPHP\I18N\Arabic;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Barryvdh\DomPDF\Facade\Pdf;
use Intervention\Image\Facades\Image;

class GenerateCertificatesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    protected $imagePath, $excelPath, $zipName;

    public function __construct($imagePath, $excelPath, $zipName)
    {
        $this->imagePath = $imagePath;
        $this->excelPath = $excelPath;
        $this->zipName = $zipName;
    }

    public function handle(): void
    {
        // تحميل ملف الإكسل
        $spreadsheet = IOFactory::load(Storage::path($this->excelPath));
        $sheet = $spreadsheet->getActiveSheet();
        $names = $sheet->toArray(null, true, true, true);

        // إنشاء مجلد مؤقت
        $tempDir = storage_path('app/temp/certs_' . uniqid());
        File::makeDirectory($tempDir, 0777, true);

        $imageSource = Storage::path($this->imagePath);

        foreach ($names as $key => $row) {
            $name = $row['A'] ?? null;
            if (!$name) continue;

            // تحميل صورة الشهادة
            $img = Image::make($imageSource);
            $Arabic = new Arabic('Glyphs');
            $arName = $Arabic->utf8Glyphs('السيدة/ '.$name ?? null);
            // كتابة الاسم بجانب "السيدة /" الموجودة مسبقًا في التصميم
            $img->text($arName, 2150, 1380, function ($font) {
                $font->file(public_path('assets/dashboard/dev_tool_fonts/NeoSansArabicMedium.ttf')); // خط عربي
                $font->size(92);
                $font->color('#326666');
                $font->align('right');
            });
            $safeFileName = Str::slug($name, '_');

            // حفظ صورة مؤقتة
            $pngPath = $tempDir . '/' . $safeFileName . '.png';
            $img->save($pngPath);

            // تحويل الصورة إلى PDF
            $pdfPath = str_replace('.png', '.pdf', $pngPath);
            Pdf::loadView('certificate', ['imagePath' => $pngPath])
                ->setPaper('a4', 'landscape')
                ->save($pdfPath);
        }

        // إنشاء ملف ZIP
        $zipPath = storage_path('app/public/' . $this->zipName);
        $zip = new \ZipArchive;
        if ($zip->open($zipPath, \ZipArchive::CREATE) === TRUE) {
            foreach (glob($tempDir . '/*.pdf') as $file) {
                $zip->addFile($file, basename($file));
            }
            $zip->close();
        }

        // تنظيف الملفات المؤقتة
        Storage::delete($this->imagePath);
        Storage::delete($this->excelPath);
        File::deleteDirectory($tempDir);
    }
}
