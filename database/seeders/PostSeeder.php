<?php

namespace Database\Seeders;

use App\Enums\CategoryTypeEnum;
use App\Enums\ParticipantTypeEnum;
use App\Enums\TagTypeEnum;
use App\Enums\ImageSizeTypeEnum;
use App\Enums\NewsTypeEnum;
use App\Enums\PublishEnum;
use App\Models\Category;
use App\Models\Files;
use App\Models\Participant;
use App\Models\Post;
use App\Models\PostRelation;
use App\Models\SortData;
use App\Models\SpecialFile;
use App\Models\Tag;
use App\Models\Type;
use App\Models\User;
use App\Traits\HasMultilingualSeeder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PostSeeder extends Seeder
{
    use HasMultilingualSeeder;

    public function run(): void
    {
        $resources = $this->prepareResources();

        if (empty($resources['imageFiles'])) {
            $this->command->error('No images found in files table');
            return;
        }

        for ($i = 1; $i <= 1000; $i++) {
            if ($this->isMultilingualEnabled()) {
                $this->createMultilingualPost($i, $resources);
            } else {
                $this->createSingleLanguagePost($i, $resources);
            }
        }

        $this->command->info('Successfully created posts with all relations.');
    }

    protected function prepareResources(): array
    {
        return [
            'imageFiles' => Files::whereIn('extension', ['jpg','jpeg','png','webp'])->pluck('id')->toArray(),
            'categories' => Category::where('category_type', CategoryTypeEnum::NEWS->value)->pluck('id')->toArray(),
            'tags' => Tag::where('tag_type', TagTypeEnum::NEWS->value)->pluck('id')->toArray(),
            'specialFiles' => SpecialFile::pluck('id')->toArray(),
            'types' => Type::pluck('id')->toArray(),
            'participants' => Participant::where('type', ParticipantTypeEnum::AUTHORS->value)->pluck('id')->toArray(),
            'publishers' => config('features.general.has_publishers')
                ? Participant::where('type', ParticipantTypeEnum::PUBLISHERS->value)->pluck('id')->toArray()
                : [],
            'resources' => config('features.general.has_resources')
                ? Participant::where('type', ParticipantTypeEnum::RESOURCES->value)->pluck('id')->toArray()
                : [],
            'users' => User::pluck('id')->toArray(),
        ];
    }

    protected function createMultilingualPost(int $index, array $resources): void
    {
        $languages = $this->getLanguages();

        foreach ($languages as $locale) {
            $this->createPostForLocale($index, $resources, $locale);
        }
    }

    protected function createSingleLanguagePost(int $index, array $resources): void
    {
        $locale = $this->getWebsiteLocale();
        $this->createPostForLocale($index, $resources, $locale);
    }

    protected function createPostForLocale(int $index, array $resources, string $locale): void
    {
        $faker = $this->createFakerForLocale($locale);
        $now = now();

        // إنشاء البيانات المترجمة يدوياً لكل لغة
        $title = $this->generateLocalizedPostTitle($locale, $faker, $index);
        $description = $this->generateLocalizedPostDescription($locale, $faker);
        $body = $this->generateLocalizedPostBody($locale, $faker);
        $slug = Str::slug($title) . '-' . rand(100,999);
        $imageAlt = $locale === 'ar' ? 'صورة توضيحية للمنشور' : 'Post illustration image';

        $post = Post::create([
            'title' => $title,
            'slug' => $slug,
            'description' => $description,
            'body' => $body,
            'image_alt' => $imageAlt,
            'publish_date' => $faker->dateTimeBetween('-60 days', 'now'),
            'order_number' => rand(1, 1000),
            'image' => 'https://picsum.photos/seed/'.rand(1,1000).'/800/400',
            'video_url' => rand(0,1) ? 'https://www.youtube.com/watch?v=dQw4w9WgXcQ' : null,
            'pdf_url' => rand(0,1) ? 'https://example.com/sample.pdf' : null,
            'news_type' => $this->getRandomNewsType(),
            'image_size' => $this->getRandomImageSize(),
            'updates' => rand(0,50),
            'publish_status' => PublishEnum::PUBLISHED->value,
            'user_id' => $faker->randomElement($resources['users']),
            'publisher_id' => $faker->randomElement($resources['users']),
            'team_id' => null,
            'lang' => $locale, // تخزين اللغة
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $this->attachPostRelations($post, $resources, $now);
        $this->createSortData($post);

        if (config('features.general.has_post_available_date')) {
            $this->createPostSpecialContent($post, $faker);
        }
    }

    protected function generateLocalizedPostTitle(string $locale, $faker, int $index): string
    {
        if ($locale === 'ar') {
            $arabicTitles = [
                'تطورات جديدة في سوق التكنولوجيا العالمية',
                'دراسة تكشف عن فوائد غير متوقعة للرياضة اليومية',
                'خبراء يتوقعون تغيرات كبيرة في الاقتصاد العالمي',
                'اكتشاف علمي مذهل في مجال الطب الحديث',
                'تقرير خاص: مستقبل العمل في عصر الذكاء الاصطناعي',
                'مبادرات شبابية تهدف إلى تحسين جودة الحياة في المدن',
                'تحليل عميق لأثر التغير المناخي على الزراعة',
                'تطورات هامة في مجال الطاقة المتجددة عالمياً',
                'نصائح خبراء التغذية للحفاظ على الصحة في الشتاء',
                'مشاريع سياحية مبتكرة تجذب الزوار من حول العالم',
                'تحديات وفرص في قطاع التعليم الإلكتروني',
                'ابتكارات تقنية تسهل حياة ذوي الاحتياجات الخاصة',
                'استراتيجيات جديدة لتعزيز السياحة الداخلية',
                'تأثير وسائل التواصل الاجتماعي على الصحة النفسية',
                'مبادرات مجتمعية لتعزيز الثقافة والتراث'
            ];
            return $arabicTitles[array_rand($arabicTitles)] . ' - ' . ($index + 1);
        }

        return $faker->sentence(rand(6, 12));
    }

    protected function generateLocalizedPostDescription(string $locale, $faker): string
    {
        if ($locale === 'ar') {
            $arabicDescriptions = [
                'تحليل شامل وتفصيلي لأهم المستجدات والتطورات في هذا المجال، مع آراء الخبراء وتوقعات المستقبل.',
                'تقرير مفصل يستعرض أبرز النقاط والتفاصيل التي تهم القارئ، مع تقديم رؤى جديدة ومعلومات دقيقة.',
                'دراسة متعمقة تكشف عن حقائق مذهلة وتقدم حلولاً مبتكرة للتحديات الحالية في هذا القطاع.',
                'تحقيق استقصائي يسلط الضوء على جوانب خفية من الموضوع، مع مقابلات حصرية وتحليلات معمقة.',
                'تغطية شاملة لأحدث التطورات، مع عرض للبيانات والإحصائيات التي تساعد في فهم الصورة الكاملة.',
                'تحليل يستند إلى أدلة وبراهين علمية، مع تقديم توصيات عملية يمكن تطبيقها على أرض الواقع.',
                'تقرير يقدم نظرة مستقبلية للمجال، مع استعراض للتحديات والفرص المتاحة للتنمية والتطوير.',
                'دراسة ميدانية توضح الواقع الحالي وتقدم مقترحات عملية لتحسين الأداء وزيادة الكفاءة.'
            ];
            return $arabicDescriptions[array_rand($arabicDescriptions)];
        }

        return $faker->paragraph(rand(2, 4));
    }

    protected function generateLocalizedPostBody(string $locale, $faker): string
    {
        if ($locale === 'ar') {
            $arabicParagraphs = [
                'في ظل التطورات المتسارعة التي يشهدها العالم اليوم، أصبح من الضروري متابعة آخر المستجدات في مختلف المجالات. هذا التقرير يسلط الضوء على أبرز النقاط التي تشغل بال الخبراء والمتخصصين، مع تقديم تحليل عميق للوضع الحالي وآفاق المستقبل.',
                'تشير الدراسات الحديثة إلى وجود تحولات كبيرة في الطرق التقليدية للعمل والتعلم. هذه التغيرات تفرض علينا إعادة النظر في الكثير من المفاهيم التي كنا نعتبرها مسلمات، والبحث عن أساليب جديدة تتلاءم مع متطلبات العصر الحديث.',
                'من خلال البيانات والإحصائيات الدقيقة، يمكننا فهم الصورة الكاملة للتحديات والفرص المتاحة. هذا التحليل يعتمد على مصادر موثوقة وآراء خبراء معترف بهم في مجالهم، مما يضمن دقة المعلومات المقدمة.',
                'لقد أصبح الابتكار والتجديد من الركائز الأساسية للنجاح في الوقت الحالي. المؤسسات والأفراد الذين يستطيعون التكيف مع المتغيرات ويتبنون أفكاراً جديدة هم الأكثر قدرة على تحقيق النجاح المستدام.',
                'في خضم هذه التطورات، تبرز أهمية التعاون بين مختلف الجهات لتحقيق الأهداف المشتركة. الشراكات الاستراتيجية وتبادل الخبرات يمكن أن يساهم بشكل كبير في تخطي العقبات وتحقيق نتائج إيجابية.',
                'لا شك أن التحديات موجودة، لكن الفرص أيضاً متاحة لمن يستطيع رؤيتها والاستفادة منها. المهم هو امتلاك الرؤية الواضحة والإرادة القوية للمضي قدماً نحو تحقيق الأهداف المرجوة.'
            ];

            $body = '';
            $paragraphCount = rand(3, 6);
            for ($i = 0; $i < $paragraphCount; $i++) {
                $body .= $arabicParagraphs[array_rand($arabicParagraphs)] . "\n\n";
            }
            return trim($body);
        }

        return $faker->paragraphs(rand(3, 8), true);
    }

    protected function getRandomNewsType(): string
    {
        $newsTypes = NewsTypeEnum::cases();
        return $newsTypes[array_rand($newsTypes)]->value;
    }

    protected function getRandomImageSize(): string
    {
        $imageSizes = ImageSizeTypeEnum::available() ?: ImageSizeTypeEnum::cases();
        return $imageSizes[array_rand($imageSizes)]->value;
    }

    protected function attachPostRelations(Post $post, array $resources, $now): void
    {
        $this->attachImage($post, $resources['imageFiles'], $now);
        $this->attachRelations($post->id, Category::class, $resources['categories']);
        $this->attachRelations($post->id, Tag::class, $resources['tags']);
        $this->attachSingleRelation($post->id, SpecialFile::class, $resources['specialFiles']);
        $this->attachSingleRelation($post->id, Type::class, $resources['types']);
        $this->attachRelations($post->id, Participant::class, $resources['participants']);

        if (!empty($resources['resources'])) {
            $this->attachRelations($post->id, Participant::class, $resources['resources']);
        }

        if (!empty($resources['publishers'])) {
            $this->attachRelations($post->id, Participant::class, $resources['publishers']);
        }
    }

    protected function createSortData(Post $post): void
    {
        $lastOrderNumber = SortData::select('order_number')->orderByDesc('order_number')->first()?->order_number;
        $post->sortable()->create([
            'order_number' => $lastOrderNumber ? $lastOrderNumber + 1 : 1,
        ]);
    }

    protected function createPostSpecialContent(Post $post, $faker): void
    {
        $post->post_special_content()->create([
            'available_date' => $faker->dateTimeBetween('-3 days', '+3 days'),
        ]);
    }

    // ========== الدوال المساعدة ==========

    private function attachImage(Post $post, array $imageFiles, $now): void
    {
        $faker = $this->createFakerForLocale($this->getWebsiteLocale());

        $post->files()->create([
            'model_column' => 'image',
            'model_id' => $post->id,
            'file_id' => $faker->randomElement($imageFiles),
            'model_type' => Post::class,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    private function attachRelations(int $postId, string $modelClass, array $items): void
    {
        if (empty($items)) return;

        $count = min(count($items), rand(1,3));
        $keys = array_rand($items, $count);
        $keys = (array) $keys;
        $selectedIds = array_map(fn($k) => $items[$k], $keys);

        foreach (array_values($selectedIds) as $index => $itemId) {
            PostRelation::create([
                'post_id' => $postId,
                'relationable_id' => $itemId,
                'relationable_type' => $modelClass,
                'relationable_is_main' => $index === 0,
            ]);
        }
    }

    private function attachSingleRelation(int $postId, string $modelClass, array $items): void
    {
        if (!empty($items)) {
            $faker = $this->createFakerForLocale($this->getWebsiteLocale());

            PostRelation::create([
                'post_id' => $postId,
                'relationable_id' => $faker->randomElement($items),
                'relationable_type' => $modelClass,
                'relationable_is_main' => true,
            ]);
        }
    }
}
