<?php
return [
    'default' => [
        '1' => 'Evet',
        '2' => 'Hayir',
        'undefined' => 'Tanimsiz',
    ],
    'models' =>[
        '1' => 'Materyal',
        '2' => 'Yazi',
        '3' => 'Katilimci',
        '4' => 'Kategori',
        '5' => 'Podcast Albumu',
        '6' => 'Video Albumu',
        '7' => 'Ayar',
        '8' => 'Son Dakika Haberi',
        '9' => 'Tur',
        '10' => 'Ozel Dosya',
        '11' => 'Etiket',
        '12' => 'Alinti',
        '13' => 'Ozel Sayfa',
        '14' => 'Reklam',
        '15' => 'Podcast Parcasi',
        '16' => 'Video Parcasi',
        '17' => 'Uyari',
        '18' => 'Kullanici',
        '19' => 'Bize Ulasin',
        '20' => 'Haber Gonder',
    ],
    'field' => 'Alan',
    'value' => 'Alan Verisi',
    'fields' => [
        'Post' => [
            'title' => 'Baslik',
            'description' => 'Aciklama',
            'body' => 'Icerik',
            'publish_date' => 'Yayin Tarihi',

        ],
        'Category' => [
            'category_title' => 'Kategori Basligi',
            'category_description' => 'Kategori Aciklamasi',
        ],
        'Author' => [
            'author_name' => 'Yazar Adi',
            'author_work' => 'Yazar Isi',
            'author_facebook' => 'Yazar Facebook',
            'author_twitter' => 'Yazar Twitter',
            'author_google_plus' => 'Yazar Google Plus',
            'author_description' => 'Yazar Aciklamasi',
        ],
        'BreakingNews' => [
            'title' => 'Haber Basligi',
            'url' => 'Haber URL\'si',
            'hide_date' => 'Sona Erme Suresi (Saat)',
        ],
        'Type' => [
            'type_name' => 'Haber Turu Basligi',
        ],
        'SpecialFile' => [
            'file_name' => 'Dosya Basligi',
        ],
        'Tag' => [
            'tag_name' => 'Etiket Basligi',
        ],
        'Quote' => [
            'quote_author' => 'Yazar Adi',
            'quote_from' => 'Alinti Platformu',
            'quote_text' => 'Alinti Metni',
        ],
        'SpecialPage' => [
            'page_title' => 'Baslik',
            'page_content' => 'Detaylar',
        ],
        'Advertisement' => [
            'title' => 'Baslik',
            'place' => 'Konum',
            'type' => 'Tur',
            'url' => 'URL',
            'url_target' => 'URL nasil acilsin?',
            'end_hour_time' => 'Bitis Saati',
            'end_min_time' => 'Bitis Dakikasi',
        ],
        'PodcastAlbum' => [
            'title' => 'Podcast Albumu Basligi',
            'description' => 'Podcast Albumu Aciklamasi',
        ],
        'PodcastTrack' => [
            'title' => 'Podcast Parcasi Basligi',
            'description' => 'Podcast Parcasi Aciklamasi',
            'url' => 'URL',
            'podcast_album_id' => 'Album',
        ],
        'VideoAlbum' => [
            'title' => 'Video Albumu Basligi',
            'description' => 'Video Albumu Aciklamasi',
        ],
        'VideoTrack' => [
            'title' => 'Video Basligi',
            'description' => 'Video Aciklamasi',
            'url' => 'Video URL\'si',
            'video_album_id' => 'Album',
        ],
        'LiveStream' => [
            'title' => 'Canli Yayin Basligi',
            'url' => 'Canli Yayin URL\'si',
            'end_date' => 'Bitis Zamani',
        ],
        'User' => [
            'first_name' => 'Ad',
            'last_name' => 'Soyad',
            'email' => 'E-posta',
            'role_id' => 'Kullanici Durumu',
        ],
    ],
    'actions' => [
        '1' => 'Olustur',
        '2' => 'Duzenle',
        '3' => 'Beklet',
        '4' => 'Sil',
        '5' => 'Yazi Durumunu Degistir',

    ],

    'actionablePublish' => [
        '1' => 'Inceleme Altinda',
        '2' => 'Yayinda',
        '3' => 'Taslak',
        '4' => 'Reddedildi',
        '5' => 'Duzenleme Gerekli',
    ],

    'advertisementPlace' => [
        '1' => 'Yan Reklam',
        '2' => 'Ana Reklam',
        '3' => 'Orta Reklam',
        '4' => 'Alt Reklam',
    ],

    'advertisementType' => [
        '1' => 'Fotograf',
        '2' => 'Kod',
    ],

    'advertisementUrlTarget' => [
        '1' => 'Ayni Pencere',
        '2' => 'Yeni Pencere',
    ],

    'alertType' => [
        '1' => 'Tehlike',
        '2' => 'Basari',
        'status' =>[
            '1' => 'tehlike',
            '2' => 'basari',
        ],
    ],

    'categoryStyle' => [
        '1' => 'Bir buyuk ve dort kucuk stili',
        '2' => 'Dort buyuk, diger kucuk stili',
        '3' => 'Bir buyuk ve iki orta stili',
        '4' => 'Uc orta alt listeli stili',
        '5' => 'Uc podcast stili',
        '6' => 'Bir buyuk video ve uc orta',
        '7' => 'Iki buyuk ve dort orta stili',
        '8' => 'Dort orta stili',
        '9' => 'Alti orta yatay stili',
        '10' => 'Uc orta dikey stili',
        '11' => 'Bir uzun ve alti alt stili',
        '12' => 'Alti kaydirici stili',
        '13' => 'Bir uzun ve dort alt stili',
        '14' => 'Uc orta uc kucuk alt stili',
        '15' => 'Bir buyuk uc orta stili',
        '16' => 'Bir buyuk dort orta video',
        '17' => 'Bir orta uc alt stili',
        '18' => 'Uc orta kaydirici ile',
        '19' => 'Iki buyuk uc orta stili',
        '20' => 'Bir buyuk dort orta stili',
        '21' => 'Bir buyuk, bir genis, iki orta stili',
        '22' => 'Bir orta dikey kaydirici ile',
        '23' => 'Dort orta alt listeli',
        '24' => 'Bir buyuk dort alt stili',
        '25' => 'Bir buyuk video uc alt stili',
        '26' => 'Uc orta kaydirici stili',
        '27' => 'Uc orta karanlik kaydirici stili',
        '28' => 'Iki kategori stili',
        '29' => 'Uc alt, bir orta, dort alt stili',
        '30' => 'Dort orta, bir kaydirici, iki alt stili',
        '31' => 'Sekiz orta sekiz kucuk stili',
        '32' => 'Bir orta kaydirici uc alt',
        '33' => 'Bir orta uc alt',
        '34' => 'FiMED: Raporlar (1 büyük + 4 liste)',
        '35' => 'FiMED: Koyu bölüm (3 kart)',
        '36' => 'FiMED: Kart ızgarası (4 renkli kart)',
        '37' => 'FiMED: Görüşler (3 yazar kartı)',
    ],

    'CategoryTypeEnum' => [
        '1' => 'Haberler',
        '2' => 'Liste',
        '3' => 'Sayfalar',
        '4' => 'Podcast',
        '5' => 'Video',
        '6' => 'Gorsel',
        '7' => 'Etkinlikler',
        '8' => 'Kullanicilar',
        '9' => 'Kurslar',
        '10' => 'Gonulluluk',
        '11' => 'Konferans',
    ],

    'dataPage' => [
        '1' => 'Kod',
        '2' => 'Dosya',
        '3' => 'Firsat',
        '4' => 'Burs',
    ],

    'dateType' => [
        '1' => 'Saat',
        '2' => 'Tarih ve Saat',
    ],

    'day' => [
        '1' => 'Cumartesi',
        '2' => 'Pazar',
        '3' => 'Pazartesi',
        '4' => 'Sali',
        '5' => 'Carsamba',
        '6' => 'Persembe',
        '7' => 'Cuma',
    ],

    'gender' => [
        '1' => 'Erkek',
        '2' => 'Kadin',
    ],

    'imageSizeType' => [
        '1' => 'Buyuk Gorsel',
        '2' => 'Orta Gorsel',
        '3' => 'Kucuk Gorsel',
        '4' => 'Kapak Makalesi',
        '5' => 'Yan Yazi',
    ],

    'linkPosition' => [
        '1' => 'Ust Bilgi',
        '2' => 'Alt Bilgi',
        '3' => 'Ust ve Alt Bilgi',
        '4' => 'Tum Konumlar',
    ],

    'linkStatus' => [
        '1' => 'Kategori',
        '2' => 'Etiket',
        '3' => 'Podcast',
        '4' => 'Video',
        '5' => 'Ozel Sayfa',
        '6' => 'Ozel Baglanti',
        '7' => 'Tur',
    ],

    'linkUrlTarget' => [
        '1' => 'Ayni Pencere',
        '2' => 'Yeni Pencere',
    ],

    'materialType' => [
        '1' => 'Podcast veya Ses',
        '2' => 'Video',
        '3' => 'Gorsel',
    ],

    'newsType' => [
        '1' => 'Ana Haber',
        '2' => 'Alt Haber',
    ],

    'notificationType' => [
        '1' => 'Bildirim',
        '2' => 'Etkinlik',
    ],

    'participantType' => [
        '1' => 'Yazarlar',
        '2' => 'Sunucular',
        '3' => 'Konuklar',
        '4' => 'Musteriler',
        '5' => 'Ortaklar',
        '6' => 'Takim',
        '7' => 'Destekleyenler',
        '8' => 'Yayincilar',
        '9' => 'Kaynaklar',
    ],

    'publish' => [
        '1' => 'Yayinda',
        '2' => 'Taslak',
        '3' => 'Beklemede',
    ],

    'reelType' => [
        '1' => 'YouTube',
        '2' => 'Instagram',
        '3' => 'Yerel',
    ],

    'subscriptionPaymentMethod' => [
        '1' => 'PayPal',
        '2' => 'Kredi Karti',
        '3' => 'Nakit',
    ],

    'subscriptionStatus' => [
        '1' => 'Aktif',
        '2' => 'Pasif',
    ],

    'subscriptionType' => [
        '1' => 'Aylik',
        '2' => 'Yillik',
    ],

    'tagType' => [
        '1' => 'Haberler',
        '2' => 'Ilgi Alanlari',
        '3' => 'Etkinlikler',
        '4' => 'Materyaller',
        '5' => 'Beceriler',
        '6' => 'Uzmanlik',
        '7' => 'Kullanicilar',
        '8' => 'Diller',
        '9' => 'Lehceler',
    ],

    'userDetailsType' => [
        '1' => 'icerik_uretici',
        '2' => 'hongora_takimi',
    ],

    'userStatus' => [
        '1' => 'Yonetici',
        '2' => 'Veri Girisi',
        '3' => 'Editorr',
        '4' => 'Seslendirme',
        '5' => 'Abone',
    ],

    'videoType' => [
        '1' => 'youtube',
        '2' => 'telegram',
        '3' => 'yerel',
    ],

    'waterMark' => [
        '1' => 'SOL_UST',
        '2' => 'SAG_UST',
        '3' => 'ORTA',
        '4' => 'SOL_ALT',
        '5' => 'SAG_ALT',
        '6' => 'TANIMSIZ',
    ],

];
