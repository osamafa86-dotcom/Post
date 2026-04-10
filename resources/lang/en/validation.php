<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => 'The :attribute must be accepted.',
    'active_url' => 'The :attribute is not a valid URL.',
    'after' => 'The :attribute must be a date after :date.',
    'after_or_equal' => 'The :attribute must be a date after or equal to :date.',
    'alpha' => 'The :attribute must only contain letters.',
    'alpha_dash' => 'The :attribute must only contain letters, numbers, dashes and underscores.',
    'alpha_num' => 'The :attribute must only contain letters and numbers.',
    'array' => 'The :attribute must be an array.',
    'before' => 'The :attribute must be a date before :date.',
    'before_or_equal' => 'The :attribute must be a date before or equal to :date.',
    'between' => [
        'numeric' => 'The :attribute must be between :min and :max.',
        'file' => 'The :attribute must be between :min and :max kilobytes.',
        'string' => 'The :attribute must be between :min and :max characters.',
        'array' => 'The :attribute must have between :min and :max items.',
    ],
    'boolean' => 'The :attribute field must be true or false.',
    'confirmed' => 'The :attribute confirmation does not match.',
    'date' => 'The :attribute is not a valid date.',
    'date_equals' => 'The :attribute must be a date equal to :date.',
    'date_format' => 'The :attribute does not match the format :format.',
    'different' => 'The :attribute and :other must be different.',
    'digits' => 'The :attribute must be :digits digits.',
    'digits_between' => 'The :attribute must be between :min and :max digits.',
    'dimensions' => 'The :attribute has invalid image dimensions.',
    'distinct' => 'The :attribute field has a duplicate value.',
    'email' => 'The :attribute must be a valid email address.',
    'ends_with' => 'The :attribute must end with one of the following: :values.',
    'exists' => 'The selected :attribute is invalid.',
    'file' => 'The :attribute must be a file.',
    'filled' => 'The :attribute field must have a value.',
    'gt' => [
        'numeric' => 'The :attribute must be greater than :value.',
        'file' => 'The :attribute must be greater than :value kilobytes.',
        'string' => 'The :attribute must be greater than :value characters.',
        'array' => 'The :attribute must have more than :value items.',
    ],
    'gte' => [
        'numeric' => 'The :attribute must be greater than or equal :value.',
        'file' => 'The :attribute must be greater than or equal :value kilobytes.',
        'string' => 'The :attribute must be greater than or equal :value characters.',
        'array' => 'The :attribute must have :value items or more.',
    ],
    'image' => 'The :attribute must be an image.',
    'in' => 'The selected :attribute is invalid.',
    'in_array' => 'The :attribute field does not exist in :other.',
    'integer' => 'The :attribute must be an integer.',
    'ip' => 'The :attribute must be a valid IP address.',
    'ipv4' => 'The :attribute must be a valid IPv4 address.',
    'ipv6' => 'The :attribute must be a valid IPv6 address.',
    'json' => 'The :attribute must be a valid JSON string.',
    'lt' => [
        'numeric' => 'The :attribute must be less than :value.',
        'file' => 'The :attribute must be less than :value kilobytes.',
        'string' => 'The :attribute must be less than :value characters.',
        'array' => 'The :attribute must have less than :value items.',
    ],
    'lte' => [
        'numeric' => 'The :attribute must be less than or equal :value.',
        'file' => 'The :attribute must be less than or equal :value kilobytes.',
        'string' => 'The :attribute must be less than or equal :value characters.',
        'array' => 'The :attribute must not have more than :value items.',
    ],
    'max' => [
        'numeric' => 'The :attribute must not be greater than :max.',
        'file' => 'The :attribute must not be greater than :max kilobytes.',
        'string' => 'The :attribute must not be greater than :max characters.',
        'array' => 'The :attribute must not have more than :max items.',
    ],
    'mimes' => 'The :attribute must be a file of type: :values.',
    'mimetypes' => 'The :attribute must be a file of type: :values.',
    'min' => [
        'numeric' => 'The :attribute must be at least :min.',
        'file' => 'The :attribute must be at least :min kilobytes.',
        'string' => 'The :attribute must be at least :min characters.',
        'array' => 'The :attribute must have at least :min items.',
    ],
    'multiple_of' => 'The :attribute must be a multiple of :value.',
    'not_in' => 'The selected :attribute is invalid.',
    'not_regex' => 'The :attribute format is invalid.',
    'numeric' => 'The :attribute must be a number.',
    'password' => 'The password is incorrect.',
    'present' => 'The :attribute field must be present.',
    'regex' => 'The :attribute format is invalid.',
    'required' => 'The :attribute field is required.',
    'required_if' => 'The :attribute field is required when :other is :value.',
    'required_unless' => 'The :attribute field is required unless :other is in :values.',
    'required_with' => 'The :attribute field is required when :values is present.',
    'required_with_all' => 'The :attribute field is required when :values are present.',
    'required_without' => 'The :attribute field is required when :values is not present.',
    'required_without_all' => 'The :attribute field is required when none of :values are present.',
    'prohibited' => 'The :attribute field is prohibited.',
    'prohibited_if' => 'The :attribute field is prohibited when :other is :value.',
    'prohibited_unless' => 'The :attribute field is prohibited unless :other is in :values.',
    'same' => 'The :attribute and :other must match.',
    'size' => [
        'numeric' => 'The :attribute must be :size.',
        'file' => 'The :attribute must be :size kilobytes.',
        'string' => 'The :attribute must be :size characters.',
        'array' => 'The :attribute must contain :size items.',
    ],
    'starts_with' => 'The :attribute must start with one of the following: :values.',
    'string' => 'The :attribute must be a string.',
    'timezone' => 'The :attribute must be a valid zone.',
    'unique' => 'The :attribute has already been taken.',
    'uploaded' => 'The :attribute failed to upload.',
    'url' => 'The :attribute format is invalid.',
    'uuid' => 'The :attribute must be a valid UUID.',
    //new
    'invalid_telegram_url' => 'Invalid Telegram URL.',
    'publish_role_created' => 'Publish Role created successfully!',
    'change_post_status' => 'Change post status',
    'change_application_status' => 'Change application status',
    'event_times_required' => 'At least one time must be entered',
    'event_times_day_required' => 'Day must be selected',
    'event_times_day_in' => 'A valid day must be selected',
    'event_times_start_time_required' => 'Start time must be entered',
    'event_times_start_time_date_format' => 'Start time format must be correct',
    'event_times_end_time_required' => 'End time must be entered',
    'event_times_end_time_date_format' => 'End time format must be correct',
    'event_times_end_time' => 'End time must be after start time',
    'event_date_times_required' => 'At least one date and time must be entered',
    'event_date_times_start_date_time_required' => 'Start date and time must be entered',
    'event_date_times_start_date_time_date_format' => 'Start date and time format must be correct',
    'event_date_times_end_date_time_required' => 'End date and time must be entered',
    'event_date_times_end_date_time_date_format' => 'End date and time format must be correct',
    'event_date_times_end_date_time' => 'End date and time must be after start date and time',
    'edit_account_status' => 'Edit account status',
    'try_later' => 'Please try again later. Remaining time: :time seconds.',
    'failed_login_try' => 'Failed login attempt, please be careful',
    'incorrect_login_credentials' => 'Incorrect login credentials, try again.',
    'success_email_add' => 'Email added successfully',
    'delete_role' => 'Delete role',
    'delete_role_confirmation' => 'Are you sure you want to delete the role?',
    'delete_role_confirmation_with_name' => 'The role :role_name will be deleted',
    'no_role_found' => 'No role found with this name',





    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],



    'attributes' => [

            'full_name' => 'full name ',
            'message' => 'message',
            'subject' => 'subject',

        //posts
        'title'=> 'title',
        'slug'=> 'slug',
        'image'=> 'image',
        'description'=> 'description',
        'body'=> 'body',
        'publish_date'=> 'publish date',
        'category_id'=> 'category',
        'author_id'=> 'author',

        //breaking_news
        'hide_date' => 'hide date',
        'url' => 'url',

        //categories
        'category_title' => 'category title',
        'category_description' => 'category description',
        'category_type' => 'category type',
        'parent_id' => 'parent',

        //types
        'type_name' => 'type title',

        //special_files
        'file_name' => 'file name',

        //tags
        'tag_name' => 'tag name',
        'tags' => 'tags',


        //quotes
        'quote_author' => 'author',
        'quote_photo' => 'quote photo',
        'quote_from' => 'quote from',
        'quote_text' => 'quote text',

        //special_pages
        'page_title' => 'page title',

        //special_file
        'file_image' => 'file image',

        //advertisements
        'type' => 'type',
        'place' => 'place',
        'end_hour_time' => 'end hour time',
        'end_min_time' => 'end min time',

        //albums
        'small_image' => 'small image',
        'background_image' => 'background image',

        //podcast_tracks
        'podcast_album_id' => 'podcast album',
        'album_id' => 'album',
        //video_tracks
        'video_album_id' => 'video album',

        //live_streams
        'end_date' => 'end date',

        //users
        'role_id' => 'role',

        //navbar_links
        'link_name' => 'link name',
        'link_url' => 'url',
        'link_status' => 'link status',
        'link_open' => 'link open',


        'presenter_id' =>'presenter',

        'icon_path' => ' icon',
        'team_id' => 'team',

        'image_size' => ' image size ',
        'event_image' => 'event image',
       'date_type' => ' date type',
        'tag_type' => 'tag type',

        'reel_title' =>'reel title',
        'reel_url' =>'reel url',
        'reel_type' =>'reel type',

        'file_description' => 'file description',

        'login' => 'login',
        'name' => 'name',
        'username' => 'username',
        'email' => 'email',
        'first_name' => 'first name',
        'third_name' => 'اسم الجد',
        'last_name' => 'last name',
        'password' => 'password',
        'new_password' => 'new password',
        'password_confirmation' => 'password confirmation',
        'city' => 'city',
        'country' => 'country',
        'address' => 'address',
        'phone' => 'phone',
        'mobile' => 'mobile',
        'age' => 'age',
        'sex' => 'النوع',
        'gender' => 'gender',
        'answers' => 'answers',
        'day' => 'day',
        'month' => 'month',
        'year' => 'year',
        'hour' => 'hour',
        'minute' => 'minute',
        'second' => 'second',
        'content' => 'content',
        'excerpt' => 'excerpt',
        'date' => 'date',
        'time' => 'time',
        'available' => 'available',
        'size' => 'size',
        'price' => 'price',
        'desc' => 'desc',
        'q' => 'البحث',
        'link' => ' ',


            'social_media_name' => 'Please enter name',
            'social_media_link' => 'Please enter the link',
            'social_media_icon' => 'Please enter the icon',
            'links_title' => 'Please enter the title',
            'links_url' => 'Please enter the url',
            'links_icon' => 'Please enter the icon',

        'button_title' => 'button title',
        'service_image' => 'service image',
        'model' =>  'model',
        'action' => 'action',
        'permission_target_id' =>  'permission target',
        'fromId' => 'from',
        'toId' => 'to',
        'light_site_name' => 'home site description',

    ],

];
