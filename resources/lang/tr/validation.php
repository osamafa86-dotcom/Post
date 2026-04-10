<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Dogrulama Dil Satirlari
    |--------------------------------------------------------------------------
    |
    | Asagidaki dil satirlari, dogrulayici sinifi tarafindan kullanilan
    | varsayilan hata mesajlarini icerir. Bazi kurallarin boyut kurallari
    | gibi birden fazla surumu vardir. Bu mesajlarin her birini burada
    | ayarlayabilirsiniz.
    |
    */

    'accepted' => ':attribute kabul edilmelidir.',
    'active_url' => ':attribute gecerli bir URL degil.',
    'after' => ':attribute :date tarihinden sonraki bir tarih olmalidir.',
    'after_or_equal' => ':attribute :date tarihine esit veya sonraki bir tarih olmalidir.',
    'alpha' => ':attribute yalnizca harflerden olusmalidir.',
    'alpha_dash' => ':attribute yalnizca harfler, rakamlar, tireler ve alt cizgilerden olusmalidir.',
    'alpha_num' => ':attribute yalnizca harfler ve rakamlardan olusmalidir.',
    'array' => ':attribute bir dizi olmalidir.',
    'before' => ':attribute :date tarihinden onceki bir tarih olmalidir.',
    'before_or_equal' => ':attribute :date tarihine esit veya onceki bir tarih olmalidir.',
    'between' => [
        'numeric' => ':attribute :min ile :max arasinda olmalidir.',
        'file' => ':attribute :min ile :max kilobayt arasinda olmalidir.',
        'string' => ':attribute :min ile :max karakter arasinda olmalidir.',
        'array' => ':attribute :min ile :max arasinda oge icermelidir.',
    ],
    'boolean' => ':attribute alani dogru veya yanlis olmalidir.',
    'confirmed' => ':attribute onay eslesmedi.',
    'date' => ':attribute gecerli bir tarih degil.',
    'date_equals' => ':attribute :date tarihine esit bir tarih olmalidir.',
    'date_format' => ':attribute :format bicimi ile eslesmiyor.',
    'different' => ':attribute ve :other birbirinden farkli olmalidir.',
    'digits' => ':attribute :digits basamak olmalidir.',
    'digits_between' => ':attribute :min ile :max basamak arasinda olmalidir.',
    'dimensions' => ':attribute gecersiz gorsel boyutlarina sahip.',
    'distinct' => ':attribute alaninda yinelenen bir deger var.',
    'email' => ':attribute gecerli bir e-posta adresi olmalidir.',
    'ends_with' => ':attribute su degerlerden biriyle bitmeli: :values.',
    'exists' => 'Secilen :attribute gecersiz.',
    'file' => ':attribute bir dosya olmalidir.',
    'filled' => ':attribute alaninin bir degeri olmalidir.',
    'gt' => [
        'numeric' => ':attribute :value degerinden buyuk olmalidir.',
        'file' => ':attribute :value kilobayttan buyuk olmalidir.',
        'string' => ':attribute :value karakterden uzun olmalidir.',
        'array' => ':attribute :value ogeden fazla olmaldir.',
    ],
    'gte' => [
        'numeric' => ':attribute :value degerinden buyuk veya esit olmalidir.',
        'file' => ':attribute :value kilobayttan buyuk veya esit olmalidir.',
        'string' => ':attribute :value karakterden uzun veya esit olmalidir.',
        'array' => ':attribute en az :value oge icermelidir.',
    ],
    'image' => ':attribute bir gorsel olmalidir.',
    'in' => 'Secilen :attribute gecersiz.',
    'in_array' => ':attribute alani :other icinde mevcut degil.',
    'integer' => ':attribute bir tam sayi olmalidir.',
    'ip' => ':attribute gecerli bir IP adresi olmalidir.',
    'ipv4' => ':attribute gecerli bir IPv4 adresi olmalidir.',
    'ipv6' => ':attribute gecerli bir IPv6 adresi olmalidir.',
    'json' => ':attribute gecerli bir JSON dizesi olmalidir.',
    'lt' => [
        'numeric' => ':attribute :value degerinden kucuk olmalidir.',
        'file' => ':attribute :value kilobayttan kucuk olmalidir.',
        'string' => ':attribute :value karakterden kisa olmalidir.',
        'array' => ':attribute :value ogeden az olmaldir.',
    ],
    'lte' => [
        'numeric' => ':attribute :value degerinden kucuk veya esit olmalidir.',
        'file' => ':attribute :value kilobayttan kucuk veya esit olmalidir.',
        'string' => ':attribute :value karakterden kisa veya esit olmalidir.',
        'array' => ':attribute :value ogeden fazla olmamalidir.',
    ],
    'max' => [
        'numeric' => ':attribute :max degerinden buyuk olmamalidir.',
        'file' => ':attribute :max kilobayttan buyuk olmamalidir.',
        'string' => ':attribute :max karakterden uzun olmamalidir.',
        'array' => ':attribute :max ogeden fazla olmamalidir.',
    ],
    'mimes' => ':attribute su dosya turlerinden biri olmalidir: :values.',
    'mimetypes' => ':attribute su dosya turlerinden biri olmalidir: :values.',
    'min' => [
        'numeric' => ':attribute en az :min olmalidir.',
        'file' => ':attribute en az :min kilobayt olmalidir.',
        'string' => ':attribute en az :min karakter olmalidir.',
        'array' => ':attribute en az :min oge icermelidir.',
    ],
    'multiple_of' => ':attribute :value degerinin bir kati olmalidir.',
    'not_in' => 'Secilen :attribute gecersiz.',
    'not_regex' => ':attribute bicimi gecersiz.',
    'numeric' => ':attribute bir sayi olmalidir.',
    'password' => 'Sifre yanlis.',
    'present' => ':attribute alani mevcut olmalidir.',
    'regex' => ':attribute bicimi gecersiz.',
    'required' => ':attribute alani zorunludur.',
    'required_if' => ':other :value oldugunda :attribute alani zorunludur.',
    'required_unless' => ':other :values icinde olmadikca :attribute alani zorunludur.',
    'required_with' => ':values mevcut oldugunda :attribute alani zorunludur.',
    'required_with_all' => ':values mevcut oldugunda :attribute alani zorunludur.',
    'required_without' => ':values mevcut olmadiginda :attribute alani zorunludur.',
    'required_without_all' => ':values degerlerinden hicbiri mevcut olmadiginda :attribute alani zorunludur.',
    'prohibited' => ':attribute alani yasaklanmistir.',
    'prohibited_if' => ':other :value oldugunda :attribute alani yasaklanmistir.',
    'prohibited_unless' => ':other :values icinde olmadikca :attribute alani yasaklanmistir.',
    'same' => ':attribute ve :other eslesmelidir.',
    'size' => [
        'numeric' => ':attribute :size olmalidir.',
        'file' => ':attribute :size kilobayt olmalidir.',
        'string' => ':attribute :size karakter olmalidir.',
        'array' => ':attribute :size oge icermelidir.',
    ],
    'starts_with' => ':attribute su degerlerden biriyle baslamalidir: :values.',
    'string' => ':attribute bir metin olmalidir.',
    'timezone' => ':attribute gecerli bir saat dilimi olmalidir.',
    'unique' => ':attribute zaten alinmis.',
    'uploaded' => ':attribute yuklenemedi.',
    'url' => ':attribute bicimi gecersiz.',
    'uuid' => ':attribute gecerli bir UUID olmalidir.',
    //new
    'invalid_telegram_url' => 'Gecersiz Telegram URL\'si.',
    'publish_role_created' => 'Yayin rolu basariyla olusturuldu!',
    'change_post_status' => 'Yazi durumunu degistir',
    'change_application_status' => 'Basvuru durumunu degistir',
    'event_times_required' => 'En az bir saat girilmelidir',
    'event_times_day_required' => 'Gun secilmelidir',
    'event_times_day_in' => 'Gecerli bir gun secilmelidir',
    'event_times_start_time_required' => 'Baslangic saati girilmelidir',
    'event_times_start_time_date_format' => 'Baslangic saati bicimi dogru olmalidir',
    'event_times_end_time_required' => 'Bitis saati girilmelidir',
    'event_times_end_time_date_format' => 'Bitis saati bicimi dogru olmalidir',
    'event_times_end_time' => 'Bitis saati baslangic saatinden sonra olmalidir',
    'event_date_times_required' => 'En az bir tarih ve saat girilmelidir',
    'event_date_times_start_date_time_required' => 'Baslangic tarihi ve saati girilmelidir',
    'event_date_times_start_date_time_date_format' => 'Baslangic tarihi ve saati bicimi dogru olmalidir',
    'event_date_times_end_date_time_required' => 'Bitis tarihi ve saati girilmelidir',
    'event_date_times_end_date_time_date_format' => 'Bitis tarihi ve saati bicimi dogru olmalidir',
    'event_date_times_end_date_time' => 'Bitis tarihi ve saati baslangic tarihi ve saatinden sonra olmalidir',
    'edit_account_status' => 'Hesap durumunu duzenle',
    'try_later' => 'Lutfen daha sonra tekrar deneyin. Kalan sure: :time saniye.',
    'failed_login_try' => 'Basarisiz giris denemesi, lutfen dikkatli olun',
    'incorrect_login_credentials' => 'Giris bilgileri hatali, tekrar deneyin.',
    'success_email_add' => 'E-posta basariyla eklendi',
    'delete_role' => 'Rolu sil',
    'delete_role_confirmation' => 'Rolu silmek istediginizden emin misiniz?',
    'delete_role_confirmation_with_name' => ':role_name rolu silinecek',
    'no_role_found' => 'Bu isimle bir rol bulunamadi',





    'custom' => [
        'attribute-name' => [
            'rule-name' => 'ozel-mesaj',
        ],
    ],



    'attributes' => [

            'full_name' => 'ad soyad',
            'message' => 'mesaj',
            'subject' => 'konu',

        //posts
        'title'=> 'baslik',
        'slug'=> 'kisa ad',
        'image'=> 'gorsel',
        'description'=> 'aciklama',
        'body'=> 'icerik',
        'publish_date'=> 'yayin tarihi',
        'category_id'=> 'kategori',
        'author_id'=> 'yazar',

        //breaking_news
        'hide_date' => 'gizleme tarihi',
        'url' => 'url',

        //categories
        'category_title' => 'kategori basligi',
        'category_description' => 'kategori aciklamasi',
        'category_type' => 'kategori turu',
        'parent_id' => 'ust kategori',

        //types
        'type_name' => 'tur basligi',

        //special_files
        'file_name' => 'dosya adi',

        //tags
        'tag_name' => 'etiket adi',
        'tags' => 'etiketler',


        //quotes
        'quote_author' => 'yazar',
        'quote_photo' => 'alinti fotografu',
        'quote_from' => 'alinti kaynagi',
        'quote_text' => 'alinti metni',

        //special_pages
        'page_title' => 'sayfa basligi',

        //special_file
        'file_image' => 'dosya gorseli',

        //advertisements
        'type' => 'tur',
        'place' => 'konum',
        'end_hour_time' => 'bitis saati',
        'end_min_time' => 'bitis dakikasi',

        //albums
        'small_image' => 'kucuk gorsel',
        'background_image' => 'arka plan gorseli',

        //podcast_tracks
        'podcast_album_id' => 'podcast albumu',
        'album_id' => 'album',
        //video_tracks
        'video_album_id' => 'video albumu',

        //live_streams
        'end_date' => 'bitis tarihi',

        //users
        'role_id' => 'rol',

        //navbar_links
        'link_name' => 'baglanti adi',
        'link_url' => 'url',
        'link_status' => 'baglanti durumu',
        'link_open' => 'baglanti acilisi',


        'presenter_id' =>'sunucu',

        'icon_path' => 'simge',
        'team_id' => 'takim',

        'image_size' => 'gorsel boyutu',
        'event_image' => 'etkinlik gorseli',
       'date_type' => 'tarih turu',
        'tag_type' => 'etiket turu',

        'reel_title' =>'reel basligi',
        'reel_url' =>'reel url\'si',
        'reel_type' =>'reel turu',

        'file_description' => 'dosya aciklamasi',

        'login' => 'giris',
        'name' => 'ad',
        'username' => 'kullanici adi',
        'email' => 'e-posta',
        'first_name' => 'ad',
        'third_name' => 'dede adi',
        'last_name' => 'soyad',
        'password' => 'sifre',
        'new_password' => 'yeni sifre',
        'password_confirmation' => 'sifre onay',
        'city' => 'sehir',
        'country' => 'ulke',
        'address' => 'adres',
        'phone' => 'telefon',
        'mobile' => 'cep telefonu',
        'age' => 'yas',
        'sex' => 'cinsiyet',
        'gender' => 'cinsiyet',
        'answers' => 'cevaplar',
        'day' => 'gun',
        'month' => 'ay',
        'year' => 'yil',
        'hour' => 'saat',
        'minute' => 'dakika',
        'second' => 'saniye',
        'content' => 'icerik',
        'excerpt' => 'ozet',
        'date' => 'tarih',
        'time' => 'saat',
        'available' => 'mevcut',
        'size' => 'boyut',
        'price' => 'fiyat',
        'desc' => 'aciklama',
        'q' => 'arama',
        'link' => ' ',


            'social_media_name' => 'Lutfen bir ad girin',
            'social_media_link' => 'Lutfen baglanti girin',
            'social_media_icon' => 'Lutfen simge girin',
            'links_title' => 'Lutfen baslik girin',
            'links_url' => 'Lutfen url girin',
            'links_icon' => 'Lutfen simge girin',

        'button_title' => 'buton basligi',
        'service_image' => 'hizmet gorseli',
        'model' =>  'model',
        'action' => 'islem',
        'permission_target_id' =>  'izin hedefi',
        'fromId' => 'kaynak',
        'toId' => 'hedef',
        'light_site_name' => 'ana sayfa site aciklamasi',

    ],

];
