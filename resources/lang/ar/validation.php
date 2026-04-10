<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | such as the size rules. Feel free to tweak each of these messages.
    |
    */

    'accepted' => 'يجب قبول الحقل :attribute',
    'active_url' => 'الحقل :attribute لا يُمثّل رابطًا صحيحًا',
    'after' => 'يجب على الحقل :attribute أن يكون تاريخًا لاحقًا للتاريخ :date.',
    'after_or_equal' => 'الحقل :attribute يجب أن يكون تاريخاً لاحقاً أو مطابقاً للتاريخ :date.',
    'alpha' => 'يجب أن لا يحتوي الحقل :attribute سوى على حروف',
    'alpha_dash' => 'يجب أن لا يحتوي الحقل :attribute على حروف، أرقام ومطّات.',
    'alpha_num' => 'يجب أن يحتوي :attribute على حروفٍ وأرقامٍ فقط',
    'array' => 'يجب أن يكون الحقل :attribute ًمصفوفة',
    'before' => 'يجب على الحقل :attribute أن يكون تاريخًا سابقًا للتاريخ :date.',
    'before_or_equal' => 'الحقل :attribute يجب أن يكون تاريخا سابقا أو مطابقا للتاريخ :date',
    'between' => [
        'numeric' => 'يجب أن تكون قيمة :attribute بين :min و :max.',
        'file' => 'يجب أن يكون حجم الملف :attribute بين :min و :max كيلوبايت.',
        'string' => 'يجب أن يكون عدد حروف النّص :attribute بين :min و :max',
        'array' => 'يجب أن يحتوي :attribute على عدد من العناصر بين :min و :max',
    ],
    'boolean' => 'يجب أن تكون قيمة الحقل :attribute إما true أو false ',
    'confirmed' => 'حقل التأكيد غير مُطابق للحقل :attribute',
    'date' => 'الحقل :attribute ليس تاريخًا صحيحًا',
    'date_format' => 'لا يتوافق الحقل :attribute مع الشكل :format.',
    'different' => 'يجب أن يكون الحقلان :attribute و :other مُختلفان',
    'digits' => 'يجب أن يحتوي الحقل :attribute على :digits رقمًا/أرقام',
    'digits_between' => 'يجب أن يحتوي الحقل :attribute بين :min و :max رقمًا/أرقام ',
    'dimensions' => 'الـ :attribute يحتوي على أبعاد صورة غير صالحة.',
    'distinct' => 'للحقل :attribute قيمة مُكرّرة.',
    'email' => 'يجب أن يكون :attribute عنوان بريد إلكتروني صحيح البُنية',
    'exists' => 'الحقل :attribute لاغٍ',
    'file' => 'الـ :attribute يجب أن يكون من ملفا.',
    'filled' => 'الحقل :attribute إجباري',
    'image' => 'يجب أن يكون الحقل :attribute صورةً',
    'in' => 'الحقل :attribute لاغٍ',
    'in_array' => 'الحقل :attribute غير موجود في :other.',
    'integer' => 'يجب أن يكون الحقل :attribute عددًا صحيحًا',
    'ip' => 'يجب أن يكون الحقل :attribute عنوان IP ذا بُنية صحيحة',
    'ipv4' => 'يجب أن يكون الحقل :attribute عنوان IPv4 ذا بنية صحيحة.',
    'ipv6' => 'يجب أن يكون الحقل :attribute عنوان IPv6 ذا بنية صحيحة.',
    'json' => 'يجب أن يكون الحقل :attribute نصا من نوع JSON.',
    'max' => [
        'numeric' => 'يجب أن تكون قيمة الحقل :attribute مساوية أو أصغر لـ :max.',
        'file' => 'يجب أن لا يتجاوز حجم الملف :attribute :max كيلوبايت',
        'string' => 'يجب أن لا يتجاوز طول نص :attribute :max حروفٍ/حرفًا',
        'array' => 'يجب أن لا يحتوي الحقل :attribute على أكثر من :max عناصر/عنصر.',
    ],
    'mimes' => 'يجب أن يكون الحقل ملفًا من نوع : :values.',
    'mimetypes' => 'يجب أن يكون الحقل ملفًا من نوع : :values.',
    'min' => [
        'numeric' => 'يجب أن تكون قيمة الحقل :attribute مساوية أو أكبر لـ :min.',
        'file' => 'يجب أن يكون حجم الملف :attribute على الأقل :min كيلوبايت',
        'string' => 'يجب أن يكون طول نص :attribute على الأقل :min حروفٍ/حرفًا',
        'array' => 'يجب أن يحتوي الحقل :attribute على الأقل على :min عُنصرًا/عناصر',
    ],
    'not_in' => 'الحقل :attribute لاغٍ',
    'numeric' => 'يجب على الحقل :attribute أن يكون رقمًا',
    'present' => 'يجب تقديم الحقل :attribute',
    'regex' => 'صيغة الحقل :attribute .غير صحيحة',
    'required' => 'الحقل :attribute مطلوب.',
    'required_if' => 'الحقل :attribute مطلوب في حال ما إذا كان :other يساوي :value.',
    'required_unless' => 'الحقل :attribute مطلوب في حال ما لم يكن :other يساوي :values.',
    'required_with' => 'الحقل :attribute مطلوب إذا توفّر :values.',
    'required_with_all' => 'الحقل :attribute مطلوب إذا توفّر :values.',
    'required_without' => 'الحقل :attribute مطلوب إذا لم يتوفّر :values.',
    'required_without_all' => 'الحقل :attribute مطلوب إذا لم يتوفّر :values.',
    'same' => 'يجب أن يتطابق الحقل :attribute مع :other',
    'size' => [
        'numeric' => 'يجب أن تكون قيمة الحقل :attribute مساوية لـ :size',
        'file' => 'يجب أن يكون حجم الملف :attribute :size كيلوبايت',
        'string' => 'يجب أن يحتوي النص :attribute على :size حروفٍ/حرفًا بالظبط',
        'array' => 'يجب أن يحتوي الحقل :attribute على :size عنصرٍ/عناصر بالظبط',
    ],
    'string' => 'يجب أن يكون الحقل :attribute نصآ.',
    'timezone' => 'يجب أن يكون :attribute نطاقًا زمنيًا صحيحًا',
    'unique' => 'قيمة الحقل :attribute مُستخدمة من قبل',
    'uploaded' => 'فشل في تحميل الـ :attribute',
    'url' => 'صيغة الرابط :attribute غير صحيحة',
    'name_required'   => 'يجب إدخال الاسم',
    'name_found'      => 'الاسم مستخدم بالفعل',
    'edit_success'    => 'تم تعديل البيانات بنجاح',
    'number_required' => 'يجب إدخال رقم',
    'saved_success'   => 'تم الحفظ بنجاح',
    'title_required' => 'يرجى ادخال العنوان',
    'url_required' => 'يرجى ادخال الرابط',
    'icon_id_required' => 'يرجى ادخال الايقونة',
    'tag_generation_failed_no_tags_returned' => 'فشل في إنشاء الوسوم،لا يوجد وسوم',
    'post_body_generation_failed_no_body_returned' => 'فشل في انشاء محتوى البوست، لا يوجد محتوى للبوست',
    'content_generation_failed' => 'فشل في إنشاء المحتوى: ',
    'something_went_wrong_while_updating_the_post' => 'حدث خطأ أثناء تحديث البوست',
    'failed' =>'فشل في التحقق',
    'error' => 'خطأ',
    //new
    'change_post_status' => 'تغییر حالة المنشور',
    'change_application_status' => 'تغییر حالة الطلب',
    'event_times_required' => 'يجب ادخال وقت واحد على الاقل',
    'event_times_day_required' => 'يجب اختيار اليوم',
    'event_times_day_in' => 'يجب اختيار يوم صحيح',
    'event_times_start_time_required' => 'يجب إدخال وقت البدء',
    'event_times_start_time_date_format' => 'يجب ان يكون بناء وقت البدء صحيح',
    'event_times_end_time_required' => 'يجب إدخال وقت الانتهاء',
    'event_times_end_time_date_format' => 'يجب ان يكون بناء وقت الانتهاء صحيح',
    'event_times_end_time' => 'يجب أن يكون وقت الانتهاء بعد وقت البدء',
    'event_date_times_required' => 'يجب ادخال تاريخ و وقت واحد على الاقل',
    'event_date_times_start_date_time_required' => 'يجب إدخال تاريخ و وقت البدء',
    'event_date_times_start_date_time_date_format' => 'يجب ان يكون بناء تاريخ و وقت البدء صحيح',
    'event_date_times_end_date_time_required' => 'يجب إدخال تاريخ و وقت الانتهاء',
    'event_date_times_end_date_time_date_format' => 'يجب ان يكون بناء تاريخ و وقت الانتهاء صحيح',
    'event_date_times_end_date_time' => 'يجب أن يكون وقت الانتهاء بعد وقت البدء',
    'edit_account_status' =>  'تعديل حالة الحساب',
    'try_later' => 'يرجى المحاولة فيما بعد. الوقت المتبقي:time ثانية.',
    'failed_login_try' => 'محاولة دخول فاشلة يرجى الانتباه',
    'incorrect_login_credentials' => 'بيانات غير صحيحة حاول مرة اخرى.',
    'success_email_add' => 'تم اضافة البريد الالكتروني بنجاح',
    'delete_role' => 'حذف صلاحية',
    'delete_role_confirmation' => 'هل أنت متأكد من حذف الصلاحية',
    'delete_role_confirmation_with_name' => 'سيتم حذف الصلاحية:role_name',
    'no_role_found' => 'لا يوجد صلاحية بهذا الاسم',
    'invalid_telegram_url' => 'رابط تليجرام غير صالح.',
    'publish_role_created' => 'تم إنشاء صلاحية النشر بنجاح!',
    'no_image_in_files_table' => 'لا يوجد صور في جدول الملفات',



    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute_rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [


        'full_name' => 'الاسم ',
        'message' => 'الرسالة',
        'subject' => 'الموضوع',

        //posts
        'title'=> 'العنوان',
        'slug'=> 'العنوان الإشاري',
        'image'=> 'الصورة',
        'description'=> 'الوصف',
        'body'=> 'المحتوى',
        'publish_date'=> 'تاريخ النشر',
        'category_id'=> 'التصنيف',
        'author_id'=> 'الكاتب',


        //breaking_news
        'hide_date' => 'وقت الإنتهاء',
        'url' => 'الرابط',

        //categories
        'category_title' => 'عنوان التصنيف',
        'category_description' => 'وصف التصنيف',
        'category_type' => 'نوع التصنيف',
        'parent_id' => 'التصنيف الرئيسي',

        //types
        'type_name' => 'عنوان النوع',

        //special_files
        'file_name' => 'عنوان الملف',

        //tags
        'tag_name' => 'عنوان الوسم',
        'tags' => 'الوسوم',

        //authors
        'author_name' => 'اسم الكاتب',
        'author_description' => 'وصف الكاتب',
        'author_image' => 'صورة الكاتب',
        'author_work' => 'عمل الكاتب',
        'author_facebook' => 'فيسبوك الكاتب',
        'author_twitter' => 'تويتر الكاتب',
        'author_google_plus' => 'قوقل بلس الكاتب',

        //quotes
        'quote_author' => 'اسم الكاتب',
        'quote_photo' => 'صورة الإقتباس',
        'quote_from' => 'الإقتباس من منصة',
        'quote_text' => 'نص الإقتباس',

        //special_pages
        'page_title' => 'عنوان الصفحة',

        //special_file
        'file_image' => 'صورة الملف',

        //advertisements
        'type' => 'النوع ',
        'place' => 'القالب',
        'end_hour_time' => 'من ساعات',
        'end_min_time' => 'من دقائق',

        'tag_type' =>'نوع الوسم',

        'reel_title' =>'عنوان الريلز',
        'reel_url' =>'رابط الريلز',
        'reel_type' =>'نوع الريلز',

        'file_description' => 'وصف الملف الخاص',

        //albums
        'small_image' => 'الصورة المصغرة',
        'background_image' => 'صورة الغلاف',

        //podcast_tracks
        'podcast_album_id' => 'الموضوع',

        //video_tracks
        'video_album_id' => 'الالبوم',

        //live_streams
        'end_date' => 'تاريخ الانتهاء',

        //users
        'role_id' => 'حالة المستخدم',

        //navbar_links
        'link_name' => 'النص في القائمة',
        'link_url' => 'الرابط',
        'link_status' => 'نوع الرابط',
        'link_open' => 'يفتح في',

        'login' => 'اسم المستخدم أو البريد الإلكتروني',
        'name' => 'الاسم',
        'username' => 'اسم المُستخدم',
        'email' => 'البريد الالكتروني',
        'first_name' => 'الاسم الأول',
        'third_name' => 'اسم الجد',
        'last_name' => 'اسم العائلة',
        'password' => 'كلمة المرور',
        'new_password' => 'كلمة المرور الجديدة',
        'password_confirmation' => 'تأكيد كلمة المرور',
        'city' => 'المحافظة',
        'country' => 'الدولة',
        'address' => 'العنوان',
        'phone' => 'الهاتف',
        'mobile' => 'الجوال',
        'age' => 'العمر',
        'sex' => 'النوع',
        'gender' => 'الجنس',
        'answers' => 'أجوبة',
        'day' => 'اليوم',
        'month' => 'الشهر',
        'year' => 'السنة',
        'hour' => 'ساعة',
        'minute' => 'دقيقة',
        'second' => 'ثانية',
        'content' => 'المُحتوى',
        'excerpt' => 'المُلخص',
        'date' => 'التاريخ',
        'time' => 'الوقت',
        'available' => 'مُتاح',
        'size' => 'الحجم',
        'price' => 'السعر',
        'desc' => 'نبذه',
        'q' => 'البحث',
        'link' => ' ',

        'icon_path' => 'الايقونة',
        'team_id' =>'الفريق',
        'image_size' => 'حجم الصورة',
        'album_id' => 'الالبوم',
        'event_image' =>'صورة الحدث',
        'presenter_id' =>'المقدم',
        'date_type' => 'نوع التوقيت',

        'birth_date' => 'تاريخ الميلاد',
        'town' => 'البلدة',
        'specialization' => 'التخصص',
        'portfolio' => 'السيرة الذاتية',
        'skills' => 'المهارات',
        'whatsapp' => 'الواتساب',
        'facebook' => 'الفيسبوك',


        'social_media_name' => 'يرجى ادخال الاسم',
        'social_media_link' => 'يرجى ادخال الرابط',
        'social_media_icon' => 'يرجى ادخال الايقونة',
        'links_title' => 'يرجى ادخال العنوان',
        'links_url' => 'يرجى ادخال الرابط',
        'links_icon' => 'يرجى ادخال الايقونة',
        'company' => 'اسم الشركة',
        'number_people' => 'عدد الاشخاص',
        'usage_purpose' => 'غرض الاستخدام',
        'usage_duration' => 'مدة الاستخدام',
        'required_equipment' => 'المعدات المطلوبة',

        'file' => 'الملف',
        'new image' =>'الملف',

        'button_title' => 'عنوان الزر',
        'service_image' => 'صورة الخدمة',
        'model' => 'النموذج',
        'action' => 'العملية',
        'permission_target_id' => 'الهدف',
        'fromId' => 'من',
        'toId' => 'الى',
        'light_site_name' => 'وصف الصفحة الرئيسية',
    ],

];
