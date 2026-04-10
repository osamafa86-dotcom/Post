<?php

namespace Database\Seeders;

use App\Enums\CategoryTypeEnum;
use App\Enums\MaterialTypeEnum;
use App\Enums\ParticipantTypeEnum;
use App\Enums\TagTypeEnum;
use App\Enums\VideoTypeEnum;
use App\Enums\PublishEnum;
use App\Models\Category;
use App\Models\Files;
use App\Models\Material;
use App\Models\MaterialAlbum;
use App\Models\MaterialRelation;
use App\Models\ModelHasFile;
use App\Models\Participant;
use App\Models\SortData;
use App\Models\Tag;
use App\Traits\HasMultilingualSeeder;
use Illuminate\Database\Seeder;

class MaterialSeeder extends Seeder
{
    use HasMultilingualSeeder;

    public function run(): void
    {
        // تجهيز البيانات المشتركة مرة واحدة
        $resources = $this->prepareResources();

        // إنشاء 100 بودكاست
        for ($i = 0; $i < 100; $i++) {
            if ($this->isMultilingualEnabled()) {
                $this->createMultilingualPodcast($i, $resources);
            } else {
                $this->createSingleLanguagePodcast($i, $resources);
            }
        }

        // إنشاء 100 فيديو
        foreach (range(1, 100) as $i) {
            if ($this->isMultilingualEnabled()) {
                $this->createMultilingualVideo($i, $resources);
            } else {
                $this->createSingleLanguageVideo($i, $resources);
            }
        }
    }

    private function prepareResources(): array
    {
        return [
            'imageFiles' => $this->getFilesByExtensions(['jpg', 'jpeg', 'png', 'webp']),
            'audioFiles' => $this->getFilesByExtensions(['mp3', 'ogg', 'wav']),
            'videoFiles' => $this->getFilesByExtensions(['mp4', 'mov']),
            'podcastCategories' => $this->getCategoryIds(CategoryTypeEnum::PODCAST->value),
            'videoCategories' => $this->getCategoryIds(CategoryTypeEnum::VIDEO->value),
            'presenters' => $this->getParticipantIds(ParticipantTypeEnum::PRESENTERS->value),
            'guests' => $this->getParticipantIds(ParticipantTypeEnum::GUESTS->value),
            'tags' => Tag::where('tag_type', TagTypeEnum::NEWS->value)->pluck('id')->toArray(),
            'podcastAlbums' => MaterialAlbum::where('type', MaterialTypeEnum::PODCAST->value)->pluck('id')->toArray(),
            'videoAlbums' => MaterialAlbum::where('type', MaterialTypeEnum::VIDEO->value)->pluck('id')->toArray(),
        ];
    }

    private function createMultilingualPodcast(int $index, array $resources): void
    {
        $languages = $this->getLanguages();

        foreach ($languages as $locale) {
            $this->createPodcastForLocale($index, $resources, $locale);
        }
    }

    private function createSingleLanguagePodcast(int $index, array $resources): void
    {
        $locale = $this->getWebsiteLocale();
        $this->createPodcastForLocale($index, $resources, $locale);
    }

    private function createPodcastForLocale(int $index, array $resources, string $locale): void
    {
        $faker = $this->createFakerForLocale($locale);

        $commonData = [
            'type' => MaterialTypeEnum::PODCAST->value,
            'video_type' => null,
            'link' => null,
            'publish_status' => PublishEnum::PUBLISHED->value,
            'album_id' => $this->randomElement($resources['podcastAlbums']),
            'like' => rand(0, 1000),
        ];

        $material = $this->createMaterialForLocale($commonData, $locale, $faker, $index);
        $this->attachPodcastRelations($material, $resources);
        $this->createSortData($material);
    }

    private function createMultilingualVideo(int $index, array $resources): void
    {
        $languages = $this->getLanguages();

        foreach ($languages as $locale) {
            $this->createVideoForLocale($index, $resources, $locale);
        }
    }

    private function createSingleLanguageVideo(int $index, array $resources): void
    {
        $locale = $this->getWebsiteLocale();
        $this->createVideoForLocale($index, $resources, $locale);
    }

    private function createVideoForLocale(int $index, array $resources, string $locale): void
    {
        $faker = $this->createFakerForLocale($locale);
        $videoType = $this->determineVideoType($index);

        $commonData = [
            'type' => MaterialTypeEnum::VIDEO->value,
            'video_type' => $videoType,
            'link' => $videoType !== VideoTypeEnum::LOCAL->value ? 'https://www.youtube.com/watch?v=eW31cgY8G6w' : null,
            'publish_status' => PublishEnum::PUBLISHED->value,
            'album_id' => $this->randomElement($resources['videoAlbums']),
            'like' => rand(0, 1000),
        ];

        $material = $this->createMaterialForLocale($commonData, $locale, $faker, $index);
        $this->attachVideoRelations($material, $videoType, $resources);
        $this->createSortData($material);
    }

    private function createMaterialForLocale(array $commonData, string $locale, $faker, int $index): Material
    {
        // إنشاء البيانات المترجمة يدوياً لكل لغة
        $title = $this->generateLocalizedMaterialTitle($commonData['type'], $locale, $faker, $index);
        $description = $this->generateLocalizedMaterialDescription($commonData['type'], $locale, $faker);

        return Material::create(array_merge($commonData, [
            'title' => $title,
            'description' => $description,
            'lang' => $locale, // تخزين اللغة
        ]));
    }

