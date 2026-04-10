<?php
return [
    'default' => [
        '1' => 'Yes',
        '2' => 'No',
        'undefined' => 'Undefined',
    ],
    'models' =>[
        '1' => 'Material',
        '2' => 'Post',
        '3' => 'Participant',
        '4' => 'Category',
        '5' => 'Podcast Album',
        '6' => 'Video Album',
        '7' => 'Setting',
        '8' => 'Breaking News',
        '9' => 'Type',
        '10' => 'Special File',
        '11' => 'Tag',
        '12' => 'Quote',
        '13' => 'Special Page',
        '14' => 'Advertisement',
        '15' => 'Podcast Track',
        '16' => 'Vedio Track',
        '17' => 'Alert',
        '18' => 'User',
        '19' => 'Contact Us',
        '20' => 'Send News',
    ],
    'field' => 'Field',
    'value' => 'Field Data',
    'fields' => [
        'Post' => [
            'title' => 'Title',
            'description' => 'Description',
            'body' => 'Body',
            'publish_date' => 'Publish Date',

        ],
        'Category' => [
            'category_title' => 'Category Title',
            'category_description' => 'Category Description',
        ],
        'Author' => [
            'author_name' => 'Author Name',
            'author_work' => 'Author Work',
            'author_facebook' => 'Author Facebook',
            'author_twitter' => 'Author Twitter',
            'author_google_plus' => 'Author Google Plus',
            'author_description' => 'Author Description',
        ],
        'BreakingNews' => [
            'title' => 'News Title',
            'url' => 'News URL',
            'hide_date' => 'Expires In (Hours)',
        ],
        'Type' => [
            'type_name' => 'News Type Title',
        ],
        'SpecialFile' => [
            'file_name' => 'File Title',
        ],
        'Tag' => [
            'tag_name' => 'Tag Title',
        ],
        'Quote' => [
            'quote_author' => 'Author Name',
            'quote_from' => 'Quote from Platform',
            'quote_text' => 'Quote Text',
        ],
        'SpecialPage' => [
            'page_title' => 'Title',
            'page_content' => 'Details',
        ],
        'Advertisement' => [
            'title' => 'Title',
            'place' => 'Place',
            'type' => 'Type',
            'url' => 'URL',
            'url_target' => 'How should the URL open?',
            'end_hour_time' => 'End Time (Hour)',
            'end_min_time' => 'End Time (Minute)',
        ],
        'PodcastAlbum' => [
            'title' => 'Podcast Album Title',
            'description' => 'Podcast Album Description',
        ],
        'PodcastTrack' => [
            'title' => 'Podcast Track Title',
            'description' => 'Podcast Track Description',
            'url' => 'URL',
            'podcast_album_id' => 'Album',
        ],
        'VideoAlbum' => [
            'title' => 'Video Album Title',
            'description' => 'Video Album Description',
        ],
        'VideoTrack' => [
            'title' => 'Video Title',
            'description' => 'Video Description',
            'url' => 'Video URL',
            'video_album_id' => 'Album',
        ],
        'LiveStream' => [
            'title' => 'Live Stream Title',
            'url' => 'Live Stream URL',
            'end_date' => 'Ends At',
        ],
        'User' => [
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'email' => 'Email',
            'role_id' => 'User Status',
        ],
    ],
    'actions' => [
        '1' => 'Create',
        '2' => 'Edit',
        '3' => 'Pend',
        '4' => 'Delete',
        '5' => 'Change Post Status',

    ],

    'actionablePublish' => [
        '1' => 'Under Review',
        '2' => 'Published',
        '3' => 'Draft',
        '4' => 'Rejected',
        '5' => 'Needs Revision',
    ],

    'advertisementPlace' => [
        '1' => 'Side Advertisement',
        '2' => 'Main Advertisement',
        '3' => 'Middle Advertisement',
        '4' => 'Bottom Advertisement',
    ],

    'advertisementType' => [
        '1' => 'Photo',
        '2' => 'Code',
    ],

    'advertisementUrlTarget' => [
        '1' => 'Same Window',
        '2' => 'New Window',
    ],

    'alertType' => [
        '1' => 'Danger',
        '2' => 'Success',
        'status' =>[
            '1' => 'danger',
            '2' => 'success',
        ],
    ],

    'categoryStyle' => [
        '1' => 'One big and four small style',
        '2' => 'Four big, rest small style',
        '3' => 'One big and two medium style',
        '4' => 'Three medium with sub list',
        '5' => 'Three podcast style',
        '6' => 'One big video and three medium',
        '7' => 'Two big and four medium style',
        '8' => 'Four medium style',
        '9' => 'Six medium horizontal style',
        '10' => 'Three medium vertical style',
        '11' => 'One tall and six sub style',
        '12' => 'Six slider style',
        '13' => 'One tall and four sub style',
        '14' => 'Three medium with three small sub style',
        '15' => 'One big with three medium style',
        '16' => 'One big with four medium videos',
        '17' => 'One medium with three sub style',
        '18' => 'Three medium with slider',
        '19' => 'Two big with three medium style',
        '20' => 'One big with four medium style',
        '21' => 'One big, one wide, two medium style',
        '22' => 'One medium with vertical slider',
        '23' => 'Four medium with sub list',
        '24' => 'One big with four sub style',
        '25' => 'One big video with three sub style',
        '26' => 'Three medium in slider style',
        '27' => 'Three medium in dark slider style',
        '28' => 'Two categories style',
        '29' => 'Three sub, one medium, four sub style',
        '30' => 'Four medium, one slider, two sub style',
        '31' => 'Eight medium with eight small style',
        '32' => 'One medium slider with three sub',
        '33' => 'One medium with three sub',
        '34' => 'FiMED: Reports (1 featured + 4 list)',
        '35' => 'FiMED: Dark section (3 cards)',
        '36' => 'FiMED: Card grid (4 colored cards)',
        '37' => 'FiMED: Opinions (3 author cards)',
    ],

    'CategoryTypeEnum' => [
        '1' => 'News',
        '2' => 'List',
        '3' => 'Pages',
        '4' => 'Podcast',
        '5' => 'Video',
        '6' => 'Image',
        '7' => 'Events',
        '8' => 'Users',
        '9' => 'Courses',
        '10' => 'Volunteering',
        '11' => 'Conference',
    ],

    'dataPage' => [
        '1' => 'Code',
        '2' => 'File',
        '3' => 'Opportunity',
        '4' => 'Scholarship',
    ],

    'dateType' => [
        '1' => 'Time',
        '2' => 'Date & Time',
    ],

    'day' => [
        '1' => 'Saturday',
        '2' => 'Sunday',
        '3' => 'Monday',
        '4' => 'Tuesday',
        '5' => 'Wednesday',
        '6' => 'Thursday',
        '7' => 'Friday',
    ],

    'gender' => [
        '1' => 'Male',
        '2' => 'Female',
    ],

    'imageSizeType' => [
        '1' => 'Large Image',
        '2' => 'Mid Image',
        '3' => 'Small Image',
        '4' => 'Cover Article',
        '5' => 'Side Post',
    ],

    'linkPosition' => [
        '1' => 'Header',
        '2' => 'Footer',
        '3' => 'Header and Footer',
        '4' => 'All Places',
    ],

    'linkStatus' => [
        '1' => 'Category',
        '2' => 'Tag',
        '3' => 'Podcast',
        '4' => 'Video',
        '5' => 'Special Page',
        '6' => 'Custom Link',
        '7' => 'Type',
    ],

    'linkUrlTarget' => [
        '1' => 'Same Window',
        '2' => 'New Window',
    ],

    'materialType' => [
        '1' => 'Podcast or Audio',
        '2' => 'Video',
        '3' => 'Image',
    ],

    'newsType' => [
        '1' => 'Main News',
        '2' => 'Sub News',
    ],

    'notificationType' => [
        '1' => 'Notification',
        '2' => 'Event',
    ],

    'participantType' => [
        '1' => 'Authors',
        '2' => 'Presenters',
        '3' => 'Guests',
        '4' => 'Clients',
        '5' => 'Partners',
        '6' => 'Team',
        '7' => 'Supporters',
        '8' => 'Publishers',
        '9' => 'Resources',
    ],

    'publish' => [
        '1' => 'Published',
        '2' => 'Draft',
        '3' => 'Pending',
    ],

    'reelType' => [
        '1' => 'YouTube',
        '2' => 'Instagram',
        '3' => 'Local',
    ],

    'subscriptionPaymentMethod' => [
        '1' => 'PayPal',
        '2' => 'Credit Card',
        '3' => 'Cash',
    ],

    'subscriptionStatus' => [
        '1' => 'Active',
        '2' => 'Inactive',
    ],

    'subscriptionType' => [
        '1' => 'Monthly',
        '2' => 'Yearly',
    ],

    'tagType' => [
        '1' => 'News',
        '2' => 'Interest',
        '3' => 'Events',
        '4' => 'Materials',
        '5' => 'Skills',
        '6' => 'Specialization',
        '7' => 'Users',
        '8' => 'Languages',
        '9' => 'Dialects',
    ],

    'userDetailsType' => [
        '1' => 'content_creator',
        '2' => 'hongora_team',
    ],

    'userStatus' => [
        '1' => 'Admin',
        '2' => 'Data Entry',
        '3' => 'Editor',
        '4' => 'Voice Over',
        '5' => 'Subscriber',
    ],

    'videoType' => [
        '1' => 'youtube',
        '2' => 'telegram',
        '3' => 'local',
    ],

    'waterMark' => [
        '1' => 'UP_LEFT',
        '2' => 'UP_RIGHT',
        '3' => 'MID',
        '4' => 'DOWN_LEFT',
        '5' => 'DOWN_RIGHT',
        '6' => 'UNDEFINED',
    ],

];
