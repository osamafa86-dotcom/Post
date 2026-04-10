<?php
return [
    'alerts' => [
        'security_alert' => [
            'title' => 'تنبيه أمني',
            'message' => 'تم رصد محاولة دخول غير مصرح بها.',
        ],
        'operation_success' => [
            'title' => 'نجاح العملية',
            'message' => 'تم حفظ البيانات بنجاح.',
        ],
        'maintenance_notice' => [
            'title' => 'تنبيه صيانة',
            'message' => 'سيتم إجراء صيانة على الخادم منتصف الليل.',
        ],
        'process_completed' => [
            'title' => 'اكتملت العملية',
            'message' => 'تم إنهاء جميع العمليات الخلفية بنجاح.',
        ],
    ],
    'categories' => [
        'words' => [
            'حرية', 'سلام', 'أمل', 'إبداع', 'ثقافة', 'تكنولوجيا', 'تعليم', 'عدالة', 'تنمية', 'صحة',
            'مستقبل', 'تطور', 'نجاح', 'قوة', 'إرادة', 'طموح', 'مسؤولية', 'أمانة', 'وطن', 'قضية',
            'ابتكار', 'اقتصاد', 'علم', 'معرفة', 'حياة', 'قيمة', 'أخلاق', 'إعلام', 'معلومة', 'مجتمع',
            'تضامن', 'مشاركة', 'تعاون', 'بيئة', 'نظام', 'تاريخ', 'هوية', 'مبدأ', 'عطاء', 'مساواة',
            'قانون', 'مواطنة', 'حقوق', 'إصلاح', 'إنتاج', 'ابداعية', 'خبرة', 'احتراف', 'مصداقية', 'تواصل',
        ],
    ],
    'contacts' => [
        'subjects' => [
            'inquiry' => 'استفسار',
            'support' => 'الدعم',
            'suggestion' => 'اقتراح',
        ],
        'messages' => [
            'inquiry' => 'أرغب بمعرفة المزيد عن خصائص الموقع.',
            'support' => 'أواجه مشكلة في رفع الصور.',
            'suggestion' => 'اقترح إضافة قسم خاص بالبودكاست.',
        ],
    ],
    'events' => [
        [
            'title' => 'اللقاء الأسبوعي للتقنية',
            'url' => 'https://example.com/events/weekly-tech',
            'description' => 'لقاء أسبوعي لمناقشة أحدث ما في عالم التقنية.',
            'date_type' => \App\Enums\DateTypeEnum::TIME->value,
        ],
        [
            'title' => 'المؤتمر السنوي 2025',
            'url' => 'https://example.com/events/conf-2025',
            'description' => 'مؤتمرنا السنوي مع محاضرات وورش عمل.',
            'date_type' => \App\Enums\DateTypeEnum::DATE_TIME->value,
        ],
    ],
    'live_streams' => [
        [
            'title' => 'البث الصباحي المباشر',
            'url' => 'https://youtube.com/live/abcdef',
            'hours' => 2,
        ],
        [
            'title' => 'نشرة الأخبار المسائية المباشرة',
            'url' => 'https://facebook.com/live/123456',
            'hours' => 3,
        ],
    ],
    'navbar' => [
        'top_links' => [
            [
                'link_order' => 1,
                'link_name' => 'الرئيسية',
                'link_url' => '/',
                'icon_id' => null,
                'link_status' => 6, // CUSTOM_LINK
                'link_open' => 1,   // SAME_WINDOW
            ],
            [
                'link_order' => 2,
                'link_name' => 'الأخبار',
                'link_url' => '/news',
                'icon_id' => null,
                'link_status' => 5, // SPECIAL_PAGE
                'link_open' => 1,
            ],
            [
                'link_order' => 3,
                'link_name' => 'البودكاست',
                'link_url' => '/podcasts',
                'icon_id' => null,
                'link_status' => 3, // PODCAST
                'link_open' => 2, //NEW WINDOW
            ],
            [
                'link_order' => 4,
                'link_name' => 'الفيديو',
                'link_url' => '/videos',
                'icon_id' => null,
                'link_status' => 4, // VIDEO
                'link_open' => 1,
            ],
        ],
        'news_children' => [
            [
                'link_order' => 1,
                'link_name' => 'عاجل',
                'link_url' => '/news/breaking',
                'icon_id' => null,
                'link_status' => 2, // TAG
                'link_open' => 2,
            ],
            [
                'link_order' => 2,
                'link_name' => 'سياسة',
                'link_url' => '/news/politics',
                'icon_id' => null,
                'link_status' => 1, // CATEGORY
                'link_open' => 1,
            ],
        ],
    ],
    'posts' => [

        'titles' => [
        'خبر عاجل: تطورات جديدة في القضية',
        'الحكومة تصدر قراراً هاماً',
        'مقال تحليلي حول الأحداث الأخيرة',
        'تعرف على أهم ما حدث هذا الأسبوع',
        'الاقتصاد يشهد تحسناً ملحوظاً',
    ],
        ],
    'video_albums' => [
        [
            'title' => 'أفلام وثائقية',
            'small_image' => 'images/videos/docs_sm.jpg',
            'background_image' => 'images/videos/docs_bg.jpg',
            'description' => 'أفلام وثائقية مفيدة حول مواضيع مختلفة.',
        ],
        [
            'title' => 'مقابلات',
            'small_image' => 'images/videos/interviews_sm.jpg',
            'background_image' => 'images/videos/interviews_bg.jpg',
            'description' => 'مقابلات معمقة مع قادة الصناعة.',
        ],
    ]
];
