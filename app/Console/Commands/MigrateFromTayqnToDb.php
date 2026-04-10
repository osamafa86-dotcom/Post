<?php

namespace App\Console\Commands;

use App\Enums\ParticipantTypeEnum;
use App\Enums\TagTypeEnum;
use App\Models\Category;
use App\Models\Files;
use App\Models\Participant;
use App\Models\Post;
use App\Models\PostRelation;
use App\Models\Tag;
use App\Models\Type;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MigrateFromTayqnToDb extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:db2-to-db1
                            {--connection=db2 : The source database connection name}
                            {--dry-run : Run without committing changes}
                            {--skip-categories : Skip categories migration}
                            {--skip-participants : Skip participants migration}
                            {--skip-tags : Skip tags migration}
                            {--skip-types : Skip claim types migration}
                            {--skip-uploads : Skip uploads/files migration}
                            {--skip-posts : Skip posts migration (claims + news)}
                            {--skip-relations : Skip relations migration}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate data from db2 (tayqn) to db1 (taktek_almohajer): categories, tags, types, uploads, claims/news->posts, authors/publishers/resources->participants';

    /**
     * Statistics tracking
     */
    protected $stats = [
        'categories' => ['inserted' => 0, 'updated' => 0, 'skipped' => 0, 'errors' => 0],
        'tags' => ['inserted' => 0, 'updated' => 0, 'skipped' => 0, 'errors' => 0],
        'types' => ['inserted' => 0, 'updated' => 0, 'skipped' => 0, 'errors' => 0],
        'uploads' => ['inserted' => 0, 'updated' => 0, 'skipped' => 0, 'errors' => 0],
        'authors' => ['inserted' => 0, 'updated' => 0, 'skipped' => 0, 'errors' => 0],
        'publishers' => ['inserted' => 0, 'updated' => 0, 'skipped' => 0, 'errors' => 0],
        'resources' => ['inserted' => 0, 'updated' => 0, 'skipped' => 0, 'errors' => 0],
        'claims' => ['inserted' => 0, 'updated' => 0, 'skipped' => 0, 'errors' => 0],
        'news' => ['inserted' => 0, 'updated' => 0, 'skipped' => 0, 'errors' => 0],
        'post_relations' => ['inserted' => 0, 'updated' => 0, 'skipped' => 0, 'errors' => 0],
        'views' => ['inserted' => 0, 'updated' => 0, 'skipped' => 0, 'errors' => 0],
    ];

    /**
     * ID mappings from old DB to new DB
     */
    protected $mappings = [
        'categories' => [],
        'tags' => [],
        'types' => [],
        'uploads' => [],
        'authors' => [],
        'publishers' => [],
        'resources' => [],
        'claims' => [],
        'news' => [],
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('========================================');
        $this->info('Starting Database Migration: db2 → db1');
        $this->info('========================================');
        $this->newLine();

        // Confirm before proceeding
        if (!$this->option('dry-run') && !$this->confirm('This will migrate data from db2 to db1. Do you want to continue?')) {
            $this->error('Migration cancelled.');
            return 1;
        }

        if ($this->option('dry-run')) {
            $this->warn('🔍 DRY RUN MODE - No changes will be committed');
            $this->newLine();
        }

        try {
            DB::beginTransaction();

            // 1. Migrate Categories
            if (!$this->option('skip-categories')) {
                $this->migrateCategories();
            }

            // 2. Migrate Tags
            if (!$this->option('skip-tags')) {
                $this->migrateTags();
            }

            // 3. Migrate Claim Types to Types
            if (!$this->option('skip-types')) {
                $this->migrateClaimTypes();
            }

            // 4. Migrate Uploads/Files
            if (!$this->option('skip-uploads')) {
                $this->migrateUploads();
            }

            // 5. Migrate Participants (Authors, Publishers, Resources)
            if (!$this->option('skip-participants')) {
                $this->migrateParticipants();
            }

            // 6. Migrate Claims to Posts
            if (!$this->option('skip-posts')) {
                $this->migrateClaims();
                $this->migrateNews();
            }

            // 7. Migrate Relations
            if (!$this->option('skip-relations')) {
                $this->migrateRelations();
                $this->migrateTagRelations();
                $this->migrateTypeRelations();
            }

            // 8. Migrate Views
            $this->migrateViews();

            if ($this->option('dry-run')) {
                DB::rollBack();
                $this->warn('🔄 Rolled back all changes (dry-run mode)');
            } else {
                DB::commit();
                $this->info('✅ All changes committed successfully!');
            }

            $this->newLine();
            $this->displayStatistics();

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('❌ Migration failed: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
            Log::error('Migration failed', ['exception' => $e]);
            return 1;
        }

        $this->newLine();
        $this->info('========================================');
        $this->info('Migration completed successfully! ✨');
        $this->info('========================================');

        return 0;
    }

    /**
     * Migrate Categories from db2 to db1
     * Each translation creates a separate category record with lang field
     */
    protected function migrateCategories()
    {
        $this->info('📁 Migrating Categories...');
        $connection = $this->option('connection');

        // Get categories from db2
        $categories = DB::connection($connection)
            ->table('categories')
            ->whereNull('deleted_at')
            ->orderBy('id')
            ->get();

        $totalTranslations = 0;

        // Count total translations for progress bar
        foreach ($categories as $category) {
            $count = DB::connection($connection)
                ->table('category_translations')
                ->where('category_id', $category->id)
                ->whereNull('deleted_at')
                ->count();
            $totalTranslations += $count;
        }

        $bar = $this->output->createProgressBar($totalTranslations);
        $bar->start();

        foreach ($categories as $oldCategory) {
            try {
                // Get translations for this category
                $translations = DB::connection($connection)
                    ->table('category_translations')
                    ->where('category_id', $oldCategory->id)
                    ->whereNull('deleted_at')
                    ->get();

                // Create a separate category record for each translation
                foreach ($translations as $translation) {
                    // Check if category already exists for this language
                    $existingCategory = Category::where('category_title', $translation->name)
                        ->where('lang', $translation->locale)
                        ->first();

                    if ($existingCategory) {
                        $this->stats['categories']['skipped']++;
                        // Store mapping with locale suffix
                        $this->mappings['categories'][$oldCategory->id . '_' . $translation->locale] = $existingCategory->id;
                    } else {
                        // Create new category record for this language
                        $newCategory = Category::create([
                            'lang' => $translation->locale,
                            'category_title' => $translation->name ?? $oldCategory->type ?? 'Untitled',
                            'category_description' => null,
                            'parent_id' => $oldCategory->parent_id ?? null,
                            'category_type' => 1,
                            'category_style' => null,
                            'show_index' => 0,
                            'team_id' => 1, // Default team
                            'order' => null,
                            'created_at' => $oldCategory->created_at,
                            'updated_at' => $oldCategory->updated_at,
                        ]);

                        // Store mapping with locale suffix for proper relation linking
                        $this->mappings['categories'][$oldCategory->id . '_' . $translation->locale] = $newCategory->id;
                        $this->stats['categories']['inserted']++;
                    }

                    $bar->advance();
                }

            } catch (\Exception $e) {
                $this->stats['categories']['errors']++;
                Log::error("Failed to migrate category {$oldCategory->id}: " . $e->getMessage());
                $bar->advance();
            }
        }

        $bar->finish();
        $this->newLine();
        $this->info("✓ Categories migration completed");
    }

    /**
     * Migrate Participants (Authors, Publishers, Resources)
     */
    protected function migrateParticipants()
    {
        $this->info('👥 Migrating Participants...');

        // Migrate Authors
        $this->migrateAuthors();

        // Migrate Publishers
        $this->migratePublishers();

        // Migrate Resources
        $this->migrateResources();

        $this->info("✓ Participants migration completed");
    }

    /**
     * Migrate Authors to Participants
     */
    protected function migrateAuthors()
    {
        $this->line('  → Migrating Authors...');
        $connection = $this->option('connection');

        $authors = DB::connection($connection)
            ->table('authors')
            ->whereNull('deleted_at')
            ->get();

        $bar = $this->output->createProgressBar($authors->count());
        $bar->start();

        foreach ($authors as $author) {
            try {
                // Get translations for this author
                $translations = DB::connection($connection)
                    ->table('author_translations')
                    ->where('author_id', $author->id)
                    ->whereNull('deleted_at')
                    ->get();

                foreach ($translations as $translation) {
                    // Check if participant already exists
                    $existingParticipant = Participant::where('name', $translation->name)
                        ->where('type', ParticipantTypeEnum::AUTHORS->value)
                        ->where('lang', $translation->locale)
                        ->first();

                    if ($existingParticipant) {
                        $this->mappings['authors'][$author->id . '_' . $translation->locale] = $existingParticipant->id;
                        $this->stats['authors']['skipped']++;
                    } else {
                        $participant = Participant::create([
                            'lang' => $translation->locale,
                            'name' => $translation->name ?? 'Unknown Author',
                            'image' => null,
                            'work' => $translation->job_title ?? null,
                            'description' => $translation->info ?? null,
                            'type' => ParticipantTypeEnum::AUTHORS->value,
                            'team_id' => 1,
                            'participants_data' => [
                                'old_author_id' => $author->id,
                                'status' => $author->status,
                                'upload_id' => $author->upload_id,
                            ],
                            'created_at' => $author->created_at,
                            'updated_at' => $author->updated_at,
                        ]);

                        $this->mappings['authors'][$author->id . '_' . $translation->locale] = $participant->id;
                        $this->stats['authors']['inserted']++;

                        // Link image via model_has_files if upload mapping exists
                        if (!empty($author->upload_id) && isset($this->mappings['uploads'][$author->upload_id])) {
                            $fileId = $this->mappings['uploads'][$author->upload_id];
                            DB::table('model_has_files')->insert([
                                'file_id' => $fileId,
                                'model_type' => Participant::class,
                                'model_id' => $participant->id,
                                'model_column' => 'image',
                                'team_id' => 1,
                                'created_at' => $author->created_at,
                                'updated_at' => $author->updated_at,
                            ]);
                        }
                    }
                }

                $bar->advance();
            } catch (\Exception $e) {
                $this->stats['authors']['errors']++;
                Log::error("Failed to migrate author {$author->id}: " . $e->getMessage());
                $bar->advance();
            }
        }

        $bar->finish();
        $this->newLine();
    }

    /**
     * Migrate Publishers to Participants
     */
    protected function migratePublishers()
    {
        $this->line('  → Migrating Publishers...');
        $connection = $this->option('connection');

        $publishers = DB::connection($connection)
            ->table('publishers')
            ->whereNull('deleted_at')
            ->get();

        $bar = $this->output->createProgressBar($publishers->count());
        $bar->start();

        foreach ($publishers as $publisher) {
            try {
                // Get translations for this publisher
                $translations = DB::connection($connection)
                    ->table('publisher_translations')
                    ->where('publisher_id', $publisher->id)
                    ->whereNull('deleted_at')
                    ->get();

                foreach ($translations as $translation) {
                    $existingParticipant = Participant::where('name', $translation->name)
                        ->where('type', ParticipantTypeEnum::PUBLISHERS->value)
                        ->where('lang', $translation->locale)
                        ->first();

                    if ($existingParticipant) {
                        $this->mappings['publishers'][$publisher->id . '_' . $translation->locale] = $existingParticipant->id;
                        $this->stats['publishers']['skipped']++;
                    } else {
                        $participant = Participant::create([
                            'lang' => $translation->locale,
                            'name' => $translation->name ?? $translation->new_name ?? 'Unknown Publisher',
                            'image' => null,
                            'work' => null,
                            'description' => null,
                            'type' => ParticipantTypeEnum::PUBLISHERS->value,
                            'team_id' => 1,
                            'participants_data' => [
                                'old_publisher_id' => $publisher->id,
                                'status' => $publisher->status,
                                'visible' => $publisher->visible,
                                'url' => $publisher->url,
                                'type' => $publisher->type,
                            ],
                            'created_at' => $publisher->created_at,
                            'updated_at' => $publisher->updated_at,
                        ]);

                        $this->mappings['publishers'][$publisher->id . '_' . $translation->locale] = $participant->id;
                        $this->stats['publishers']['inserted']++;

                        // Create/find file from image path and link via model_has_files
                        if (!empty($publisher->image)) {
                            $existingFile = Files::where('path', $publisher->image)->first();
                            if (!$existingFile) {
                                $basename = basename($publisher->image);
                                $extension = pathinfo($basename, PATHINFO_EXTENSION) ?: null;
                                $existingFile = Files::create([
                                    'path' => $publisher->image,
                                    'original_name' => $basename,
                                    'file_name' => $basename,
                                    'extension' => $extension,
                                    'size' => null,
                                    'duration' => null,
                                    'team_id' => 1,
                                    'created_at' => $publisher->created_at,
                                    'updated_at' => $publisher->updated_at,
                                ]);
                            }
                            DB::table('model_has_files')->insert([
                                'file_id' => $existingFile->id,
                                'model_type' => Participant::class,
                                'model_id' => $participant->id,
                                'model_column' => 'image',
                                'team_id' => 1,
                                'created_at' => $publisher->created_at,
                                'updated_at' => $publisher->updated_at,
                            ]);
                        }
                    }
                }

                $bar->advance();
            } catch (\Exception $e) {
                $this->stats['publishers']['errors']++;
                Log::error("Failed to migrate publisher {$publisher->id}: " . $e->getMessage());
                $bar->advance();
            }
        }

        $bar->finish();
        $this->newLine();
    }

    /**
     * Migrate Resources to Participants
     */
    protected function migrateResources()
    {
        $this->line('  → Migrating Resources...');
        $connection = $this->option('connection');

        $resources = DB::connection($connection)
            ->table('resources')
            ->whereNull('deleted_at')
            ->get();

        $bar = $this->output->createProgressBar($resources->count());
        $bar->start();

        foreach ($resources as $resource) {
            try {
                // Get translations for this resource
                $translations = DB::connection($connection)
                    ->table('resource_translations')
                    ->where('resource_id', $resource->id)
                    ->whereNull('deleted_at')
                    ->get();

                foreach ($translations as $translation) {
                    $existingParticipant = Participant::where('name', $translation->name)
                        ->where('type', ParticipantTypeEnum::RESOURCES->value)
                        ->where('lang', $translation->locale)
                        ->first();

                    if ($existingParticipant) {
                        $this->mappings['resources'][$resource->id . '_' . $translation->locale] = $existingParticipant->id;
                        $this->stats['resources']['skipped']++;
                    } else {
                        $participant = Participant::create([
                            'lang' => $translation->locale,
                            'name' => $translation->name ?? 'Unknown Resource',
                            'image' => null,
                            'work' => null,
                            'description' => null,
                            'type' => ParticipantTypeEnum::RESOURCES->value,
                            'team_id' => 1,
                            'participants_data' => [
                                'old_resource_id' => $resource->id,
                                'status' => $resource->status,
                                'url' => $resource->url ?? null,
                                'type' => $resource->type ?? null,
                            ],
                            'created_at' => $resource->created_at,
                            'updated_at' => $resource->updated_at,
                        ]);

                        $this->mappings['resources'][$resource->id . '_' . $translation->locale] = $participant->id;
                        $this->stats['resources']['inserted']++;

                        // Create/find file from image path and link via model_has_files
                        if (!empty($resource->image)) {
                            $existingFile = Files::where('path', $resource->image)->first();
                            if (!$existingFile) {
                                $basename = basename($resource->image);
                                $extension = pathinfo($basename, PATHINFO_EXTENSION) ?: null;
                                $existingFile = Files::create([
                                    'path' => $resource->image,
                                    'original_name' => $basename,
                                    'file_name' => $basename,
                                    'extension' => $extension,
                                    'size' => null,
                                    'duration' => null,
                                    'team_id' => 1,
                                    'created_at' => $resource->created_at,
                                    'updated_at' => $resource->updated_at,
                                ]);
                            }
                            DB::table('model_has_files')->insert([
                                'file_id' => $existingFile->id,
                                'model_type' => Participant::class,
                                'model_id' => $participant->id,
                                'model_column' => 'image',
                                'team_id' => 1,
                                'created_at' => $resource->created_at,
                                'updated_at' => $resource->updated_at,
                            ]);
                        }
                    }
                }

                $bar->advance();
            } catch (\Exception $e) {
                $this->stats['resources']['errors']++;
                Log::error("Failed to migrate resource {$resource->id}: " . $e->getMessage());
                $bar->advance();
            }
        }

        $bar->finish();
        $this->newLine();
    }

    /**
     * Migrate Claims to Posts
     */
    protected function migrateClaims()
    {
        $this->info('📝 Migrating Claims to Posts...');
        $connection = $this->option('connection');

        $claims = DB::connection($connection)
            ->table('claims')
            ->whereNull('deleted_at')
            ->orderBy('id')
            ->get();

        $bar = $this->output->createProgressBar($claims->count());
        $bar->start();

        foreach ($claims as $claim) {
            try {
                // Get translations for this claim
                $translations = DB::connection($connection)
                    ->table('claim_translations')
                    ->where('claim_id', $claim->id)
                    ->whereNull('deleted_at')
                    ->get();

                foreach ($translations as $translation) {
                    // Check if post already exists
                    $existingPost = Post::where('title', $translation->title)
                        ->where('lang', $translation->locale)
                        ->first();

                    if ($existingPost) {
                        $this->mappings['claims'][$claim->id . '_' . $translation->locale] = $existingPost->id;
                        $this->stats['claims']['skipped']++;
                    } else {
                        // Map category ID - use locale-specific mapping
                        $newCategoryId = $this->mappings['categories'][$claim->category_id . '_' . $translation->locale] ?? null;

                        // Get upload/file mapping if exists
                        $uploadId = null;
                        if ($claim->upload_id && isset($this->mappings['uploads'][$claim->upload_id])) {
                            $uploadId = $this->mappings['uploads'][$claim->upload_id];
                        }

                        $post = Post::create([
                            'lang' => $translation->locale,
                            'publish_date' => $claim->published_at ?? $claim->created_at,
                            'order_number' => null,
                            'title' => $translation->title ?? 'Untitled',
                            'slug' => Str::replace(' ', '-', $translation->title),
                            'image' => null, // Will be linked via model_has_files if upload exists
                            'video_url' => null,
                            'pdf_url' => null,
                            'image_alt' => null,
                            'news_type' => null,
                            'image_size' => null,
                            'description' => $translation->summary ?? $translation->info ?? null,
                            'body' => $this->buildClaimBody($translation),
                            'updates' => 0,
                            'publish_status' => $this->mapPublishStatus($claim->status),
                            'user_id' => 1, // Default user
                            'publisher_id' => null,
                            'team_id' => $claim->team_id ?? 1,
                            'old_id' => $claim->id, // Store reference to original claim ID
                            'created_at' => $claim->created_at,
                            'updated_at' => $claim->updated_at,
                        ]);

                        $this->mappings['claims'][$claim->id . '_' . $translation->locale] = $post->id;
                        $this->stats['claims']['inserted']++;

                        // Create category relation if category exists for this locale
                        if ($newCategoryId) {
                            PostRelation::create([
                                'post_id' => $post->id,
                                'relationable_type' => Category::class,
                                'relationable_id' => $newCategoryId,
                                'relationable_is_main' => 1,
                                'team_id' => $post->team_id,
                                'lang' => $translation->locale,
                                'created_at' => $claim->created_at,
                                'updated_at' => $claim->updated_at,
                            ]);
                        }

                        // Link upload/file if exists
                        if ($uploadId) {
                            DB::table('model_has_files')->insert([
                                'file_id' => $uploadId,
                                'model_type' => Post::class,
                                'model_id' => $post->id,
                                'model_column' => 'image',
                                'team_id' => $post->team_id,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                    }
                }

                $bar->advance();
            } catch (\Exception $e) {
                $this->stats['claims']['errors']++;
                Log::error("Failed to migrate claim {$claim->id}: " . $e->getMessage());
                $bar->advance();
            }
        }

        $bar->finish();
        $this->newLine();
        $this->info("✓ Claims migration completed");
    }

    /**
     * Build claim body from translations
     */
    protected function buildClaimBody($translation)
    {
        $body = '';

        if (!empty($translation->clime)) {
            $body .= "<h3>الإدعاء</h3>\n" . $translation->clime . "\n\n";
        }

        if (!empty($translation->clime)) {
            $body .= "<h3>الادعاء</h3>\n" . $translation->clime . "\n\n";
        }

        if (!empty($translation->check)) {
            $body .= "<h3>التحقق</h3>\n" . $translation->check . "\n\n";
        }

        if (!empty($translation->text)) {
            $body .= $translation->text;
        }

        return $body ?: null;
    }

    /**
     * Map publish status from db2 to db1
     */
    protected function mapPublishStatus($status)
    {
        // Map db2 status to db1 publish_status enum
        // You may need to adjust these mappings based on your actual enums
        return match ($status) {
            1 => 1, // Published
            0, 2 => 2, // Draft or pending
            default => 2, // Default to draft
        };
    }

    /**
     * Migrate Relations (claim_publisher, claim_resource)
     */
    protected function migrateRelations()
    {
        $this->info('🔗 Migrating Post Relations...');
        $connection = $this->option('connection');

        // Migrate claim_publisher relations
        $this->migrateClaimPublishers($connection);

        // Migrate claim_resource relations
        $this->migrateClaimResources($connection);

        $this->info("✓ Relations migration completed");
    }

    /**
     * Migrate claim_publisher to post_relations
     */
    protected function migrateClaimPublishers($connection)
    {
        $this->line('  → Migrating Claim-Publisher relations...');

        $relations = DB::connection($connection)
            ->table('claim_publisher')
            ->whereNull('deleted_at')
            ->get();

        $bar = $this->output->createProgressBar($relations->count());
        $bar->start();

        foreach ($relations as $relation) {
            try {
                // Get claim translations to find corresponding posts
                $claimTranslations = DB::connection($connection)
                    ->table('claim_translations')
                    ->where('claim_id', $relation->claim_id)
                    ->whereNull('deleted_at')
                    ->get();

                // Get publisher translations
                $publisherTranslations = DB::connection($connection)
                    ->table('publisher_translations')
                    ->where('publisher_id', $relation->publisher_id)
                    ->whereNull('deleted_at')
                    ->get();

                foreach ($claimTranslations as $claimTrans) {
                    foreach ($publisherTranslations as $pubTrans) {
                        if ($claimTrans->locale === $pubTrans->locale) {
                            $postId = $this->mappings['claims'][$relation->claim_id . '_' . $claimTrans->locale] ?? null;
                            $participantId = $this->mappings['publishers'][$relation->publisher_id . '_' . $pubTrans->locale] ?? null;

                            if ($postId && $participantId) {
                                // Check if relation already exists
                                $exists = PostRelation::where('post_id', $postId)
                                    ->where('relationable_type', Participant::class)
                                    ->where('relationable_id', $participantId)
                                    ->exists();

                                if (!$exists) {
                                    PostRelation::create([
                                        'post_id' => $postId,
                                        'relationable_type' => Participant::class,
                                        'relationable_id' => $participantId,
                                        'relationable_is_main' => 0,
                                        'team_id' => 1,
                                        'lang' => $claimTrans->locale,
                                        'created_at' => $relation->created_at,
                                        'updated_at' => $relation->updated_at,
                                    ]);
                                    $this->stats['post_relations']['inserted']++;
                                } else {
                                    $this->stats['post_relations']['skipped']++;
                                }
                            }
                        }
                    }
                }

                $bar->advance();
            } catch (\Exception $e) {
                $this->stats['post_relations']['errors']++;
                Log::error("Failed to migrate claim-publisher relation: " . $e->getMessage());
                $bar->advance();
            }
        }

        $bar->finish();
        $this->newLine();
    }

    /**
     * Migrate claim_resource to post_relations
     */
    protected function migrateClaimResources($connection)
    {
        $this->line('  → Migrating Claim-Resource relations...');

        $relations = DB::connection($connection)
            ->table('claim_resource')
            ->whereNull('deleted_at')
            ->get();

        $bar = $this->output->createProgressBar($relations->count());
        $bar->start();

        foreach ($relations as $relation) {
            try {
                // Get claim translations to find corresponding posts
                $claimTranslations = DB::connection($connection)
                    ->table('claim_translations')
                    ->where('claim_id', $relation->claim_id)
                    ->whereNull('deleted_at')
                    ->get();

                // Get resource translations
                $resourceTranslations = DB::connection($connection)
                    ->table('resource_translations')
                    ->where('resource_id', $relation->resource_id)
                    ->whereNull('deleted_at')
                    ->get();

                foreach ($claimTranslations as $claimTrans) {
                    foreach ($resourceTranslations as $resTrans) {
                        if ($claimTrans->locale === $resTrans->locale) {
                            $postId = $this->mappings['claims'][$relation->claim_id . '_' . $claimTrans->locale] ?? null;
                            $participantId = $this->mappings['resources'][$relation->resource_id . '_' . $resTrans->locale] ?? null;

                            if ($postId && $participantId) {
                                // Check if relation already exists
                                $exists = PostRelation::where('post_id', $postId)
                                    ->where('relationable_type', Participant::class)
                                    ->where('relationable_id', $participantId)
                                    ->exists();

                                if (!$exists) {
                                    PostRelation::create([
                                        'post_id' => $postId,
                                        'relationable_type' => Participant::class,
                                        'relationable_id' => $participantId,
                                        'relationable_is_main' => 0,
                                        'team_id' => 1,
                                        'lang' => $claimTrans->locale,
                                        'created_at' => $relation->created_at,
                                        'updated_at' => $relation->updated_at,
                                    ]);
                                    $this->stats['post_relations']['inserted']++;
                                } else {
                                    $this->stats['post_relations']['skipped']++;
                                }
                            }
                        }
                    }
                }

                $bar->advance();
            } catch (\Exception $e) {
                $this->stats['post_relations']['errors']++;
                Log::error("Failed to migrate claim-resource relation: " . $e->getMessage());
                $bar->advance();
            }
        }

        $bar->finish();
        $this->newLine();
    }

    /**
     * Migrate Claim and News Views to Post Views
     */
    protected function migrateViews()
    {
        $this->info('👁️  Migrating Views...');
        $connection = $this->option('connection');

        // Migrate Claim Views
        $this->line('  → Migrating Claim Views...');
        $claims = DB::connection($connection)
            ->table('claims')
            ->whereNull('deleted_at')
            ->whereNotNull('views')
            ->where('views', '>', 0)
            ->get();

        $bar = $this->output->createProgressBar($claims->count());
        $bar->start();

        foreach ($claims as $claim) {
            try {
                // Get all translations for this claim to find corresponding posts
                $translations = DB::connection($connection)
                    ->table('claim_translations')
                    ->where('claim_id', $claim->id)
                    ->whereNull('deleted_at')
                    ->get();

                foreach ($translations as $translation) {
                    $postId = $this->mappings['claims'][$claim->id . '_' . $translation->locale] ?? null;

                    if ($postId) {
                        // Check if view record already exists for this post
                        $existingView = DB::table('views')
                            ->where('viewable_type', Post::class)
                            ->where('viewable_id', $postId)
                            ->first();

                        if ($existingView) {
                            $this->stats['views']['skipped']++;
                        } else {
                            // Create view record in db1
                            DB::table('views')->insert([
                                'viewable_type' => Post::class,
                                'viewable_id' => $postId,
                                'views_number' => $claim->views ?? 1,
                                'last_viewed_at' => now(), // Set to current time
                                'created_at' => $claim->created_at,
                                'updated_at' => now(),
                            ]);
                            $this->stats['views']['inserted']++;
                        }
                    }
                }

                $bar->advance();
            } catch (\Exception $e) {
                $this->stats['views']['errors']++;
                Log::error("Failed to migrate views for claim {$claim->id}: " . $e->getMessage());
                $bar->advance();
            }
        }

        $bar->finish();
        $this->newLine();

        // Migrate News Views
        $this->line('  → Migrating News Views...');
        $newsList = DB::connection($connection)
            ->table('news')
            ->whereNull('deleted_at')
            ->whereNotNull('views')
            ->where('views', '>', 0)
            ->get();

        $bar = $this->output->createProgressBar($newsList->count());
        $bar->start();

        foreach ($newsList as $news) {
            try {
                // Get all translations for this news to find corresponding posts
                $translations = DB::connection($connection)
                    ->table('news_translations')
                    ->where('news_id', $news->id)
                    ->whereNull('deleted_at')
                    ->get();

                foreach ($translations as $translation) {
                    $postId = $this->mappings['news'][$news->id . '_' . $translation->locale] ?? null;

                    if ($postId) {
                        // Check if view record already exists for this post
                        $existingView = DB::table('views')
                            ->where('viewable_type', Post::class)
                            ->where('viewable_id', $postId)
                            ->first();

                        if ($existingView) {
                            $this->stats['views']['skipped']++;
                        } else {
                            // Create view record in db1
                            DB::table('views')->insert([
                                'viewable_type' => Post::class,
                                'viewable_id' => $postId,
                                'views_number' => $news->views ?? 1,
                                'last_viewed_at' => now(), // Set to current time
                                'created_at' => $news->created_at,
                                'updated_at' => now(),
                            ]);
                            $this->stats['views']['inserted']++;
                        }
                    }
                }

                $bar->advance();
            } catch (\Exception $e) {
                $this->stats['views']['errors']++;
                Log::error("Failed to migrate views for news {$news->id}: " . $e->getMessage());
                $bar->advance();
            }
        }

        $bar->finish();
        $this->newLine();
        $this->info("✓ Views migration completed");
    }

    /**
     * Migrate Tags from db2 to db1
     */
    protected function migrateTags()
    {
        $this->info('🏷️  Migrating Tags...');
        $connection = $this->option('connection');

        $tags = DB::connection($connection)
            ->table('tags')
            ->whereNull('deleted_at')
            ->get();

        $bar = $this->output->createProgressBar($tags->count());
        $bar->start();

        foreach ($tags as $oldTag) {
            try {
                // Check if tag already exists (by name)
                $existingTag = Tag::where('tag_name', $oldTag->name)->first();

                if ($existingTag) {
                    $this->stats['tags']['skipped']++;
                    $this->mappings['tags'][$oldTag->id] = $existingTag->id;
                } else {
                    // Create new tag in db1 (all in Arabic as specified)
                    $newTag = Tag::create([
                        'lang' => 'ar', // All tags are Arabic
                        'tag_name' => $oldTag->name,
                        'tag_type' => TagTypeEnum::NEWS,
                        'team_id' => 1, // Default team
                        'created_at' => $oldTag->created_at,
                        'updated_at' => $oldTag->updated_at,
                    ]);

                    $this->mappings['tags'][$oldTag->id] = $newTag->id;
                    $this->stats['tags']['inserted']++;
                }

                $bar->advance();
            } catch (\Exception $e) {
                $this->stats['tags']['errors']++;
                Log::error("Failed to migrate tag {$oldTag->id}: " . $e->getMessage());
                $bar->advance();
            }
        }

        $bar->finish();
        $this->newLine();
        $this->info("✓ Tags migration completed");
    }

    /**
     * Migrate Uploads to Files
     */
    protected function migrateUploads()
    {
        $this->info('📁 Migrating Uploads to Files...');
        $connection = $this->option('connection');

        $uploads = DB::connection($connection)
            ->table('uploads')
            ->whereNull('deleted_at')
            ->get();

        $bar = $this->output->createProgressBar($uploads->count());
        $bar->start();

        foreach ($uploads as $oldUpload) {
            try {
                // Check if file already exists (by path)
                $existingFile = Files::where('path', $oldUpload->full_large_path)->first();

                if ($existingFile) {
                    $this->stats['uploads']['skipped']++;
                    $this->mappings['uploads'][$oldUpload->id] = $existingFile->id;
                } else {
                    // Extract extension from filename
                    $extension = pathinfo($oldUpload->filename, PATHINFO_EXTENSION);

                    // Create new file in db1 using full_large_path as specified
                    $newFile = Files::create([
                        'path' => $oldUpload->full_large_path, // Use full_large_path as path
                        'original_name' => $oldUpload->name,
                        'file_name' => $oldUpload->filename,
                        'size' => $oldUpload->size,
                        'duration' => null,
                        'extension' => $extension,
                        'team_id' => 1, // Default team
                        'created_at' => $oldUpload->created_at,
                        'updated_at' => $oldUpload->updated_at,
                    ]);

                    $this->mappings['uploads'][$oldUpload->id] = $newFile->id;
                    $this->stats['uploads']['inserted']++;
                }

                $bar->advance();
            } catch (\Exception $e) {
                $this->stats['uploads']['errors']++;
                Log::error("Failed to migrate upload {$oldUpload->id}: " . $e->getMessage());
                $bar->advance();
            }
        }

        $bar->finish();
        $this->newLine();
        $this->info("✓ Uploads migration completed");
    }

    /**
     * Migrate Claim Types to Types table
     */
    protected function migrateClaimTypes()
    {
        $this->info('📋 Migrating Claim Types to Types...');
        $connection = $this->option('connection');

        $claimTypes = DB::connection($connection)
            ->table('claim_types')
            ->whereNull('deleted_at')
            ->get();

        $bar = $this->output->createProgressBar($claimTypes->count());
        $bar->start();

        foreach ($claimTypes as $claimType) {
            try {
                // Get translations for this claim type
                $translations = DB::connection($connection)
                    ->table('claim_type_translations')
                    ->where('claim_type_id', $claimType->id)
                    ->whereNull('deleted_at')
                    ->get();

                if ($translations->isEmpty()) {
                    $this->stats['types']['skipped']++;
                    $bar->advance();
                    continue;
                }

                // Create one type per locale (like categories)
                foreach ($translations as $translation) {
                    // Check if type already exists
                    $existingType = Type::where('type_name', $translation->name)->first();

                    if ($existingType) {
                        $this->stats['types']['skipped']++;
                        $this->mappings['types'][$claimType->id . '_' . $translation->locale] = $existingType->id;
                    } else {
                        // Create new type in db1
                        $newType = Type::create([
                            'type_name' => $translation->name,
                            'team_id' => 1, // Default team
                            'show_index' => $claimType->status == 1 ? true : false,
                            'order' => null,
                            'created_at' => $claimType->created_at,
                            'updated_at' => $claimType->updated_at,
                        ]);

                        $this->mappings['types'][$claimType->id . '_' . $translation->locale] = $newType->id;
                        $this->stats['types']['inserted']++;
                    }
                }

                $bar->advance();
            } catch (\Exception $e) {
                $this->stats['types']['errors']++;
                Log::error("Failed to migrate claim type {$claimType->id}: " . $e->getMessage());
                $bar->advance();
            }
        }

        $bar->finish();
        $this->newLine();
        $this->info("✓ Claim Types migration completed");
    }

    /**
     * Migrate News to Posts
     */
    protected function migrateNews()
    {
        $this->info('📰 Migrating News to Posts...');
        $connection = $this->option('connection');

        $newsList = DB::connection($connection)
            ->table('news')
            ->whereNull('deleted_at')
            ->get();

        $bar = $this->output->createProgressBar($newsList->count());
        $bar->start();

        foreach ($newsList as $news) {
            try {
                // Get news translations
                $translations = DB::connection($connection)
                    ->table('news_translations')
                    ->where('news_id', $news->id)
                    ->whereNull('deleted_at')
                    ->get();

                if ($translations->isEmpty()) {
                    $this->stats['news']['skipped']++;
                    $bar->advance();
                    continue;
                }

                // Get category mapping for this news locale
                $newCategoryId = $this->mappings['categories'][$news->category_id . '_' . $translations->first()->locale] ?? null;

                foreach ($translations as $translation) {
                    // Check if post already exists
                    $existingPost = Post::where('title', $translation->title)
                        ->where('lang', $translation->locale)
                        ->first();

                    if ($existingPost) {
                        $this->stats['news']['skipped']++;
                        $this->mappings['news'][$news->id . '_' . $translation->locale] = $existingPost->id;
                        continue;
                    }

                    // Get upload/file mapping if exists
                    $uploadId = null;
                    if ($news->upload_id && isset($this->mappings['uploads'][$news->upload_id])) {
                        $uploadId = $this->mappings['uploads'][$news->upload_id];
                    }

                    // Build news body
                    $body = '';
                    if ($translation->text) {
                        $body .= $translation->text;
                    }
                    if ($translation->info) {
                        $body .= "\n\n" . $translation->info;
                    }

                    // Create post
                    $post = Post::create([
                        'lang' => $translation->locale,
                        'publish_date' => $news->published_at ?? $news->created_at,
                        'order_number' => null,
                        'title' => $translation->title ?? 'Untitled',
                        'slug' => Str::replace(' ', '-', $translation->title),
                        'image' => null, // Will be linked via model_has_files if upload exists
                        'video_url' => null,
                        'pdf_url' => null,
                        'image_alt' => null,
                        'news_type' => $news->type_id ?? null,
                        'image_size' => null,
                        'description' => $translation->summary ?? $translation->badge ?? null,
                        'body' => $body,
                        'updates' => 0,
                        'publish_status' => $this->mapPublishStatus($news->status),
                        'user_id' => 1, // Default user
                        'publisher_id' => null,
                        'team_id' => 1, // Default team
                        'old_id' => null, // This is news, not a claim
                        'created_at' => $news->created_at,
                        'updated_at' => $news->updated_at,
                    ]);

                    $this->mappings['news'][$news->id . '_' . $translation->locale] = $post->id;
                    $this->stats['news']['inserted']++;

                    // Create category relation if category exists for this locale
                    if ($newCategoryId) {
                        PostRelation::create([
                            'post_id' => $post->id,
                            'relationable_type' => Category::class,
                            'relationable_id' => $newCategoryId,
                            'lang' => $translation->locale,
                            'team_id' => 1,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }

                    // Link upload/file if exists
                    if ($uploadId) {
                        DB::table('model_has_files')->insert([
                            'file_id' => $uploadId,
                            'model_type' => Post::class,
                            'model_id' => $post->id,
                            'model_column' => 'image',
                            'team_id' => 1,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }

                $bar->advance();
            } catch (\Exception $e) {
                $this->stats['news']['errors']++;
                Log::error("Failed to migrate news {$news->id}: " . $e->getMessage());
                $bar->advance();
            }
        }

        $bar->finish();
        $this->newLine();
        $this->info("✓ News migration completed");
    }

    /**
     * Migrate Tag Relations (news_tag to post_relations)
     */
    protected function migrateTagRelations()
    {
        $this->info('🔗 Migrating Tag Relations...');
        $connection = $this->option('connection');

        $tagRelations = DB::connection($connection)
            ->table('news_tag')
            ->whereNull('deleted_at')
            ->get();

        $bar = $this->output->createProgressBar($tagRelations->count());
        $bar->start();

        foreach ($tagRelations as $relation) {
            try {
                // Determine if this is a news or claim relation
                $isNews = $relation->type === 'news';
                $sourceId = $relation->type_id;

                // Get the appropriate post mapping
                $postMapping = $isNews ? $this->mappings['news'] : $this->mappings['claims'];

                // Find all locale versions of this post
                foreach ($postMapping as $key => $postId) {
                    // Check if this is the right source ID
                    if (str_starts_with($key, $sourceId . '_')) {
                        $locale = explode('_', $key)[1] ?? 'ar';

                        // Get new tag ID
                        $newTagId = $this->mappings['tags'][$relation->tag_id] ?? null;

                        if ($newTagId && $postId) {
                            // Check if relation already exists
                            $existingRelation = PostRelation::where('post_id', $postId)
                                ->where('relationable_type', Tag::class)
                                ->where('relationable_id', $newTagId)
                                ->first();

                            if (!$existingRelation) {
                                PostRelation::create([
                                    'post_id' => $postId,
                                    'relationable_type' => Tag::class,
                                    'relationable_id' => $newTagId,
                                    'lang' => $locale,
                                    'team_id' => 1,
                                    'created_at' => $relation->created_at ?? now(),
                                    'updated_at' => $relation->updated_at ?? now(),
                                ]);
                            }
                        }
                    }
                }

                $bar->advance();
            } catch (\Exception $e) {
                Log::error("Failed to migrate tag relation: " . $e->getMessage());
                $bar->advance();
            }
        }

        $bar->finish();
        $this->newLine();
        $this->info("✓ Tag relations migration completed");
    }

    /**
     * Migrate Type Relations (claim types to posts)
     */
    protected function migrateTypeRelations()
    {
        $this->info('🔗 Migrating Type Relations (Claim Types to Posts)...');
        $connection = $this->option('connection');

        // Get all claims with claim_type_id
        $claims = DB::connection($connection)
            ->table('claims')
            ->whereNull('deleted_at')
            ->whereNotNull('claim_type_id')
            ->get();

        $bar = $this->output->createProgressBar($claims->count());
        $bar->start();

        foreach ($claims as $claim) {
            try {
                // Get claim translations to find all post versions
                $translations = DB::connection($connection)
                    ->table('claim_translations')
                    ->where('claim_id', $claim->id)
                    ->whereNull('deleted_at')
                    ->get();

                foreach ($translations as $translation) {
                    $postId = $this->mappings['claims'][$claim->id . '_' . $translation->locale] ?? null;
                    $typeId = $this->mappings['types'][$claim->claim_type_id . '_' . $translation->locale] ?? null;

                    if ($postId && $typeId) {
                        // Check if relation already exists
                        $existingRelation = PostRelation::where('post_id', $postId)
                            ->where('relationable_type', Type::class)
                            ->where('relationable_id', $typeId)
                            ->first();

                        if (!$existingRelation) {
                            PostRelation::create([
                                'post_id' => $postId,
                                'relationable_type' => Type::class,
                                'relationable_id' => $typeId,
                                'lang' => $translation->locale,
                                'team_id' => 1,
                                'created_at' => $claim->created_at,
                                'updated_at' => $claim->updated_at,
                            ]);
                        }
                    }
                }

                $bar->advance();
            } catch (\Exception $e) {
                Log::error("Failed to migrate type relation for claim {$claim->id}: " . $e->getMessage());
                $bar->advance();
            }
        }

        $bar->finish();
        $this->newLine();
        $this->info("✓ Type relations migration completed");
    }

    /**
     * Display migration statistics
     */
    protected function displayStatistics()
    {
        $this->info('========================================');
        $this->info('Migration Statistics');
        $this->info('========================================');
        $this->newLine();

        $this->table(
            ['Type', 'Inserted', 'Updated', 'Skipped', 'Errors'],
            [
                ['Categories (all locales)', $this->stats['categories']['inserted'], $this->stats['categories']['updated'], $this->stats['categories']['skipped'], $this->stats['categories']['errors']],
                ['Tags (all Arabic)', $this->stats['tags']['inserted'], $this->stats['tags']['updated'], $this->stats['tags']['skipped'], $this->stats['tags']['errors']],
                ['Types (Claim Types)', $this->stats['types']['inserted'], $this->stats['types']['updated'], $this->stats['types']['skipped'], $this->stats['types']['errors']],
                ['Uploads → Files', $this->stats['uploads']['inserted'], $this->stats['uploads']['updated'], $this->stats['uploads']['skipped'], $this->stats['uploads']['errors']],
                ['Authors (all locales)', $this->stats['authors']['inserted'], $this->stats['authors']['updated'], $this->stats['authors']['skipped'], $this->stats['authors']['errors']],
                ['Publishers (all locales)', $this->stats['publishers']['inserted'], $this->stats['publishers']['updated'], $this->stats['publishers']['skipped'], $this->stats['publishers']['errors']],
                ['Resources (all locales)', $this->stats['resources']['inserted'], $this->stats['resources']['updated'], $this->stats['resources']['skipped'], $this->stats['resources']['errors']],
                ['Claims → Posts', $this->stats['claims']['inserted'], $this->stats['claims']['updated'], $this->stats['claims']['skipped'], $this->stats['claims']['errors']],
                ['News → Posts', $this->stats['news']['inserted'], $this->stats['news']['updated'], $this->stats['news']['skipped'], $this->stats['news']['errors']],
                ['Post Relations', $this->stats['post_relations']['inserted'], $this->stats['post_relations']['updated'], $this->stats['post_relations']['skipped'], $this->stats['post_relations']['errors']],
                ['Views', $this->stats['views']['inserted'], $this->stats['views']['updated'], $this->stats['views']['skipped'], $this->stats['views']['errors']],
            ]
        );

        $this->newLine();
    }
}