    private function generateLocalizedMaterialTitle(int $materialType, string $locale, $faker, int $index): string
    {
        if ($locale === 'ar') {
            $typeBasedTitles = [
                MaterialTypeEnum::PODCAST->value => [
                    'حلقة بودكاست عن التكنولوجيا الحديثة',
                    'نقاش حول مستقبل الذكاء الاصطناعي',
                    'محادثة مع خبير في التسويق الرقمي',
                    'تحليل لأحدث trends في السوق',
                    'حوار حول تحديات ريادة الأعمال',
                    'مناقشة قضايا الاستدامة البيئية',
                    'محادثة عن الصحة النفسية والعمل',
                    'تحليل الأحداث الجارية وأثرها'
                ],
                MaterialTypeEnum::VIDEO->value => [
                    'فيديو تعليمي عن البرمجة المتقدمة',
                    'شرح مفصل لأحدث التقنيات',
                    'دورة تدريبية في التسويق الإلكتروني',
                    'تحليل مرئي للسوق الحالي',
                    'مقابلة مع رائد أعمال ناجح',
                    'تقرير مصور عن الابتكار التكنولوجي',
                    'شرح عملي لاستخدام الأدوات الحديثة',
                    'تغطية حية لفعاليات مهمة'
                ]
            ];

            $titles = $typeBasedTitles[$materialType] ?? ['مادة مميزة عن موضوع هام'];
            return $titles[array_rand($titles)] . ' ' . ($index + 1);
        }

        return $faker->sentence(rand(4, 8));
    }

    private function generateLocalizedMaterialDescription(int $materialType, string $locale, $faker): string
    {
        if ($locale === 'ar') {
            $typeBasedDescriptions = [
                MaterialTypeEnum::PODCAST->value => 'استمع إلى هذه الحلقة المميزة التي تناقش موضوعاً هاماً مع ضيوف متخصصين يقدمون رؤى قيمة وأفكار مبتكرة يمكنك تطبيقها في عملك وحياتك.',
                MaterialTypeEnum::VIDEO->value => 'شاهد هذا الفيديو المميز الذي يقدم محتوى قيماً بطريقة مشوقة وجذابة، مع شرح مفصل وأمثلة عملية تساعدك على فهم الموضوع بشكل أعمق.'
            ];

            return $typeBasedDescriptions[$materialType] ?? 'مادة مميزة تقدم محتوى قيماً ومفيداً للمشاهدين، مع تركيز على الجودة والوضوح في العرض.';
        }

        return $faker->paragraphs(rand(2, 4), true);
    }

    private function determineVideoType(int $index): int
    {
        return match (true) {
            $index <= 5 => VideoTypeEnum::YOUTUBE->value,
            $index <= 10 => VideoTypeEnum::TELEGRAM->value,
            default => VideoTypeEnum::LOCAL->value,
        };
    }

    private function attachPodcastRelations(Material $material, array $resources): void
    {
        $this->attachFile($material, 'image', $resources['imageFiles']);
        $this->attachFile($material, 'file', $resources['audioFiles']);
        $this->attachRelations($material, Category::class, $resources['podcastCategories']);
        $this->attachRelations($material, Participant::class, $resources['presenters']);
        $this->attachRelations($material, Participant::class, $resources['guests']);
        $this->attachTag($material, $resources['tags']);
    }

    private function attachVideoRelations(Material $material, int $videoType, array $resources): void
    {
        $this->attachFile($material, 'image', $resources['imageFiles']);

        if ($videoType === VideoTypeEnum::LOCAL->value) {
            $this->attachFile($material, 'file', $resources['videoFiles']);
            $this->attachRelations($material, Participant::class, $resources['presenters']);
            $this->attachRelations($material, Participant::class, $resources['guests']);
        }

        $this->attachRelations($material, Category::class, $resources['videoCategories']);
        $this->attachTag($material, $resources['tags']);
    }

    // ========== الدوال المساعدة ==========

    private function getFilesByExtensions(array $extensions): array
    {
        return Files::whereIn('extension', $extensions)->pluck('id')->toArray();
    }

    private function getCategoryIds(int $type): array
    {
        return Category::where('category_type', $type)->pluck('id')->toArray();
    }

    private function getParticipantIds(string|int $type): array
    {
        return Participant::where('type', $type)->pluck('id')->toArray();
    }

    private function createSortData(Material $material): void
    {
        $lastOrderNumber = SortData::select('order_number')->orderBy('order_number', 'desc')->first()?->order_number;
        $material->sortable()->create([
            'order_number' => $lastOrderNumber ? $lastOrderNumber + 1 : 1,
        ]);
    }

    private function attachFile(Material $material, string $column, array $fileIds): void
    {
        if (!empty($fileIds)) {
            ModelHasFile::create([
                'file_id' => $this->randomElement($fileIds),
                'model_type' => Material::class,
                'model_id' => $material->id,
                'model_column' => $column,
                'team_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function attachRelations(Material $material, string $relationClass, array $ids): void
    {
        if (!empty($ids)) {
            foreach ($ids as $index => $id) {
                MaterialRelation::create([
                    'material_id' => $material->id,
                    'relationable_id' => $id,
                    'relationable_type' => $relationClass,
                    'relationable_is_main' => $index === 0,
                ]);
            }
        }
    }

    private function attachTag(Material $material, array $tagIds): void
    {
        if (!empty($tagIds)) {
            MaterialRelation::create([
                'material_id' => $material->id,
                'relationable_id' => $this->randomElement($tagIds),
                'relationable_type' => Tag::class,
                'relationable_is_main' => 1,
            ]);
        }
    }

    private function randomElement(array $array)
    {
        return !empty($array) ? $array[array_rand($array)] : null;
    }
}
