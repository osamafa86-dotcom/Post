<?php
return [
    'alerts' => [
        'security_alert' => [
            'title' => 'Security Alert',
            'message' => 'An unauthorized login attempt was detected.',
        ],
        'operation_success' => [
            'title' => 'Operation Successful',
            'message' => 'Data has been saved successfully.',
        ],
        'maintenance_notice' => [
            'title' => 'Maintenance Notice',
            'message' => 'Server maintenance will occur at midnight.',
        ],
        'process_completed' => [
            'title' => 'Process Completed',
            'message' => 'All background jobs finished successfully.',
        ],
    ],
    'categories' => [
        'words' => [
            'Freedom', 'Peace', 'Hope', 'Creativity', 'Culture', 'Technology', 'Education', 'Justice', 'Development', 'Health',
            'Future', 'Progress', 'Success', 'Strength', 'Willpower', 'Ambition', 'Responsibility', 'Integrity', 'Nation', 'Cause',
            'Innovation', 'Economy', 'Science', 'Knowledge', 'Life', 'Value', 'Ethics', 'Media', 'Information', 'Community',
            'Solidarity', 'Participation', 'Cooperation', 'Environment', 'System', 'History', 'Identity', 'Principle', 'Giving', 'Equality',
            'Law', 'Citizenship', 'Rights', 'Reform', 'Production', 'Creativity', 'Experience', 'Professionalism', 'Credibility', 'Communication',
        ],
    ],
    'contacts' => [

        'subjects' => [
            'inquiry' => 'Inquiry',
            'support' => 'Support',
            'suggestion' => 'Suggestion',
        ],
        'messages' => [
            'inquiry' => 'I would like to know more about the site features.',
            'support' => 'Having trouble uploading images.',
            'suggestion' => 'I suggest adding a podcast section.',
        ],
    ],
    'events' => [
        [
            'title' => 'Weekly Tech Meetup',
            'url' => 'https://example.com/events/weekly-tech',
            'description' => 'A weekly meetup to discuss latest in tech.',
            'date_type' => \App\Enums\DateTypeEnum::TIME->value,
        ],
        [
            'title' => 'Annual Conference 2025',
            'url' => 'https://example.com/events/conf-2025',
            'description' => 'Our annual conference with keynotes and workshops.',
            'date_type' => \App\Enums\DateTypeEnum::DATE_TIME->value,
        ],
    ],

    'live_streams' => [
        [
            'title' => 'Morning Show Live',
            'url' => 'https://youtube.com/live/abcdef',
            'hours' => 2,
        ],
        [
            'title' => 'Evening News Live',
            'url' => 'https://facebook.com/live/123456',
            'hours' => 3,
        ],
    ],
    'navbar' => [
        'top_links' => [
            [
                'link_order' => 1,
                'link_name' => 'Home',
                'link_url' => '/',
                'icon_id' => null,
                'link_status' => 6, // CUSTOM_LINK
                'link_open' => 1,   // SAME_WINDOW
            ],
            [
                'link_order' => 2,
                'link_name' => 'News',
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
                'link_name' => 'Breaking',
                'link_url' => '/news/breaking',
                'icon_id' => null,
                'link_status' => 2, // TAG
                'link_open' => 2,   // NEW_WINDOW
            ],
            [
                'link_order' => 2,
                'link_name' => 'Politics',
                'link_url' => '/news/politics',
                'icon_id' => null,
                'link_status' => 1, // CATEGORY
                'link_open' => 1,   // SAME_WINDOW
            ],
        ],
    ],
    'posts' => [
    'titles' => [
        'Breaking News: New developments in the case',
        'Government issues an important decision',
        'Analytical article on recent events',
        'Discover the main events this week',
        'The economy shows remarkable improvement',
    ],
    ],
    'video_albums' => [
    [
        'title' => 'Documentaries',
        'small_image' => 'images/videos/docs_sm.jpg',
        'background_image' => 'images/videos/docs_bg.jpg',
        'description' => 'Informative documentaries across various topics.',
    ],
    [
        'title' => 'Interviews',
        'small_image' => 'images/videos/interviews_sm.jpg',
        'background_image' => 'images/videos/interviews_bg.jpg',
        'description' => 'In-depth interviews with industry leaders.',
    ],
    ],
];
