<?php

namespace App\Traits;

use App\Models\Team;
use App\Models\UserDetails;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Layout;

trait PermissionsTrait
{
    public function listPermissions()
    {
        return [
            'posts' => ['name' => 'المنشورات','en_name' => 'Posts'],
            'sort_contents' => ['name' => 'ترتيب المحتوى','en_name' => 'Sort Contents'],
            'breaking_news' => ['name' => 'أخبار عاجلة','en_name' => 'Breaking News'],
            'categories' => ['name' => 'التصنيفات','en_name' => 'Categories'],
            'types' => ['name' => 'أنواع الأخبار','en_name' => 'Types'],
            'icons' => ['name' => 'الايقونات','en_name' => 'Icons'],
            'special_files' => ['name' => 'مفات خاصة','en_name' => 'Special Files'],
            'tags' => ['name' => 'الوسوم','en_name' => 'Tags'],
            'quotes' => ['name' => 'الاقتباسات','en_name' => 'Quotes'],
            'special_pages' => ['name' => 'الصفحات الخاصة','en_name' => 'Special Pages'],
            'advertisements' => ['name' => 'الاعلانات','en_name' => 'Advertisements'],
            'events' => ['name' => 'الأحداث','en_name' => 'Events'],
            'users' => ['name' => 'المشرفين','en_name' => 'Users'],
            'permissions' => ['name' => 'الصلاحيات','en_name' => 'Permissions'],
            'user_logs' => ['name' => 'سجل المستخدمين','en_name' => 'User Logs'],
            'settings' => ['name' => 'الاعدادات','en_name' => 'Settings'],
            'publish_roles' => ['name' => 'رتبة النشر','en_name' => 'Publish Roles'],
            'pending_actions' => ['name' => 'المواد المعلقة','en_name' => 'Pending Actions'],
            'navbar_links' => ['name' => 'القوائم','en_name' => 'Navbar Links'],
            'contact_us' => ['name' => 'تواصل معنا','en_name' => 'Contact Us'],
            'send_news' => ['name' => 'أرسل خبر','en_name' => 'Send News'],
            'roles' => ['name' => 'إشعارات','en_name' => 'Roles'],
            'alerts' => ['name' => 'التنبيهات','en_name' => 'Alerts'],
            'user_details' => ['name' => 'تفاصيل المستخدم','en_name' => 'User Details'],
            'news_letter_emails' => ['name' => 'النشره البريدية','en_name' => 'News Letter Emails'],
            'participants' => ['name' => 'المشاركين','en_name' => 'Participants'],
            'materials' => ['name' => 'المواد','en_name' => 'Materials'],
            'materials_albums' => ['name' => 'البومات المواد','en_name' => 'Materials Albums'],
            'data_page' => ['name' => 'صفحة البيانات','en_name' => 'Data Page'],
            'social_media' => ['name' => 'روابط التواصل الاجتماعي ','en_name' => 'Social Media'],
            'reels' => ['name' => 'الريلز','en_name' => 'Reels'],
            'courses' => ['name' => 'الدورات','en_name' => 'Courses'],
            'import' => ['name' => 'الاستيراد','en_name' => 'Import'],
            'services' => ['name' => 'الخدمات','en_name' => 'Services'],
            'applications' => ['name' => 'طلبات الدورات','en_name' => 'Applications'],
            'subscribers' => ['name' => 'المشتركين','en_name' => 'Subscribers'],
            'subscriptions' => ['name' => 'الاشتراكات','en_name' => 'Subscriptions'],
            'subscriber_notifications' => ['name' => 'اشعارات المشتركين','en_name' => 'Subscriber Notifications'],
        ];
    }
}
