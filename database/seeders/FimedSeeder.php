<?php

namespace Database\Seeders;

use App\Enums\AdvertisementPlaceEnum;
use App\Enums\AdvertisementTypeEnum;
use App\Enums\AdvertisementUrlTargetEnum;
use App\Enums\CategoryTypeEnum;
use App\Enums\ImageSizeTypeEnum;
use App\Enums\LinkStatusEnum;
use App\Enums\LinkUrlTargetEnum;
use App\Enums\NewsTypeEnum;
use App\Enums\ParticipantTypeEnum;
use App\Enums\PublishEnum;
use App\Enums\TagTypeEnum;
use App\Models\Advertisement;
use App\Models\Category;
use App\Models\Files;
use App\Models\Icon;
use App\Models\ModelHasFile;
use App\Models\NavbarLink;
use App\Models\Participant;
use App\Models\ParticipantSocialMedia;
use App\Models\Post;
use App\Models\PostRelation;
use App\Models\Setting;
use App\Models\SocialMedia;
use App\Models\SortData;
use App\Models\Tag;
use App\Models\User;
use App\Models\View;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class FimedSeeder extends Seeder
{
    private array $imageFileIds = [];
    private array $categoryIds = [];
    private array $authorIds = [];
    private array $tagIds = [];

    public function run(): void
    {
        $this->command->info('🚀 بدء تهيئة بيانات FiMED...');

        $this->seedFiles();
        $this->seedSettings();
        $this->seedIcons();
        $this->seedSocialMedia();
        $this->seedNavbarLinks();
        $this->seedCategories();
        $this->seedTags();
        $this->seedParticipants();
        $this->seedPosts();
        $this->seedAdvertisements();

        $this->command->info('✅ تمت تهيئة بيانات FiMED بنجاح!');
    }

    // ========== 1. FILES ==========
    private function seedFiles(): void
    {
        $this->command->info('📁 إنشاء ملفات الصور...');

        // Check if we have existing files
        $existing = Files::whereIn('extension', ['jpg', 'jpeg', 'png', 'webp'])->pluck('id')->toArray();
        if (!empty($existing)) {
            $this->imageFileIds = $existing;
            $this->command->info("   تم العثور على " . count($existing) . " صورة موجودة");
            return;
        }

        // Create placeholder files using picsum
        $storageDir = storage_path('app/public/uploads');
        if (!is_dir($storageDir)) {
            mkdir($storageDir, 0755, true);
        }

        for ($i = 1; $i <= 20; $i++) {
            $fileName = "fimed_placeholder_{$i}.jpg";
            $filePath = "/uploads/{$fileName}";

            $file = Files::create([
                'path' => $filePath,
                'original_name' => $fileName,
                'file_name' => $fileName,
                'extension' => 'jpg',
                'size' => rand(50000, 500000),
                'type' => 'image',
            ]);

            $this->imageFileIds[] = $file->id;

            // Copy placeholder image
            $placeholderSrc = public_path('frontend/fimed/images/placeholder.png');
            if (file_exists($placeholderSrc)) {
                @copy($placeholderSrc, $storageDir . "/{$fileName}");
            }
        }

        $this->command->info("   تم إنشاء " . count($this->imageFileIds) . " صورة");
    }

    // ========== 2. SETTINGS ==========
    private function seedSettings(): void
    {
        $this->command->info('⚙️ إنشاء إعدادات الموقع...');

        Setting::updateOrCreate(
            ['id' => Setting::first()?->id ?? 0],
            [
                'site_name' => 'FiMED',
                'light_site_name' => 'المؤسسة الفلسطينية للإعلام',
                'footer_description' => 'مؤسسة إعلامية فلسطينية مستقلة، تأسست بهدف نقل الحقيقة وتسليط الضوء على القضية الفلسطينية والشؤون العربية والدولية بمهنية ومصداقية.',
                'copyright_text' => 'جميع الحقوق محفوظة © FiMED 2024',
                'site_email' => 'info@fimed.ps',
                'address' => 'إسطنبول، تركيا',
                'phone' => '+90 555 123 4567',
                'tags' => 'فلسطين,القدس,إعلام,أخبار,تقارير',
                'open_graph_description' => 'المؤسسة الفلسطينية للإعلام - FiMED',
                'open_graph_local' => 'ar_AR',
                'website_active' => true,
                'lang' => 'ar',
            ]
        );
    }

    // ========== 3. ICONS ==========
    private function seedIcons(): void
    {
        if (Icon::count() > 0) return;

        $this->command->info('🎨 إنشاء الأيقونات...');

        $icons = [
            'fab fa-facebook-f', 'fab fa-x-twitter', 'fab fa-youtube',
            'fab fa-telegram-plane', 'fab fa-instagram', 'fab fa-whatsapp',
            'fab fa-linkedin-in', 'fas fa-globe', 'fas fa-rss',
            'fas fa-home', 'fas fa-newspaper', 'fas fa-link',
        ];

        foreach ($icons as $icon) {
            Icon::firstOrCreate(['icon_path' => $icon], [
                'icon_path' => $icon,
                'lang' => 'ar',
            ]);
        }
    }

    // ========== 4. SOCIAL MEDIA ==========
    private function seedSocialMedia(): void
    {
        $this->command->info('🔗 إنشاء روابط التواصل...');

        SocialMedia::query()->delete();

        $socials = [
            ['title' => 'Facebook', 'url' => 'https://facebook.com/fimed', 'icon' => 'fab fa-facebook-f', 'position' => 'header', 'order' => 1],
            ['title' => 'X (Twitter)', 'url' => 'https://x.com/fimed', 'icon' => 'fab fa-x-twitter', 'position' => 'header', 'order' => 2],
            ['title' => 'YouTube', 'url' => 'https://youtube.com/fimed', 'icon' => 'fab fa-youtube', 'position' => 'header', 'order' => 3],
            ['title' => 'Telegram', 'url' => 'https://t.me/fimed', 'icon' => 'fab fa-telegram-plane', 'position' => 'header', 'order' => 4],
            ['title' => 'Instagram', 'url' => 'https://instagram.com/fimed', 'icon' => 'fab fa-instagram', 'position' => 'header', 'order' => 5],
        ];

        foreach ($socials as $social) {
            $icon = Icon::where('icon_path', $social['icon'])->first();
            SocialMedia::create([
                'title' => $social['title'],
                'url' => $social['url'],
                'icon_id' => $icon?->id,
                'position' => $social['position'],
                'order' => $social['order'],
                'lang' => 'ar',
            ]);
        }
    }

    // ========== 5. NAVBAR LINKS ==========
    private function seedNavbarLinks(): void
    {
        $this->command->info('🧭 إنشاء روابط التنقل...');

        NavbarLink::query()->delete();

        $defaultIcon = Icon::first()?->id;
        $links = [
            ['name' => 'فلسطين', 'url' => '/category_news/فلسطين'],
            ['name' => 'القدس', 'url' => '/category_news/القدس'],
            ['name' => 'تركيا', 'url' => '/category_news/تركيا'],
            ['name' => 'العالم العربي', 'url' => '/category_news/العالم العربي'],
            ['name' => 'فلسطينيو تركيا', 'url' => '/category_news/فلسطينيو تركيا'],
            ['name' => 'تقارير', 'url' => '/category_news/تقارير'],
            ['name' => 'صوت فلسطين', 'url' => '/category_news/صوت فلسطين'],
            ['name' => 'آراء ومقالات', 'url' => '/all_posts'],
        ];

        foreach ($links as $i => $link) {
            NavbarLink::create([
                'link_name' => $link['name'],
                'link_url' => $link['url'],
                'link_order' => $i + 1,
                'link_status' => LinkStatusEnum::CUSTOM_LINK->value ?? 1,
                'link_open' => LinkUrlTargetEnum::SAME_WINDOW->value ?? 1,
                'icon_id' => $defaultIcon,
                'parent_id' => null,
                'lang' => 'ar',
            ]);
        }
    }

    // ========== 6. CATEGORIES ==========
    private function seedCategories(): void
    {
        $this->command->info('📂 إنشاء الأقسام...');

        $categories = [
            'فلسطين', 'القدس', 'تركيا', 'العالم العربي',
            'فلسطينيو تركيا', 'تقارير', 'صوت فلسطين', 'آراء ومقالات',
        ];

        foreach ($categories as $i => $name) {
            $cat = Category::firstOrCreate(
                ['category_title' => $name, 'lang' => 'ar'],
                [
                    'category_title' => $name,
                    'category_description' => "أخبار ومقالات في قسم {$name}",
                    'category_type' => CategoryTypeEnum::NEWS->value,
                    'show_index' => 1,
                    'order' => $i + 1,
                    'lang' => 'ar',
                ]
            );

            $this->categoryIds[$name] = $cat->id;

            // Attach image
            if (!empty($this->imageFileIds)) {
                ModelHasFile::firstOrCreate([
                    'model_type' => Category::class,
                    'model_id' => $cat->id,
                    'model_column' => 'image',
                ], [
                    'file_id' => $this->imageFileIds[array_rand($this->imageFileIds)],
                ]);
            }
        }
    }

    // ========== 7. TAGS ==========
    private function seedTags(): void
    {
        $this->command->info('🏷️ إنشاء الوسوم...');

        $tags = [
            'القضية الفلسطينية', 'المسجد الأقصى', 'الاحتلال', 'المقاومة',
            'غزة', 'الضفة الغربية', 'اللاجئون', 'القدس المحتلة',
            'حق العودة', 'إسطنبول', 'تركيا', 'الإعلام',
        ];

        foreach ($tags as $name) {
            $tag = Tag::firstOrCreate(
                ['tag_name' => $name, 'lang' => 'ar'],
                [
                    'tag_name' => $name,
                    'tag_type' => TagTypeEnum::NEWS->value,
                    'lang' => 'ar',
                ]
            );
            $this->tagIds[] = $tag->id;
        }
    }

    // ========== 8. PARTICIPANTS (Authors) ==========
    private function seedParticipants(): void
    {
        $this->command->info('✍️ إنشاء الكتّاب...');

        $authors = [
            ['name' => 'أحمد محمد', 'work' => 'كاتب وباحث سياسي', 'desc' => 'كاتب وباحث سياسي متخصص في الشأن الفلسطيني والعلاقات الدولية. يكتب بانتظام في عدة منصات إعلامية عربية ودولية.'],
            ['name' => 'سارة الخطيب', 'work' => 'صحفية استقصائية', 'desc' => 'صحفية استقصائية حاصلة على عدة جوائز صحفية. متخصصة في تغطية قضايا حقوق الإنسان والشأن الفلسطيني.'],
            ['name' => 'عمر حسن', 'work' => 'محلل سياسي', 'desc' => 'محلل سياسي ومعلق على الشؤون الإقليمية، متخصص في العلاقات التركية-العربية والقضية الفلسطينية.'],
            ['name' => 'ليلى عبدالله', 'work' => 'كاتبة رأي', 'desc' => 'كاتبة رأي ومدونة نشطة في مجال حقوق المرأة والتنمية المجتمعية في الدول العربية.'],
            ['name' => 'خالد يوسف', 'work' => 'مراسل ميداني', 'desc' => 'مراسل ميداني يغطي الأحداث من قلب الحدث. عمل في عدة مناطق ساخنة وله تقارير حصرية من أرض الميدان.'],
            ['name' => 'نور الدين', 'work' => 'باحث أكاديمي', 'desc' => 'باحث أكاديمي في جامعة إسطنبول، متخصص في الدراسات الإسلامية وتاريخ القدس.'],
            ['name' => 'هدى سليمان', 'work' => 'مديرة تحرير', 'desc' => 'مديرة تحرير في FiMED ولها خبرة تزيد عن 15 عاماً في مجال الإعلام والصحافة.'],
            ['name' => 'فارس نبيل', 'work' => 'كاتب ومؤرخ', 'desc' => 'كاتب ومؤرخ فلسطيني مقيم في تركيا، متخصص في توثيق الرواية الفلسطينية والتراث الثقافي.'],
        ];

        foreach ($authors as $i => $data) {
            $author = Participant::firstOrCreate(
                ['name' => $data['name'], 'lang' => 'ar'],
                [
                    'name' => $data['name'],
                    'work' => $data['work'],
                    'description' => $data['desc'],
                    'type' => ParticipantTypeEnum::AUTHORS->value,
                    'lang' => 'ar',
                ]
            );

            $this->authorIds[] = $author->id;

            // Attach image
            if (!empty($this->imageFileIds)) {
                ModelHasFile::firstOrCreate([
                    'model_type' => Participant::class,
                    'model_id' => $author->id,
                    'model_column' => 'main_image',
                ], [
                    'file_id' => $this->imageFileIds[$i % count($this->imageFileIds)],
                ]);
            }
        }
    }

    // ========== 9. POSTS ==========
    private function seedPosts(): void
    {
        $this->command->info('📰 إنشاء المقالات...');

        if (empty($this->imageFileIds) || empty($this->categoryIds) || empty($this->authorIds)) {
            $this->command->error('   بيانات مسبقة مفقودة!');
            return;
        }

        $userId = User::first()?->id ?? 1;
        $categoryNames = array_keys($this->categoryIds);
        $postIndex = 0;

        $titles = [
            'فلسطين' => [
                'تطورات ميدانية جديدة في قطاع غزة وتصعيد إسرائيلي متواصل',
                'الأمم المتحدة تدعو لوقف فوري لإطلاق النار في الأراضي الفلسطينية',
                'مظاهرات حاشدة في عواصم عالمية تضامناً مع الشعب الفلسطيني',
                'تقرير أممي يوثق انتهاكات الاحتلال لحقوق الإنسان الفلسطيني',
                'مبادرة دولية جديدة لإعادة إعمار غزة وتقديم المساعدات الإنسانية',
            ],
            'القدس' => [
                'اقتحامات متواصلة للمسجد الأقصى من قبل المستوطنين بحماية جيش الاحتلال',
                'مقدسيون يتصدون لمخططات التهويد في حي الشيخ جراح',
                'القدس: سياسات التهجير القسري تتصاعد وسط صمت دولي',
                'المسجد الأقصى يستقبل آلاف المصلين في صلاة الجمعة',
            ],
            'تركيا' => [
                'تركيا تؤكد موقفها الثابت تجاه القضية الفلسطينية في مجلس الأمن',
                'العلاقات التركية-العربية تشهد تطوراً ملحوظاً على الصعيد الاقتصادي',
                'إسطنبول تحتضن مؤتمراً دولياً حول مستقبل القضية الفلسطينية',
                'الاقتصاد التركي يسجل نمواً إيجابياً رغم التحديات العالمية',
            ],
            'العالم العربي' => [
                'قمة عربية طارئة لمناقشة التطورات في فلسطين والمنطقة',
                'مصر تبذل جهوداً مكثفة للتوصل لوقف إطلاق النار في غزة',
                'الأردن يحذر من عواقب استمرار التصعيد الإسرائيلي في القدس',
            ],
            'فلسطينيو تركيا' => [
                'الجالية الفلسطينية في تركيا تنظم وقفة تضامنية مع غزة',
                'طلاب فلسطينيون في تركيا يحققون إنجازات أكاديمية مميزة',
                'مبادرات مجتمعية فلسطينية في إسطنبول لتعزيز التكافل الاجتماعي',
                'فلسطينيو تركيا يقيمون معرضاً ثقافياً عن التراث الفلسطيني',
            ],
            'تقارير' => [
                'تقرير خاص: كيف يعيش اللاجئون الفلسطينيون في المخيمات بعد عقود من النكبة',
                'تحقيق استقصائي: شبكات التمويل المشبوهة للاستيطان الإسرائيلي',
                'تقرير: التعليم في ظل الحرب - كيف يتعلم أطفال غزة تحت القصف',
                'تقرير ميداني: الزراعة الفلسطينية في مواجهة سياسات المصادرة',
                'تحقيق: معاناة الأسرى الفلسطينيين في سجون الاحتلال',
            ],
            'صوت فلسطين' => [
                'شهادة: ناجية من مجزرة تروي تفاصيل ما حدث ليلة القصف',
                'حوار مع أسير محرر: الحرية أغلى ما يملك الإنسان',
                'قصة صمود: عائلة فلسطينية تعيد بناء منزلها للمرة الرابعة',
            ],
            'آراء ومقالات' => [
                'القضية الفلسطينية في ميزان القانون الدولي - قراءة تحليلية',
                'مستقبل الإعلام الفلسطيني: تحديات وفرص في العصر الرقمي',
                'دور الشباب الفلسطيني في تعزيز الهوية الوطنية والثقافية',
            ],
        ];

        $descriptions = [
            'تحليل شامل وتفصيلي لأهم المستجدات والتطورات، مع آراء الخبراء وتوقعات المستقبل القريب.',
            'تقرير مفصل يستعرض أبرز النقاط والتفاصيل، مع تقديم رؤى جديدة ومعلومات موثقة من مصادر حصرية.',
            'دراسة متعمقة تكشف عن حقائق وتقدم حلولاً مبتكرة للتحديات الراهنة في هذا الملف.',
            'تغطية شاملة لأحدث التطورات، مع عرض للبيانات والإحصائيات التي تساعد في فهم الصورة الكاملة.',
            'تحليل يستند إلى أدلة وبراهين موثقة، مع تقديم توصيات عملية يمكن تطبيقها على أرض الواقع.',
        ];

        $body = '<p class="article-body__lead">في ظل التطورات المتسارعة التي يشهدها الملف الفلسطيني، أصبح من الضروري متابعة آخر المستجدات وتحليل أبعادها المختلفة. هذا التقرير يسلط الضوء على أبرز النقاط التي تشغل الرأي العام.</p>'
            . '<h2>السياق العام</h2>'
            . '<p>تشير المعطيات الحالية إلى وجود تحولات كبيرة في المشهد السياسي والميداني، حيث تتفاعل الأطراف المختلفة مع المستجدات بطرق متباينة. الخبراء يرون أن المرحلة القادمة ستكون حاسمة في تحديد مسار الأحداث.</p>'
            . '<p>من خلال البيانات والإحصائيات الدقيقة، يمكننا فهم الصورة الكاملة للتحديات والفرص المتاحة. هذا التحليل يعتمد على مصادر موثوقة وآراء خبراء معترف بهم في مجالهم.</p>'
            . '<blockquote class="article-blockquote"><div class="article-blockquote__icon"><i class="fas fa-quote-right"></i></div><div class="article-blockquote__content"><p>الحقيقة لا يمكن إخفاؤها مهما حاولوا، والعدالة ستأتي مهما طال الانتظار. الشعب الفلسطيني أثبت للعالم أن إرادة الحياة أقوى من كل آلات القتل والدمار.</p></div></blockquote>'
            . '<h2>التحليل والمتابعة</h2>'
            . '<p>لقد أصبح واضحاً أن المجتمع الدولي بدأ يتحرك بشكل أكبر تجاه القضية الفلسطينية، وإن كانت هذه التحركات لا تزال دون المستوى المطلوب. المطلوب هو موقف دولي حازم يضع حداً للانتهاكات المتواصلة.</p>'
            . '<p>في السياق ذاته، تبرز أهمية الدور الإعلامي في نقل الحقيقة وتوثيق ما يجري على أرض الواقع. الإعلام الفلسطيني والعربي مطالب بمزيد من المهنية والجرأة في التعاطي مع هذه القضايا.</p>'
            . '<ul class="article-list"><li>متابعة مستمرة للتطورات الميدانية</li><li>تحليلات معمقة من خبراء متخصصين</li><li>تقارير حصرية من أرض الحدث</li><li>توثيق شهادات وروايات إنسانية</li></ul>'
            . '<p>نحن في FiMED نلتزم بنقل الحقيقة بكل أمانة ومهنية، ونعمل على تقديم محتوى إعلامي يرتقي لمستوى القضية وتضحيات الشعب الفلسطيني.</p>';

        foreach ($titles as $catName => $catTitles) {
            $catId = $this->categoryIds[$catName] ?? null;
            if (!$catId) continue;

            foreach ($catTitles as $titleIndex => $title) {
                $postIndex++;
                $isMain = $postIndex <= 2; // First 2 posts are MAIN_NEWS

                $post = Post::create([
                    'title' => $title,
                    'slug' => Str::slug($title) . '-' . $postIndex,
                    'description' => $descriptions[array_rand($descriptions)],
                    'body' => $body,
                    'image_alt' => 'صورة توضيحية - ' . $title,
                    'publish_date' => now()->subHours(rand(1, 720)),
                    'order_number' => $postIndex,
                    'news_type' => $isMain ? NewsTypeEnum::MAIN_NEWS->value : NewsTypeEnum::SUB_NEWS->value,
                    'image_size' => ImageSizeTypeEnum::LARGE_IMAGE->value,
                    'publish_status' => PublishEnum::PUBLISHED->value,
                    'user_id' => $userId,
                    'lang' => 'ar',
                ]);

                // Attach main image
                $post->files()->create([
                    'model_column' => 'main_image',
                    'model_id' => $post->id,
                    'file_id' => $this->imageFileIds[array_rand($this->imageFileIds)],
                    'model_type' => Post::class,
                ]);

                // Attach category
                PostRelation::create([
                    'post_id' => $post->id,
                    'relationable_id' => $catId,
                    'relationable_type' => Category::class,
                    'relationable_is_main' => true,
                ]);

                // Attach author
                $authorId = $this->authorIds[array_rand($this->authorIds)];
                PostRelation::create([
                    'post_id' => $post->id,
                    'relationable_id' => $authorId,
                    'relationable_type' => Participant::class,
                    'relationable_is_main' => true,
                ]);

                // Attach 2-3 tags
                $tagSample = array_rand($this->tagIds, min(3, count($this->tagIds)));
                foreach ((array) $tagSample as $ti => $tagKey) {
                    PostRelation::create([
                        'post_id' => $post->id,
                        'relationable_id' => $this->tagIds[$tagKey],
                        'relationable_type' => Tag::class,
                        'relationable_is_main' => $ti === 0,
                    ]);
                }

                // Add random views
                View::create([
                    'viewable_type' => Post::class,
                    'viewable_id' => $post->id,
                    'views_number' => rand(50, 5000),
                    'last_viewed_at' => now(),
                ]);

                // SortData
                SortData::create([
                    'sortable_type' => Post::class,
                    'sortable_id' => $post->id,
                    'order_number' => $postIndex,
                ]);
            }
        }

        $this->command->info("   تم إنشاء {$postIndex} مقال");
    }

    // ========== 10. ADVERTISEMENTS ==========
    private function seedAdvertisements(): void
    {
        $this->command->info('📢 إنشاء الإعلانات...');

        $ads = [
            ['title' => 'إعلان رئيسي', 'place' => AdvertisementPlaceEnum::MAIN_ADS->value],
            ['title' => 'إعلان وسطي', 'place' => AdvertisementPlaceEnum::MID_ADS->value],
            ['title' => 'إعلان جانبي', 'place' => AdvertisementPlaceEnum::SIDE_ADS->value],
        ];

        foreach ($ads as $ad) {
            Advertisement::firstOrCreate(
                ['title' => $ad['title'], 'lang' => 'ar'],
                [
                    'title' => $ad['title'],
                    'type' => AdvertisementTypeEnum::PHOTO->value,
                    'place' => $ad['place'],
                    'url' => 'https://fimed.ps',
                    'url_target' => AdvertisementUrlTargetEnum::NEW_WINDOW->value,
                    'publish_status' => PublishEnum::PUBLISHED->value,
                    'lang' => 'ar',
                ]
            );
        }
    }
}
