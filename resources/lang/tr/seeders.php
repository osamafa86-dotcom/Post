<?php
return [
    'alerts' => [
        'security_alert' => [
            'title' => 'Guvenlik Uyarisi',
            'message' => 'Yetkisiz bir giris denemesi tespit edildi.',
        ],
        'operation_success' => [
            'title' => 'Islem Basarili',
            'message' => 'Veriler basariyla kaydedildi.',
        ],
        'maintenance_notice' => [
            'title' => 'Bakim Bildirimi',
            'message' => 'Sunucu bakimi gece yarisi yapilacaktir.',
        ],
        'process_completed' => [
            'title' => 'Islem Tamamlandi',
            'message' => 'Tum arka plan isleri basariyla tamamlandi.',
        ],
    ],
    'categories' => [
        'words' => [
            'Ozgurluk', 'Baris', 'Umut', 'Yaraticilik', 'Kultur', 'Teknoloji', 'Egitim', 'Adalet', 'Kalkinma', 'Saglik',
            'Gelecek', 'Ilerleme', 'Basari', 'Guc', 'Irade', 'Tutku', 'Sorumluluk', 'Durustluk', 'Millet', 'Dava',
            'Yenilik', 'Ekonomi', 'Bilim', 'Bilgi', 'Yasam', 'Deger', 'Ahlak', 'Medya', 'Enformasyon', 'Toplum',
            'Dayanisma', 'Katilim', 'Isbirligi', 'Cevre', 'Sistem', 'Tarih', 'Kimlik', 'Ilke', 'Fedakarlik', 'Esitlik',
            'Hukuk', 'Vatandaslik', 'Haklar', 'Reform', 'Uretim', 'Yaraticilik', 'Deneyim', 'Profesyonellik', 'Guvenilirlik', 'Iletisim',
        ],
    ],
    'contacts' => [

        'subjects' => [
            'inquiry' => 'Bilgi Talebi',
            'support' => 'Destek',
            'suggestion' => 'Oneri',
        ],
        'messages' => [
            'inquiry' => 'Site ozellikleri hakkinda daha fazla bilgi almak istiyorum.',
            'support' => 'Gorsel yuklemede sorun yasiyorum.',
            'suggestion' => 'Podcast bolumu eklenmesini oneriyorum.',
        ],
    ],
    'events' => [
        [
            'title' => 'Haftalik Teknoloji Bulusmasi',
            'url' => 'https://example.com/events/weekly-tech',
            'description' => 'Teknolojideki son gelismeleri tartismak icin haftalik bulusma.',
            'date_type' => \App\Enums\DateTypeEnum::TIME->value,
        ],
        [
            'title' => 'Yillik Konferans 2025',
            'url' => 'https://example.com/events/conf-2025',
            'description' => 'Acilis konusmalari ve calistaylarla yillik konferansimiz.',
            'date_type' => \App\Enums\DateTypeEnum::DATE_TIME->value,
        ],
    ],

    'live_streams' => [
        [
            'title' => 'Sabah Programi Canli',
            'url' => 'https://youtube.com/live/abcdef',
            'hours' => 2,
        ],
        [
            'title' => 'Aksam Haberleri Canli',
            'url' => 'https://facebook.com/live/123456',
            'hours' => 3,
        ],
    ],
    'navbar' => [
        'top_links' => [
            [
                'link_order' => 1,
                'link_name' => 'Ana Sayfa',
                'link_url' => '/',
                'icon_id' => null,
                'link_status' => 6, // CUSTOM_LINK
                'link_open' => 1,   // SAME_WINDOW
            ],
            [
                'link_order' => 2,
                'link_name' => 'Haberler',
                'link_url' => '/news',
                'icon_id' => null,
                'link_status' => 5, // SPECIAL_PAGE
                'link_open' => 1,   // SAME_WINDOW
            ],
            [
                'link_order' => 3,
                'link_name' => 'Podcast',
                'link_url' => '/podcasts',
                'icon_id' => null,
                'link_status' => 3, // PODCAST
                'link_open' => 2,   // NEW_WINDOW
            ],
            [
                'link_order' => 4,
                'link_name' => 'Video',
                'link_url' => '/videos',
                'icon_id' => null,
                'link_status' => 4, // VIDEO
                'link_open' => 1,   // SAME_WINDOW
            ],
        ],
        'news_children' => [
            [
                'link_order' => 1,
                'link_name' => 'Son Dakika',
                'link_url' => '/news/breaking',
                'icon_id' => null,
                'link_status' => 2, // TAG
                'link_open' => 2,   // NEW_WINDOW
            ],
            [
                'link_order' => 2,
                'link_name' => 'Siyaset',
                'link_url' => '/news/politics',
                'icon_id' => null,
                'link_status' => 1, // CATEGORY
                'link_open' => 1,   // SAME_WINDOW
            ],
        ],
    ],
    'posts' => [
    'titles' => [
        'Son Dakika: Davada yeni gelismeler',
        'Hukumet onemli bir karar aldi',
        'Son olaylara iliskin analiz yazisi',
        'Bu haftanin onemli olaylarini kesfedin',
        'Ekonomi dikkate deger bir iyilesme gosteriyor',
    ],
    ],
    'video_albums' => [
    [
        'title' => 'Belgeseller',
        'small_image' => 'images/videos/docs_sm.jpg',
        'background_image' => 'images/videos/docs_bg.jpg',
        'description' => 'Cesitli konularda bilgilendirici belgeseller.',
    ],
    [
        'title' => 'Roportajlar',
        'small_image' => 'images/videos/interviews_sm.jpg',
        'background_image' => 'images/videos/interviews_bg.jpg',
        'description' => 'Sektor liderleriyle derinlemesine roportajlar.',
    ],
    ],
];
