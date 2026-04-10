<?php

namespace Database\Seeders;

use App\Enums\CategoryTypeEnum;
use App\Enums\ImageSizeTypeEnum;
use App\Enums\NewsTypeEnum;
use App\Enums\ParticipantTypeEnum;
use App\Enums\PublishEnum;
use App\Enums\TagTypeEnum;
use App\Models\Category;
use App\Models\Files;
use App\Models\ModelHasFile;
use App\Models\NavbarLink;
use App\Models\Participant;
use App\Models\Post;
use App\Models\PostRelation;
use App\Models\SortData;
use App\Models\Tag;
use App\Models\User;
use App\Models\View;
use App\Enums\LinkStatusEnum;
use App\Enums\LinkUrlTargetEnum;
use App\Models\Icon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class FimedTurkishSeeder extends Seeder
{
    private array $imageFileIds = [];
    private array $categoryIds = [];
    private array $authorIds = [];
    private array $tagIds = [];

    public function run(): void
    {
        $this->command->info('🇹🇷 Türkçe veriler oluşturuluyor...');

        $this->imageFileIds = Files::whereIn('extension', ['jpg', 'jpeg', 'png', 'webp'])->pluck('id')->toArray();

        if (empty($this->imageFileIds)) {
            $this->command->error('Resim dosyası bulunamadı! Önce FimedSeeder çalıştırın.');
            return;
        }

        $this->seedNavbarLinks();
        $this->seedCategories();
        $this->seedTags();
        $this->seedParticipants();
        $this->seedPosts();

        $this->command->info('✅ Türkçe veriler başarıyla oluşturuldu!');
    }

    private function seedNavbarLinks(): void
    {
        $this->command->info('🧭 Navigasyon bağlantıları...');

        // Delete old Turkish nav links
        NavbarLink::where('lang', 'tr')->delete();

        $defaultIcon = Icon::first()?->id;
        $links = [
            ['name' => 'Filistin', 'url' => '/category_news/Filistin'],
            ['name' => 'Kudüs', 'url' => '/category_news/Kudüs'],
            ['name' => 'Türkiye', 'url' => '/category_news/Türkiye'],
            ['name' => 'Arap Dünyası', 'url' => '/category_news/Arap Dünyası'],
            ['name' => 'Türkiye\'deki Filistinliler', 'url' => '/category_news/Türkiye\'deki Filistinliler'],
            ['name' => 'Raporlar', 'url' => '/category_news/Raporlar'],
            ['name' => 'Filistin\'in Sesi', 'url' => '/category_news/Filistin\'in Sesi'],
            ['name' => 'Görüşler', 'url' => '/all_posts'],
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
                'lang' => 'tr',
            ]);
        }
    }

    private function seedCategories(): void
    {
        $this->command->info('📂 Kategoriler...');

        $categories = [
            'Filistin' => 'Filistin haberleri ve gelişmeler',
            'Kudüs' => 'Kudüs ve Mescid-i Aksa haberleri',
            'Türkiye' => 'Türkiye gündem ve politika',
            'Arap Dünyası' => 'Arap dünyasından haberler',
            'Türkiye\'deki Filistinliler' => 'Türkiye\'de yaşayan Filistinlilerin haberleri',
            'Raporlar' => 'Araştırma raporları ve analizler',
            'Filistin\'in Sesi' => 'Tanıklıklar, röportajlar ve hikayeler',
            'Görüşler' => 'Köşe yazıları ve makaleler',
        ];

        foreach ($categories as $i => $desc) {
            $name = is_int($i) ? $desc : $i;
            $description = is_int($i) ? '' : $desc;

            $cat = Category::firstOrCreate(
                ['category_title' => $name, 'lang' => 'tr'],
                [
                    'category_title' => $name,
                    'category_description' => $description,
                    'category_type' => CategoryTypeEnum::NEWS->value,
                    'show_index' => 1,
                    'order' => count($this->categoryIds) + 1,
                    'lang' => 'tr',
                ]
            );

            $this->categoryIds[$name] = $cat->id;

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

    private function seedTags(): void
    {
        $this->command->info('🏷️ Etiketler...');

        $tags = [
            'Filistin Davası', 'Mescid-i Aksa', 'İşgal', 'Direniş',
            'Gazze', 'Batı Şeria', 'Mülteciler', 'İşgal Altındaki Kudüs',
            'Geri Dönüş Hakkı', 'İstanbul', 'Türkiye', 'Medya',
        ];

        foreach ($tags as $name) {
            $tag = Tag::firstOrCreate(
                ['tag_name' => $name, 'lang' => 'tr'],
                [
                    'tag_name' => $name,
                    'tag_type' => TagTypeEnum::NEWS->value,
                    'lang' => 'tr',
                ]
            );
            $this->tagIds[] = $tag->id;
        }
    }

    private function seedParticipants(): void
    {
        $this->command->info('✍️ Yazarlar...');

        $authors = [
            ['name' => 'Ahmet Yılmaz', 'work' => 'Siyasi Analist', 'desc' => 'Filistin meselesi ve uluslararası ilişkiler konusunda uzmanlaşmış siyasi analist. Çeşitli Türk ve Arap medya platformlarında düzenli olarak yazılar yayımlamaktadır.'],
            ['name' => 'Zeynep Kaya', 'work' => 'Araştırmacı Gazeteci', 'desc' => 'İnsan hakları ve Filistin meselesi konularında uzmanlaşmış ödüllü araştırmacı gazeteci. Birçok uluslararası gazetecilik ödülünün sahibidir.'],
            ['name' => 'Mehmet Demir', 'work' => 'Köşe Yazarı', 'desc' => 'Türk-Arap ilişkileri ve bölgesel politika konularında uzman köşe yazarı. 20 yılı aşkın gazetecilik deneyimine sahiptir.'],
            ['name' => 'Ayşe Özkan', 'work' => 'Akademisyen', 'desc' => 'İstanbul Üniversitesi\'nde Ortadoğu Çalışmaları bölümünde öğretim üyesi. Kudüs tarihi ve İslami çalışmalar konusunda uzman.'],
            ['name' => 'Mustafa Şahin', 'work' => 'Saha Muhabiri', 'desc' => 'Olayları yerinden takip eden saha muhabiri. Birçok sıcak bölgede görev yapmış, özel raporları ile tanınmaktadır.'],
            ['name' => 'Fatma Yıldız', 'work' => 'Editör', 'desc' => 'FiMED\'de editör olarak görev yapmaktadır. Medya ve gazetecilik alanında 15 yılı aşkın deneyime sahiptir.'],
            ['name' => 'Ali Çelik', 'work' => 'Tarihçi Yazar', 'desc' => 'Türkiye\'de yaşayan Filistinli tarihçi yazar. Filistin anlatısının ve kültürel mirasın belgelenmesi konusunda çalışmalar yapmaktadır.'],
            ['name' => 'Elif Arslan', 'work' => 'Araştırmacı', 'desc' => 'Mülteci hakları ve toplumsal kalkınma alanında çalışan araştırmacı. Birçok sivil toplum kuruluşuyla işbirliği yapmaktadır.'],
        ];

        foreach ($authors as $i => $data) {
            $author = Participant::firstOrCreate(
                ['name' => $data['name'], 'lang' => 'tr'],
                [
                    'name' => $data['name'],
                    'work' => $data['work'],
                    'description' => $data['desc'],
                    'type' => ParticipantTypeEnum::AUTHORS->value,
                    'lang' => 'tr',
                ]
            );

            $this->authorIds[] = $author->id;

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

    private function seedPosts(): void
    {
        $this->command->info('📰 Makaleler oluşturuluyor...');

        $userId = User::first()?->id ?? 1;
        $postIndex = 0;

        $titles = [
            'Filistin' => [
                'Gazze\'de yeni gelişmeler: İsrail saldırıları devam ediyor',
                'Birleşmiş Milletler Filistin\'de acil ateşkes çağrısında bulundu',
                'Dünya başkentlerinde Filistin\'e destek gösterileri düzenlendi',
                'BM raporu: İşgalin Filistinli sivillere yönelik ihlalleri belgelendi',
                'Gazze\'nin yeniden inşası için uluslararası yardım girişimi başlatıldı',
            ],
            'Kudüs' => [
                'İşgal güçlerinin korumasında yerleşimciler Mescid-i Aksa\'ya baskın düzenledi',
                'Kudüslüler Şeyh Cerrah\'taki Yahudileştirme planlarına karşı direniyor',
                'Kudüs: Uluslararası sessizlik ortasında zorla yerinden etme politikaları tırmanıyor',
                'Mescid-i Aksa Cuma namazında binlerce Müslüman\'ı ağırladı',
            ],
            'Türkiye' => [
                'Türkiye BM Güvenlik Konseyi\'nde Filistin konusundaki kararlı tutumunu teyit etti',
                'Türk-Arap ilişkilerinde ekonomik alanda kayda değer gelişme yaşanıyor',
                'İstanbul Filistin meselesinin geleceğine ilişkin uluslararası konferansa ev sahipliği yaptı',
                'Türk ekonomisi küresel zorluklara rağmen pozitif büyüme kaydetti',
                'Türkiye\'den Gazze\'ye insani yardım köprüsü: Yeni yardım konvoyu yola çıktı',
            ],
            'Arap Dünyası' => [
                'Filistin ve bölgedeki gelişmeleri ele almak üzere olağanüstü Arap zirvesi toplandı',
                'Mısır Gazze\'de ateşkes sağlanması için yoğun çaba harcıyor',
                'Ürdün İsrail\'in Kudüs\'teki tırmanmasının sonuçları konusunda uyardı',
                'Arap Birliği Filistin halkıyla dayanışma çağrısını yineledi',
            ],
            'Türkiye\'deki Filistinliler' => [
                'Türkiye\'deki Filistin diasporası Gazze ile dayanışma yürüyüşü düzenledi',
                'Türkiye\'deki Filistinli öğrenciler akademik başarılarla öne çıkıyor',
                'İstanbul\'da toplumsal dayanışmayı güçlendiren Filistinli sivil toplum girişimleri',
                'Türkiye\'deki Filistinliler Filistin kültür mirası sergisi açtı',
            ],
            'Raporlar' => [
                'Özel rapor: Nakba\'dan on yıllar sonra Filistinli mülteciler kamplarda nasıl yaşıyor?',
                'Araştırma: İsrail yerleşim faaliyetlerinin şüpheli finansman ağları',
                'Rapor: Savaş altında eğitim - Gazze\'nin çocukları bombardıman altında nasıl öğreniyor?',
                'Saha raporu: El koyma politikalarına karşı Filistin tarımı',
                'Araştırma: İşgal hapishanelerinde Filistinli esirlerin acı çektikleri koşullar',
            ],
            'Filistin\'in Sesi' => [
                'Tanıklık: Katliam mağduru bombardıman gecesinin detaylarını anlattı',
                'Serbest bırakılan esirle röportaj: Özgürlük insanın en değerli varlığıdır',
                'Direniş hikayesi: Filistinli aile evini dördüncü kez yeniden inşa etti',
                'Tanıklık: Gazze\'den tahliye edilen bir doktorun anlatımı',
            ],
            'Görüşler' => [
                'Filistin meselesi uluslararası hukuk terazisinde - Analitik bir okuma',
                'Filistin medyasının geleceği: Dijital çağda zorluklar ve fırsatlar',
                'Filistinli gençlerin ulusal ve kültürel kimliğin güçlendirilmesindeki rolü',
                'Osmanlı\'dan günümüze Türkiye-Filistin ilişkilerinin tarihi derinliği',
            ],
        ];

        $descriptions = [
            'Uzmanların görüşleri ve gelecek tahminleriyle birlikte en son gelişmelerin kapsamlı ve ayrıntılı analizi.',
            'Öne çıkan noktaları ve ayrıntıları inceleyen, özel kaynaklardan doğrulanmış bilgiler sunan detaylı rapor.',
            'Bu dosyadaki mevcut zorluklar için gerçekleri ortaya koyan ve yenilikçi çözümler sunan derinlemesine araştırma.',
            'Resmin tamamının anlaşılmasına yardımcı olan veri ve istatistiklerle birlikte en son gelişmelerin kapsamlı haberi.',
            'Belgelenmiş kanıt ve delillere dayanan, sahada uygulanabilir pratik öneriler sunan analiz çalışması.',
        ];

        $body = '<p class="article-body__lead">Filistin dosyasındaki hızlı gelişmeler ışığında, son gelişmeleri takip etmek ve farklı boyutlarını analiz etmek zorunlu hale gelmiştir. Bu rapor, kamuoyunu meşgul eden en önemli noktalara ışık tutmaktadır.</p>'
            . '<h2>Genel Bağlam</h2>'
            . '<p>Mevcut veriler, siyasi ve askeri sahnede önemli dönüşümlerin yaşandığına işaret etmektedir. Farklı taraflar gelişmelere çeşitli şekillerde tepki vermektedir. Uzmanlar, önümüzdeki dönemin olayların seyrini belirlemede kritik olacağını değerlendirmektedir.</p>'
            . '<p>Doğru veri ve istatistikler aracılığıyla, mevcut zorlukların ve fırsatların tam resmini anlayabiliriz. Bu analiz, alanlarında tanınmış güvenilir kaynaklara ve uzman görüşlerine dayanmaktadır.</p>'
            . '<blockquote class="article-blockquote"><div class="article-blockquote__icon"><i class="fas fa-quote-right"></i></div><div class="article-blockquote__content"><p>Gerçek ne kadar gizlenmeye çalışılsa da saklanamaz ve adalet ne kadar gecikse de mutlaka gelecektir. Filistin halkı dünyaya yaşam iradesinin tüm öldürme ve yıkım makinelerinden daha güçlü olduğunu kanıtlamıştır.</p></div></blockquote>'
            . '<h2>Analiz ve Takip</h2>'
            . '<p>Uluslararası toplumun Filistin meselesine yönelik daha fazla harekete geçtiği görülmektedir, ancak bu hareketler henüz gereken düzeyde değildir. İhlallere son verecek kararlı bir uluslararası tutum gerekmektedir.</p>'
            . '<p>Bu bağlamda, gerçeğin aktarılması ve sahada yaşananların belgelenmesinde medyanın rolü ön plana çıkmaktadır. Filistin ve Arap medyası bu konularda daha fazla profesyonellik ve cesaret göstermekle yükümlüdür.</p>'
            . '<ul class="article-list"><li>Saha gelişmelerinin sürekli takibi</li><li>Uzman analistlerden derinlemesine değerlendirmeler</li><li>Olayların merkezinden özel raporlar</li><li>İnsani tanıklık ve anlatımların belgelenmesi</li></ul>'
            . '<p>Biz FiMED olarak gerçeği dürüstlük ve profesyonellikle aktarmaya kendimizi adamış bulunuyoruz. Filistin halkının davası ve fedakarlıklarına yaraşır bir medya içeriği sunmak için çalışıyoruz.</p>';

        foreach ($titles as $catName => $catTitles) {
            $catId = $this->categoryIds[$catName] ?? null;
            if (!$catId) continue;

            foreach ($catTitles as $title) {
                $postIndex++;
                $isMain = $postIndex <= 2;

                $post = Post::create([
                    'title' => $title,
                    'slug' => Str::slug($title) . '-tr-' . $postIndex,
                    'description' => $descriptions[array_rand($descriptions)],
                    'body' => $body,
                    'image_alt' => 'Açıklayıcı görsel - ' . Str::limit($title, 50),
                    'publish_date' => now()->subHours(rand(1, 720)),
                    'order_number' => $postIndex,
                    'news_type' => $isMain ? NewsTypeEnum::MAIN_NEWS->value : NewsTypeEnum::SUB_NEWS->value,
                    'image_size' => ImageSizeTypeEnum::LARGE_IMAGE->value,
                    'publish_status' => PublishEnum::PUBLISHED->value,
                    'user_id' => $userId,
                    'lang' => 'tr',
                ]);

                // Image
                $post->files()->create([
                    'model_column' => 'main_image',
                    'model_id' => $post->id,
                    'file_id' => $this->imageFileIds[array_rand($this->imageFileIds)],
                    'model_type' => Post::class,
                ]);

                // Category
                PostRelation::create([
                    'post_id' => $post->id,
                    'relationable_id' => $catId,
                    'relationable_type' => Category::class,
                    'relationable_is_main' => true,
                ]);

                // Author
                PostRelation::create([
                    'post_id' => $post->id,
                    'relationable_id' => $this->authorIds[array_rand($this->authorIds)],
                    'relationable_type' => Participant::class,
                    'relationable_is_main' => true,
                ]);

                // Tags
                $tagSample = array_rand($this->tagIds, min(3, count($this->tagIds)));
                foreach ((array) $tagSample as $ti => $tagKey) {
                    PostRelation::create([
                        'post_id' => $post->id,
                        'relationable_id' => $this->tagIds[$tagKey],
                        'relationable_type' => Tag::class,
                        'relationable_is_main' => $ti === 0,
                    ]);
                }

                // Views
                View::create([
                    'viewable_type' => Post::class,
                    'viewable_id' => $post->id,
                    'views_number' => rand(50, 5000),
                    'last_viewed_at' => now(),
                ]);

                // Sort
                SortData::create([
                    'sortable_type' => Post::class,
                    'sortable_id' => $post->id,
                    'order_number' => 1000 + $postIndex,
                ]);
            }
        }

        $this->command->info("   {$postIndex} makale oluşturuldu");
    }
}
