<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Users table - search and sorting optimization
        $this->addIndexIfMissing('users', ['email'], 'users_email_search_index');
        $this->addIndexIfMissing('users', ['first_name', 'last_name'], 'users_name_search_index');
        $this->addIndexIfMissing('users', ['created_at'], 'users_created_at_index');
        $this->addIndexIfMissing('users', ['user_status'], 'users_status_index');

        // Categories table
        $this->addIndexIfMissing('categories', ['category_type'], 'categories_type_index');
        $this->addIndexIfMissing('categories', ['category_title'], 'categories_title_index');
        $this->addIndexIfMissing('categories', ['created_at'], 'categories_created_at_index');

        // Tags table
        $this->addIndexIfMissing('tags', ['tag_name'], 'tags_name_index');
        $this->addIndexIfMissing('tags', ['tag_type'], 'tags_type_index');
        $this->addIndexIfMissing('tags', ['created_at'], 'tags_created_at_index');

        // Participants table
        $this->addIndexIfMissing('participants', ['name'], 'participants_name_index');
        $this->addIndexIfMissing('participants', ['type'], 'participants_type_index');
        $this->addIndexIfMissing('participants', ['created_at'], 'participants_created_at_index');

        // Quotes table
        $this->addIndexIfMissing('quotes', ['quote_from'], 'quotes_from_index');
        $this->addIndexIfMissing('quotes', ['created_at'], 'quotes_created_at_index');

        // Contact Us table
        $this->addIndexIfMissing('contact_us', ['email'], 'contact_us_email_index');
        $this->addIndexIfMissing('contact_us', ['full_name'], 'contact_us_name_index');
        $this->addIndexIfMissing('contact_us', ['created_at'], 'contact_us_created_at_index');

        // Advertisements table
        $this->addIndexIfMissing('advertisements', ['user_id'], 'advertisements_user_id_index');
        $this->addIndexIfMissing('advertisements', ['title'], 'advertisements_title_index');
        $this->addIndexIfMissing('advertisements', ['type'], 'advertisements_type_index');
        $this->addIndexIfMissing('advertisements', ['place'], 'advertisements_place_index');
        $this->addIndexIfMissing('advertisements', ['created_at'], 'advertisements_created_at_index');

        // Breaking News table
        $this->addIndexIfMissing('breaking_news', ['hide_date'], 'breaking_news_hide_date_index');
        $this->addIndexIfMissing('breaking_news', ['created_at'], 'breaking_news_created_at_index');

        // Special Files table
        $this->addIndexIfMissing('special_files', ['created_at'], 'special_files_created_at_index');

        // Types table
        $this->addIndexIfMissing('types', ['type_name'], 'types_name_index');
        $this->addIndexIfMissing('types', ['created_at'], 'types_created_at_index');

        // Icons table  
        $this->addIndexIfMissing('icons', ['icon_name'], 'icons_name_index');
        $this->addIndexIfMissing('icons', ['created_at'], 'icons_created_at_index');

        // Events table
        $this->addIndexIfMissing('events', ['start_date'], 'events_start_date_index');
        $this->addIndexIfMissing('events', ['end_date'], 'events_end_date_index');
        $this->addIndexIfMissing('events', ['created_at'], 'events_created_at_index');

        // News Letter Emails table
        $this->addIndexIfMissing('news_letter_emails', ['email'], 'news_letter_emails_email_index');
        $this->addIndexIfMissing('news_letter_emails', ['created_at'], 'news_letter_emails_created_at_index');

        // Roles table (Spatie)
        $this->addIndexIfMissing('roles', ['name'], 'roles_name_index');

        // Alerts table
        $this->addIndexIfMissing('alerts', ['created_at'], 'alerts_created_at_index');

        // Posts table - critical for performance
        $this->addIndexIfMissing('posts', ['publish_status'], 'posts_status_index');
        $this->addIndexIfMissing('posts', ['publisher_id'], 'posts_publisher_id_index');
        $this->addIndexIfMissing('posts', ['post_type'], 'posts_type_index');
        $this->addIndexIfMissing('posts', ['deleted_at'], 'posts_deleted_at_index');

        // Materials table
        $this->addIndexIfMissing('materials', ['title'], 'materials_title_index');
        $this->addIndexIfMissing('materials', ['material_album_id'], 'materials_album_id_index');

        // Subscribers table
        $this->addIndexIfMissing('subscribers', ['email'], 'subscribers_email_index');
        $this->addIndexIfMissing('subscribers', ['created_at'], 'subscribers_created_at_index');

        // Subscriptions table
        $this->addIndexIfMissing('subscriptions', ['subscriber_id'], 'subscriptions_subscriber_id_index');
        $this->addIndexIfMissing('subscriptions', ['subscription_start'], 'subscriptions_start_index');
        $this->addIndexIfMissing('subscriptions', ['subscription_end'], 'subscriptions_end_index');

        // Subscriber Notifications table
        $this->addIndexIfMissing('subscriber_notifications', ['type'], 'subscriber_notifications_type_index');
        $this->addIndexIfMissing('subscriber_notifications', ['created_at'], 'subscriber_notifications_created_at_index');

        // Applications table
        $this->addIndexIfMissing('applications', ['created_at'], 'applications_created_at_index');

        // Services table
        $this->addIndexIfMissing('services', ['created_at'], 'services_created_at_index');

        // Courses table
        $this->addIndexIfMissing('courses', ['created_at'], 'courses_created_at_index');

        // Reels table
        $this->addIndexIfMissing('reels', ['created_at'], 'reels_created_at_index');
    }

    public function down(): void
    {
        // Users
        $this->dropIndexIfExists('users', 'users_email_search_index');
        $this->dropIndexIfExists('users', 'users_name_search_index');
        $this->dropIndexIfExists('users', 'users_created_at_index');
        $this->dropIndexIfExists('users', 'users_status_index');

        // Categories
        $this->dropIndexIfExists('categories', 'categories_type_index');
        $this->dropIndexIfExists('categories', 'categories_title_index');
        $this->dropIndexIfExists('categories', 'categories_created_at_index');

        // Tags
        $this->dropIndexIfExists('tags', 'tags_name_index');
        $this->dropIndexIfExists('tags', 'tags_type_index');
        $this->dropIndexIfExists('tags', 'tags_created_at_index');

        // Participants
        $this->dropIndexIfExists('participants', 'participants_name_index');
        $this->dropIndexIfExists('participants', 'participants_type_index');
        $this->dropIndexIfExists('participants', 'participants_created_at_index');

        // Quotes
        $this->dropIndexIfExists('quotes', 'quotes_from_index');
        $this->dropIndexIfExists('quotes', 'quotes_created_at_index');

        // Contact Us
        $this->dropIndexIfExists('contact_us', 'contact_us_email_index');
        $this->dropIndexIfExists('contact_us', 'contact_us_name_index');
        $this->dropIndexIfExists('contact_us', 'contact_us_created_at_index');

        // Advertisements
        $this->dropIndexIfExists('advertisements', 'advertisements_user_id_index');
        $this->dropIndexIfExists('advertisements', 'advertisements_title_index');
        $this->dropIndexIfExists('advertisements', 'advertisements_type_index');
        $this->dropIndexIfExists('advertisements', 'advertisements_place_index');
        $this->dropIndexIfExists('advertisements', 'advertisements_created_at_index');

        // Breaking News
        $this->dropIndexIfExists('breaking_news', 'breaking_news_hide_date_index');
        $this->dropIndexIfExists('breaking_news', 'breaking_news_created_at_index');

        // Special Files
        $this->dropIndexIfExists('special_files', 'special_files_created_at_index');

        // Types
        $this->dropIndexIfExists('types', 'types_name_index');
        $this->dropIndexIfExists('types', 'types_created_at_index');

        // Icons
        $this->dropIndexIfExists('icons', 'icons_name_index');
        $this->dropIndexIfExists('icons', 'icons_created_at_index');

        // Events
        $this->dropIndexIfExists('events', 'events_start_date_index');
        $this->dropIndexIfExists('events', 'events_end_date_index');
        $this->dropIndexIfExists('events', 'events_created_at_index');

        // News Letter Emails
        $this->dropIndexIfExists('news_letter_emails', 'news_letter_emails_email_index');
        $this->dropIndexIfExists('news_letter_emails', 'news_letter_emails_created_at_index');

        // Roles
        $this->dropIndexIfExists('roles', 'roles_name_index');

        // Alerts
        $this->dropIndexIfExists('alerts', 'alerts_created_at_index');

        // Posts
        $this->dropIndexIfExists('posts', 'posts_status_index');
        $this->dropIndexIfExists('posts', 'posts_publisher_id_index');
        $this->dropIndexIfExists('posts', 'posts_type_index');
        $this->dropIndexIfExists('posts', 'posts_deleted_at_index');

        // Materials
        $this->dropIndexIfExists('materials', 'materials_title_index');
        $this->dropIndexIfExists('materials', 'materials_album_id_index');

        // Subscribers
        $this->dropIndexIfExists('subscribers', 'subscribers_email_index');
        $this->dropIndexIfExists('subscribers', 'subscribers_created_at_index');

        // Subscriptions
        $this->dropIndexIfExists('subscriptions', 'subscriptions_subscriber_id_index');
        $this->dropIndexIfExists('subscriptions', 'subscriptions_start_index');
        $this->dropIndexIfExists('subscriptions', 'subscriptions_end_index');

        // Subscriber Notifications
        $this->dropIndexIfExists('subscriber_notifications', 'subscriber_notifications_type_index');
        $this->dropIndexIfExists('subscriber_notifications', 'subscriber_notifications_created_at_index');

        // Applications, Services, Courses, Reels
        $this->dropIndexIfExists('applications', 'applications_created_at_index');
        $this->dropIndexIfExists('services', 'services_created_at_index');
        $this->dropIndexIfExists('courses', 'courses_created_at_index');
        $this->dropIndexIfExists('reels', 'reels_created_at_index');
    }

    /* ---------- Helpers ---------- */

    private function addIndexIfMissing(string $table, array $columns, string $indexName): void
    {
        if (!Schema::hasTable($table)) {
            return; // Skip if table doesn't exist
        }

        if (!$this->indexExists($table, $indexName)) {
            try {
                Schema::table($table, function (Blueprint $t) use ($columns, $indexName) {
                    $t->index($columns, $indexName);
                });
            } catch (\Exception $e) {
                // Silently skip if column doesn't exist
            }
        }
    }

    private function dropIndexIfExists(string $table, string $indexName): void
    {
        if (!Schema::hasTable($table)) {
            return;
        }

        if ($this->indexExists($table, $indexName)) {
            try {
                Schema::table($table, function (Blueprint $t) use ($indexName) {
                    $t->dropIndex($indexName);
                });
            } catch (\Exception $e) {
                // Silently skip
            }
        }
    }

    private function indexExists(string $table, string $indexName): bool
    {
        $schema = DB::getDatabaseName();
        $res = DB::select(
            'SELECT COUNT(1) AS c
             FROM information_schema.statistics
             WHERE table_schema = ? AND table_name = ? AND index_name = ?',
            [$schema, $table, $indexName]
        );
        return !empty($res) && (int)$res[0]->c > 0;
    }
};
