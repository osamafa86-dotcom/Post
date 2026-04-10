<?php

use App\Http\Controllers\Dashboard\Languages\LanguageController;
use App\Http\Controllers\FileManger\FileMangerController;
use App\Http\Controllers\ImageEditorController;
use App\Http\Controllers\RSSFeedController;
use App\Livewire\Dashboard\Advertisements\CreateUpdateAdvertisement;
use App\Livewire\Dashboard\Advertisements\ListAdvertisements;
use App\Livewire\Dashboard\ListAlerts;
use App\Livewire\Dashboard\ListApplications;
use App\Livewire\Dashboard\ListBreakingNews;
use App\Livewire\Dashboard\ListCategories;
use App\Livewire\Dashboard\ListContactUs;
use App\Livewire\Dashboard\ListCourses;
use App\Livewire\Dashboard\ListDataPage;
use App\Livewire\Dashboard\ListEvents;
use App\Livewire\Dashboard\ListIcons;
use App\Livewire\Dashboard\DevTools\ImportDatabase;
use App\Livewire\Dashboard\ListMaterialAlbums;
use App\Livewire\Dashboard\ListMaterials;
use App\Livewire\Dashboard\ListNavbarLinks;
use App\Livewire\Dashboard\ListNewsLetterEmails;
use App\Livewire\Dashboard\ListParticipants;
use App\Livewire\Dashboard\ListPendingActions;
use App\Livewire\Dashboard\ListPublishRoles;
use App\Livewire\Dashboard\ListQuotes;
use App\Livewire\Dashboard\ListReels;
use App\Livewire\Dashboard\ListRoles;
use App\Livewire\Dashboard\ListSends;
use App\Livewire\Dashboard\ListServices;
use App\Livewire\Dashboard\ListSettings;
use App\Livewire\Dashboard\ListSortContents;
use App\Livewire\Dashboard\ListSpecialFiles;
use App\Livewire\Dashboard\ListSubscriberNotifications;
use App\Livewire\Dashboard\ListSubscribers;
use App\Livewire\Dashboard\ListSubscriptions;
use App\Livewire\Dashboard\ListTags;
use App\Livewire\Dashboard\ListTypes;
use App\Livewire\Dashboard\ListUser;
use App\Livewire\Dashboard\ListUserDetails;
use App\Livewire\Dashboard\ListUserLogs;
use App\Livewire\Dashboard\Login;
use App\Livewire\Dashboard\MainDashboard;
use App\Livewire\Dashboard\Permissions\CreateUpdatePermission;
use App\Livewire\Dashboard\Permissions\ListPermissions;
use App\Livewire\Dashboard\Posts\CreateUpdatePost;
use App\Livewire\Dashboard\Posts\ListDeletedPosts;
use App\Livewire\Dashboard\Posts\ListDraftPosts;
use App\Livewire\Dashboard\Posts\ListPosts;
use App\Livewire\Dashboard\Profile;
use App\Livewire\Dashboard\SpecialPages\CreateUpdateSpecialPages;
use App\Livewire\Dashboard\SpecialPages\ListSpecialPages;
use App\Models\Post;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider, and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
//dashboard
Route::prefix('/dashboard')->middleware('auth')->group(function () {
    Route::get('/', MainDashboard::class)->name('dashboard.main');
    Route::post('/upload-files-to-library', [FileMangerController::class, 'uploadFiles'])->name('upload-files-to-library');
    Route::post('/export-files-from-library', [FileMangerController::class, 'exportBulk'])->name('export-files-from-library');
    Route::post('/file-manager/crop', [FileMangerController::class, 'crop'])->name('file-manger.crop');
    Route::post('/file-manager/crop-as-new', [FileMangerController::class, 'cropAsNew'])->name('file-manger.crop-as-new');
    Route::get('/file-manager/proxy-image', [FileMangerController::class, 'proxyImage'])->name('file-manger.proxy-image');
    Route::get('/profile', Profile::class)->name('dashboard.profile');
    Route::get('/post/preview/{token}', \App\Livewire\Preview\PostPreview::class)->name('posts.preview');
    if (config('features.permissions.navbar_links')) {
        Route::get('/navbar_links', ListNavbarLinks::class)->middleware(['permission:navbar_links_show|navbar_links_edit'])->name('dashboard.navbar_links');
    }
    if (config('features.permissions.quotes')) {
        Route::get('/quotes/{add?}', ListQuotes::class)->middleware(['permission:quotes_show|quotes_create|quotes_edit'])->name('dashboard.quotes');
    }
    if (config('features.permissions.data_page')) {
        Route::get('/data_page/{add?}', ListDataPage::class)->middleware(['permission:data_page_show|data_page_create|data_page_edit'])->name('dashboard.data_page');
    }
    if (config('features.permissions.users')) {
        Route::get('/users/{add?}', ListUser::class)->middleware(['permission:users_show|users_create|users_edit'])->name('dashboard.users');
    }
    if (config('features.permissions.settings')) {
        Route::get('/settings/{add?}', ListSettings::class)->middleware(['permission:settings_general_settings|settings_extra_codes|settings_custom_tags|settings_landing_page_information'])->name('dashboard.settings');
    }
    if (config('features.permissions.contact_us')) {
        Route::get('/contact_us', ListContactUs::class)->middleware(['permission:contact_us_show'])->name('dashboard.contact_us');
    }
    if (config('features.permissions.send_news')) {
        Route::get('/sends', ListSends::class)->middleware(['permission:send_news_show'])->name('dashboard.sends');
    }
    if (config('features.permissions.alerts')) {
        Route::get('/alerts', ListAlerts::class)->middleware(['permission:alerts_show'])->name('dashboard.alerts');
    }
    if (config('features.permissions.categories')) {
        Route::get('/categories/{add?}', ListCategories::class)->middleware(['permission:categories_show|categories_create|categories_edit'])->name('dashboard.categories');
    }
    if (config('features.permissions.publish_roles')) {
        Route::get('/publish_roles/{add?}', ListPublishRoles::class)->name('dashboard.publish_roles');
    }
    if (config('features.permissions.posts')) {
        Route::get('/pending_actions/{add?}', ListPendingActions::class)->middleware(['permission:posts_create|posts_create|posts_create|posts_publish'])->name('dashboard.pending_actions');
    }
    if (config('features.permissions.types')) {
        Route::get('/types/{add?}', ListTypes::class)->middleware(['permission:types_show|types_create|types_edit'])->name('dashboard.types');
    }
    if (config('features.permissions.special_files')) {
        Route::get('/special_files/{add?}', ListSpecialFiles::class)->middleware(['permission:special_files_show|special_files_create|special_files_edit'])->name('dashboard.special_files');
    }
    if (config('features.permissions.breaking_news')) {
        Route::get('/breaking_news/{add?}', ListBreakingNews::class)->middleware(['permission:breaking_news_show|breaking_news_create|breaking_news_edit'])->name('dashboard.breaking_news');
    }
    if (config('features.permissions.tags')) {
        Route::get('/tags/{add?}', ListTags::class)->middleware(['permission:tags_show|tags_create|tags_edit'])->name('dashboard.tags');
    }
    if (config('features.permissions.user_logs')) {
        Route::get('/user_logs', ListUserLogs::class)->middleware(['permission:user_logs_show'])->name('dashboard.user_logs');
    }
    if (config('features.permissions.sort_contents')) {
        Route::get('/sort_contents', ListSortContents::class)->name('dashboard.sort_contents');
    }
    if (config('features.permissions.icons')) {
        Route::get('/icons/{add?}', ListIcons::class)->middleware(['permission:icons_show|icons_create|icons_edit'])->name('dashboard.icons');
    }
    if (config('features.permissions.events')) {
        Route::get('/events/{add?}', ListEvents::class)->middleware(['permission:events_show|events_create|events_edit'])->name('dashboard.events');
    }
    if (config('features.permissions.news_letter_emails')) {
        Route::get('/news_letter_emails/{add?}', ListNewsLetterEmails::class)->middleware(['permission:news_letter_emails_show|news_letter_emails_create|news_letter_emails_edit'])->name('dashboard.news_letter_emails');
    }
    if (config('features.permissions.participants')) {
        Route::get('/participants/{add?}', ListParticipants::class)->middleware(['permission:participants_show|participants_create|participants_edit'])->name('dashboard.participants');
    }
    if (config('features.permissions.roles')) {
        Route::get('/roles/{add?}', ListRoles::class)->middleware(['permission:roles_show|roles_create|roles_edit'])->name('dashboard.roles');
    }
    if (config('features.permissions.user_details')) {
        Route::get('/user_details/{add?}', ListUserDetails::class)->middleware(['permission:user_details_show|user_details_create|user_details_edit'])->name('dashboard.user_details');
    }
    if (config('features.permissions.reels')) {
        Route::get('/reels', ListReels::class)->middleware(['permission:reels_show'])->name('dashboard.reels');
    }
    if (config('features.permissions.posts')) {
        Route::get('/create_update_post/{post_id?}', CreateUpdatePost::class)->middleware(['permission:posts_create|posts_edit'])->name('dashboard.posts.create_update_post');
    }
    if (config('features.permissions.posts')) {
        Route::get('/posts/{type?}/{id?}', ListPosts::class)->middleware(['permission:posts_show'])->name('dashboard.posts');
    }
    if (config('features.permissions.posts')) {
        Route::get('/draft_posts', ListDraftPosts::class)->middleware(['permission:posts_create|posts_edit'])->name('dashboard.draft_posts');
    }
    if (config('features.permissions.posts')) {
        Route::get('/deleted_posts', ListDeletedPosts::class)->middleware(['permission:posts_create|posts_edit'])->name('dashboard.deleted_posts');
    }
    if (config('features.permissions.courses')) {
        Route::get('/courses', ListCourses::class)->middleware(['permission:courses_show'])->name('dashboard.courses');
    }
    if (config('features.permissions.services')) {
        Route::get('/services', ListServices::class)->middleware(['permission:services_show'])->name('dashboard.services');
    }
    if (config('features.permissions.applications')) {
        Route::get('/applications', ListApplications::class)->middleware(['permission:applications_show'])->name('dashboard.applications');
    }
    if (config('features.permissions.subscribers')) {
        Route::get('/subscribers', ListSubscribers::class)->middleware(['permission:subscribers_show'])->name('dashboard.subscribers');
    }
    if (config('features.permissions.subscriber_notifications')) {
        Route::get('/subscriber_notifications', ListSubscriberNotifications::class)->middleware(['permission:subscriber_notifications_show'])->name('dashboard.subscriber_notifications');
    }
    if (config('features.permissions.subscriptions')) {
        Route::get('/subscriptions', ListSubscriptions::class)->middleware(['permission:subscriptions_show'])->name('dashboard.subscriptions');
    }
    if (config('features.permissions.materials')) {
        Route::prefix('materials')->group(function () {
            Route::get('/tracks/{add?}', ListMaterials::class)->middleware(['permission:materials_show|materials_create|materials_edit'])->name('dashboard.materials');
            Route::get('/albums/{add?}', ListMaterialAlbums::class)->middleware(['permission:materials_albums_show|materials_albums_create|materials_albums_edit'])->name('dashboard.materials.albums');
        });
    }
    if (config('features.permissions.advertisements')) {
        Route::prefix('advertisements')->group(function () {
            Route::get('/', ListAdvertisements::class)->middleware(['permission:advertisements_show'])->name('dashboard.advertisements');
            Route::get('/create_update_post/{advertisement_id?}', CreateUpdateAdvertisement::class)->middleware(['permission:advertisements_create|advertisements_edit'])->name('dashboard.advertisements.create_update_advertisement');
        });
    }
    if (config('features.permissions.special_pages')) {
        Route::prefix('special_pages')->group(function () {
            Route::get('/', ListSpecialPages::class)->middleware(['permission:special_pages_show'])->name('dashboard.special_pages');
            Route::get('/create_update_special_page/{special_page_id?}', CreateUpdateSpecialPages::class)->middleware(['permission:special_pages_create|special_pages_edit'])->name('dashboard.special_pages.create_update_special_page');
        });
    }
    if (config('features.permissions.permissions')) {
        Route::prefix('permissions')->group(function () {
            Route::get('/', ListPermissions::class)->middleware(['permission:permissions_show'])->name('dashboard.permissions');
            Route::get('/create_update_permission/{role_id?}', CreateUpdatePermission::class)->middleware(['permission:permissions_edit|permissions_create'])->name('dashboard.permissions.create_update_permission');
        });
    }
});
Route::get('/login', Login::class)->name('login');
Route::get('/logout', [Login::class, 'logout'])->name('logout_');
Route::get('/change-language/{lang}', [LanguageController::class, 'changeLanguage'])->name('dashboard.change-language');
//rss feed
Route::get('/rss', [RSSFeedController::class, 'index'])->name('main.rss_feed');
//palestine_post
if (config('app.launch') === 'palestine_post') {
    Route::prefix('/')->group(function () {
        Route::get('/', App\Livewire\Main\PalestinePost\IndexPage::class)->name('main.palestine_post.index');
        Route::get('/category_news/{category_title}', App\Livewire\Main\PalestinePost\CategoryNews::class)->name('main.palestine_post.category_news');
        Route::get('/tag_news/{tag_name}', App\Livewire\Main\PalestinePost\TagNews::class)->name('main.palestine_post.tag_news');
        Route::get('/all_posts', App\Livewire\Main\PalestinePost\AllPosts::class)->name('main.palestine_post.all_posts');
        Route::get('/all_podcast', App\Livewire\Main\PalestinePost\AllPodcastAlbums::class)->name('main.palestine_post.all_podcast_albums');
        Route::get('/all_videos', App\Livewire\Main\PalestinePost\AllVideoAlbums::class)->name('main.palestine_post.all_video_albums');
        Route::get('/post', App\Livewire\Main\PalestinePost\SinglePost::class)->name('main.palestine_post.show_post');
        Route::get('/share/{id}', function ($id) {
            $post = Post::findOrFail($id);
            return redirect()->route('main.palestine_post.show_post', ['id' => $post->id, 'slug' => $post->slug]);
        })->name('main.palestine_post.share');
        Route::get('/special_page/{id}', App\Livewire\Main\PalestinePost\SpecialPages::class)->name('main.palestine_post.special_page');
        Route::get('/send_news', App\Livewire\Main\PalestinePost\SendNews::class)->name('main.palestine_post.send_news');
        Route::get('/search_page/{search?}', App\Livewire\Main\PalestinePost\SearchPage::class)->name('main.palestine_post.search_page');
        Route::get('/sitemap', App\Livewire\Main\PalestinePost\SiteMap::class)->name('main.palestine_post.sitemap');
        Route::get('/site_widgets', App\Livewire\Main\PalestinePost\SiteWidgets::class)->name('main.palestine_post.site_widgets');
        Route::get('/single_article', \App\Livewire\Main\PalestinePost\SingleArticle::class)->name('main.palestine_post.article');
        Route::get('/writer/{author_id}/{author_name}', \App\Livewire\Main\PalestinePost\Writer::class)->name('main.palestine_post.writer');
        Route::get('/about_us', \App\Livewire\Main\PalestinePost\AboutUs::class)->name('main.palestine_post.about_us');
        Route::get('/single_podcast/{podcast_album_id}/{album_name}', \App\Livewire\Main\PalestinePost\SinglePodcast::class)->name('main.palestine_post.podcast');
        Route::get('/single_video/{category_id?}/{category_title?}', \App\Livewire\Main\PalestinePost\SingleVideo::class)->name('main.palestine_post.single_video');
        Route::get('/rss/category/all', [RSSFeedController::class, 'index'])->name('main.rss_feed');

    });
}