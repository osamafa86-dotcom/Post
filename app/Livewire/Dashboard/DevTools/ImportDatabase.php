<?php

namespace App\Livewire\Dashboard\DevTools;

use App\Enums\CategoryTypeEnum;
use App\Enums\ImageSizeTypeEnum;
use App\Enums\NewsTypeEnum;
use App\Enums\PublishEnum;
use App\Enums\TagTypeEnum;
use App\Models\Advertisement;
use App\Models\BreakingNews;
use App\Models\Category;
use App\Models\Course;
use App\Models\Event;
use App\Models\Files;
use App\Models\Icon;
use App\Models\Material;
use App\Models\MaterialAlbum;
use App\Models\MaterialRelation;
use App\Models\ModelHasFile;
use App\Models\NavbarLink;
use App\Models\Participant;
use App\Models\Post;
use App\Models\PostRelation;
use App\Models\Quote;
use App\Models\Reel;
use App\Models\Service;
use App\Models\Setting;
use App\Models\SocialMedia;
use App\Models\SortData;
use App\Models\SpecialFile;
use App\Models\SpecialPage;
use App\Models\Tag;
use App\Models\Team;
use App\Models\User;
use App\Models\UserLog;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Livewire\Attributes\Computed;
use Livewire\Component;

class ImportDatabase extends Component
{

    public array $languages = [];
    public string $language = 'ar';

    public string $host = '127.0.0.1';
    public string $db = 'maktoob';
    public string $username = 'root';
    public string $password = '';
    public string $result = '';
    public string $source = 'maktoob';
    public string $fromPart = '';
    public string $toPart = '';

    public string $changeCatFrom = '';
    public string $changeCatTo = '';

    public string $oldPath = '';
    public string $newPath = '';

    public bool $cleanBeforeImport = false;
    public string $connectionStatus = '';

    // Progress tracking
    public array $importProgress = [
        'currentStep' => 0,
        'totalSteps' => 5,
        'steps' => [
            ['name' => 'Importing Authors', 'completed' => false, 'total' => 0, 'processed' => 0],
            ['name' => 'Importing Categories', 'completed' => false, 'total' => 0, 'processed' => 0],
            ['name' => 'Importing Tags', 'completed' => false, 'total' => 0, 'processed' => 0],
            ['name' => 'Importing Users', 'completed' => false, 'total' => 0, 'processed' => 0],
            ['name' => 'Importing Posts', 'completed' => false, 'total' => 0, 'processed' => 0],
        ],
        'isImporting' => false,
    ];

    protected $listeners = ['updateProgress' => 'updateProgress', 'run-import-step' => 'runNextImportStep'];

    /**
     * set system default language for old/null records
     * @return void
     * @throws \Exception
     */
    public function setSystemRecordsDefaultLanguage()
    {
        try{
            Category::withoutGlobalScopes()->whereNull('lang')->update(['lang' => $this->language]);
            Tag::withoutGlobalScopes()->whereNull('lang')->update(['lang' => $this->language]);
            Participant::withoutGlobalScopes()->whereNull('lang')->update(['lang' => $this->language]);
            Post::withoutGlobalScopes()->whereNull('lang')->update(['lang' => $this->language]);
            BreakingNews::withoutGlobalScopes()->whereNull('lang')->update(['lang' => $this->language]);
            Icon::withoutGlobalScopes()->whereNull('lang')->update(['lang' => $this->language]);
            SpecialPage::withoutGlobalScopes()->whereNull('lang')->update(['lang' => $this->language]);
            SpecialFile::withoutGlobalScopes()->whereNull('lang')->update(['lang' => $this->language]);
            Quote::withoutGlobalScopes()->whereNull('lang')->update(['lang' => $this->language]);
            Advertisement::withoutGlobalScopes()->whereNull('lang')->update(['lang' => $this->language]);
            Event::withoutGlobalScopes()->whereNull('lang')->update(['lang' => $this->language]);
            UserLog::withoutGlobalScopes()->whereNull('lang')->update(['lang' => $this->language]);
            Setting::withoutGlobalScopes()->whereNull('lang')->update(['lang' => $this->language]);
            NavbarLink::withoutGlobalScopes()->whereNull('lang')->update(['lang' => $this->language]);
            Material::withoutGlobalScopes()->whereNull('lang')->update(['lang' => $this->language]);
            SocialMedia::withoutGlobalScopes()->whereNull('lang')->update(['lang' => $this->language]);
            Reel::withoutGlobalScopes()->whereNull('lang')->update(['lang' => $this->language]);
            Course::withoutGlobalScopes()->whereNull('lang')->update(['lang' => $this->language]);
            Service::withoutGlobalScopes()->whereNull('lang')->update(['lang' => $this->language]);
            PostRelation::withoutGlobalScopes()->whereNull('lang')->update(['lang' => $this->language]);
        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
        }
    }
    public function updateProgress($stepIndex, $processed, $total = null)
    {
        if ($total !== null) {
            $this->importProgress['steps'][$stepIndex]['total'] = $total;
        }
        $this->importProgress['steps'][$stepIndex]['processed'] = $processed;
        $this->importProgress['currentStep'] = $stepIndex;

        // Mark as completed if processed all items
        if ($total && $processed >= $total) {
            $this->importProgress['steps'][$stepIndex]['completed'] = true;
        }

        $this->dispatch('importProgressUpdated');
    }

    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.dashboard.dev-tools.import-database');
    }

    public function mount($add = null): void
    {
        $this->languages = config('app.languages');
        // Default target language from site's configured default frontend language
        $this->language = config('features.default_frontend_language', config('app.website_locale', 'ar'));
    }

    /**
     * Force the target language as the current locale so HasLanguage trait
     * auto-sets the correct lang on all records created during import.
     */
    protected function forceImportLocale(): void
    {
        App::setLocale($this->language);
        Config::set('app.locale', $this->language);
    }

    #[Computed]
    public function teams(): Collection
    {
        return Team::all();
    }

    #[Computed]
    public function categories(): Collection
    {
        return Category::withoutGlobalScopes()->select('id', 'category_title')->get();
    }

    public function testConnection()
    {
        try {
            Config::set('database.connections.remote.host', $this->host);
            Config::set('database.connections.remote.database', $this->db);
            Config::set('database.connections.remote.username', $this->username);
            Config::set('database.connections.remote.password', $this->password);
            DB::purge('remote');
            DB::connection('remote')->getPdo();
            $this->connectionStatus = 'success';
        } catch (\Exception $e) {
            $this->connectionStatus = 'error';
            $this->result = __('messages.import.connection_failed') . ': ' . $e->getMessage();
        }
    }

    public function cleanTables()
    {
        if (!Auth::user()->is_admin()) return;

        Schema::disableForeignKeyConstraints();

        $tables = [
            'post_relations',
            'model_has_files',
            'files',
            'sort_data',
            'material_relations',
            'materials',
            'posts',
            'tags',
            'categories',
            'participants',
            'advertisements',
            'breaking_news',
            'user_logs',
            'quotes',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                DB::table($table)->truncate();
            }
        }

        // Delete non-admin users but preserve admin (id=1) and session
        DB::table('users')->where('id', '!=', 1)->delete();

        Schema::enableForeignKeyConstraints();
    }

    public function confirmImport()
    {
        if (!Auth::user()->is_admin()) return;

        $this->validate([
            'host' => 'required|string',
            'db' => 'required|string',
            'username' => 'required|string',
            'source' => 'required|in:laravel,cilliumfm,almadina,qudsnen,hodhod,mohager,maktoob',
        ]);

        $message = $this->cleanBeforeImport
            ? __('messages.import.confirm_clean_message')
            : __('messages.import.confirm_import_message');

        $this->dispatch('show-import-confirmation', [
            'message' => $message,
            'isClean' => $this->cleanBeforeImport,
        ]);
    }

    public function executeImport()
    {
        if (!Auth::user()->is_admin()) return;

        if ($this->cleanBeforeImport) {
            $this->cleanTables();
        }

        $this->forceImportLocale();
        $this->import();
    }

    /**
     * Fix double-encoded text (mojibake) in imported content.
     * This happens when WordPress data is stored as latin1 but contains UTF-8 bytes,
     * causing characters like ' to appear as â€™, " as â€œ, etc.
     *
     * Strategy: use MySQL SQL for speed (CONVERT(BINARY CONVERT USING latin1) USING utf8mb4),
     * and fall back to PHP mb_convert_encoding per-row if SQL fails on mixed-language content.
     */
    public function fixMojibake()
    {
        if (!Auth::user()->is_admin()) return;

        $fixMap = [
            'posts' => ['title', 'slug', 'description', 'body', 'image_alt'],
            'categories' => ['category_title', 'category_description'],
            'tags' => ['tag_name'],
            'participants' => ['name', 'work'],
            'breaking_news' => ['title'],
            'quotes' => ['quote', 'author'],
            'advertisements' => ['title'],
        ];

        $totalUpdated = 0;
        $errors = [];

        foreach ($fixMap as $table => $columns) {
            if (!Schema::hasTable($table)) continue;

            foreach ($columns as $col) {
                if (!Schema::hasColumn($table, $col)) continue;

                try {
                    // SQL path: fast, atomic per-column. Only touches rows with telltale sequence.
                    $updated = DB::update(
                        "UPDATE `{$table}`
                         SET `{$col}` = CONVERT(BINARY CONVERT(`{$col}` USING latin1) USING utf8mb4)
                         WHERE `{$col}` IS NOT NULL AND `{$col}` LIKE ?",
                        ['%â€%']
                    );
                    $totalUpdated += $updated;
                } catch (\Exception $e) {
                    // Fall back to PHP row-by-row for this column (mixed-language safe)
                    try {
                        $rows = DB::table($table)
                            ->select('id', $col)
                            ->whereNotNull($col)
                            ->where($col, 'LIKE', '%â€%')
                            ->get();

                        foreach ($rows as $row) {
                            $fixed = @mb_convert_encoding($row->$col, 'Windows-1252', 'UTF-8');
                            if ($fixed !== false && mb_check_encoding($fixed, 'UTF-8')) {
                                DB::table($table)->where('id', $row->id)->update([$col => $fixed]);
                                $totalUpdated++;
                            }
                        }
                    } catch (\Exception $inner) {
                        $errors[] = "{$table}.{$col}: " . $inner->getMessage();
                    }
                }
            }
        }

        $msg = __('messages.import.mojibake_fixed') . ' (' . $totalUpdated . ')';
        if (!empty($errors)) {
            $msg .= ' | ' . implode(', ', array_slice($errors, 0, 3));
        }
        $this->result = $msg;
    }

    /**
     * Update lang column on imported content tables to the target language.
     * Useful when data was already imported with the wrong locale.
     */
    public function fixImportedDataLanguage()
    {
        if (!Auth::user()->is_admin()) return;

        try {
            $targetTables = [
                'posts',
                'post_relations',
                'categories',
                'tags',
                'participants',
                'materials',
                'material_relations',
                'advertisements',
                'breaking_news',
                'quotes',
                'user_logs',
            ];

            $updated = 0;
            foreach ($targetTables as $table) {
                if (Schema::hasTable($table) && Schema::hasColumn($table, 'lang')) {
                    $updated += DB::table($table)
                        ->where('lang', '!=', $this->language)
                        ->update(['lang' => $this->language]);
                }
            }

            $this->result = __('messages.import.language_fixed') . ' (' . $updated . ')';
        } catch (\Exception $e) {
            $this->result = __('messages.import.import_error') . ': ' . $e->getMessage();
        }
    }

    public function import()
    {
        if (!Auth::user()->is_admin()) return;

        // Reset and start the import process
        $this->importProgress['isImporting'] = true;
        $this->importProgress['currentStep'] = 0;
        $this->importProgress['steps'] = array_map(function($step) {
            $step['completed'] = false;
            $step['processed'] = 0;
            $step['total'] = 0;
            return $step;
        }, $this->importProgress['steps']);

        // Dispatch an event to the browser to start the first step
        $this->dispatch('run-import-step');
    }

    public function runNextImportStep()
    {
        if (!$this->importProgress['isImporting']) {
            return;
        }

        // Force target language on every step so HasLanguage trait picks it up
        $this->forceImportLocale();

        // Set the remote database connection config for every step
        Config::set('database.connections.remote.host', $this->host);
        Config::set('database.connections.remote.database', $this->db);
        Config::set('database.connections.remote.username', $this->username);
        Config::set('database.connections.remote.password', $this->password);

        $currentStepIndex = $this->importProgress['currentStep'];
        $steps = [
            'importAuthorsFromWPUsers',
            'CategoryFromWP',
            'TagsFromWP',
            'importUsersFromWP',
            'importPostsFromWP'
        ];

        if (isset($steps[$currentStepIndex])) {
            $prefix = 'wp'; // Default prefix
            $domain = 'mohajer.net'; // Default domain

            // Adjust prefix and domain based on the source
            if ($this->source == 'cilliumfm') {
                $domain = 'cilliumfm';
            } elseif ($this->source == 'qudsnen') {
                $prefix = '0rnvns53';
                $domain = 'qudsnen';
            } elseif ($this->source == 'hodhod') {
                $domain = 'hodhodpal';
            } elseif ($this->source == 'maktoob') {
                $prefix = 'wpyl';
                $domain = 'maktoobmedia';
            }

            try {
                $this->updateProgress($currentStepIndex, 0, 1); // Mark as started

                // Call the import method for the current step
                if ($steps[$currentStepIndex] === 'importPostsFromWP') {
                    $this->{$steps[$currentStepIndex]}($prefix, $domain);
                } else {
                    $this->{$steps[$currentStepIndex]}($prefix);
                }

                $this->updateProgress($currentStepIndex, 1, 1); // Mark as completed

                // Move to the next step
                $this->importProgress['currentStep']++;

                if ($this->importProgress['currentStep'] < count($steps)) {
                    // If there are more steps, dispatch event to run the next one
                    $this->dispatch('run-import-step');
                } else {
                    // All steps are done
                    $this->importProgress['isImporting'] = false;
                    $this->result = __('messages.import.import_completed');
                }
            } catch (\Exception $e) {
                $this->importProgress['isImporting'] = false;
                $this->result = __('messages.import.import_error') . ': ' . $e->getMessage() . ' (line ' . $e->getLine() . ')';
            }
        } else {
            // This case means all steps are finished.
            $this->importProgress['isImporting'] = false;
            if ($this->source == 'hodhod') {
                $this->updateBodyImageLink("https://hodhodpal.ps/post","https://hodhodpal.ps/share");
                $this->updateBodyImageLink("https://hodhodpal.ps/?p=","https://hodhodpal.ps/share/");
                $this->updateBodyImageLink("https://hodhodpal.ps/112037","https://hodhodpal.ps/share/112037");
            }
        }
    }

    /**
     * @description Import posts from almadina
     * @param void
     * @return void
     */
    public function importAlmadinaPosts()
    {
        $published = \App\Enums\PublishEnum::PUBLISHED->value;
        $draft = \App\Enums\PublishEnum::DRAFT->value;

        $select = DB::connection('remote')->select("
            select id,
                   id ,
                   title as 'title',
                   body,
                   title as 'slug',
                   CASE active
                        WHEN 1 THEN $published
                        ELSE $draft
                   END as 'publish_status',
                   description as description,
                   created_at as publish_date,
                   author_id as author_id,
                   category_id as category_id,
                   created_at
            from posts
        ");
        foreach ($select as $record) {
            $cat_id = $record->category_id;
            $author_id = $record->author_id ?? null;

            $post = Post::withoutGlobalScopes()->updateOrCreate([
                'id' => $record->id,
            ], [
                'id' => $record->id,
                'publish_date' => $record->created_at,
                'title' => $record->title,
                'slug' => $record->title,
                'description' => $record->description,
                'body' => $record->body,
                'publish_status' => $record->publish_status,//$record->publish_status,
                'image_size' => $author_id !== null ? ImageSizeTypeEnum::COVER_ARTICLE->value : ($record->style == 1 ? ImageSizeTypeEnum::LARGE_IMAGE->value : ImageSizeTypeEnum::MID_IMAGE->value),
            ]);

            PostRelation::withoutGlobalScopes()->create([
                'post_id' => $post->id,
                'relationable_id' => $cat_id,
                'relationable_type' => Category::class,
                'relationable_is_main' => 1,
            ]);

            if ($author_id !== null) {
                PostRelation::withoutGlobalScopes()->create([
                    'post_id' => $post->id,
                    'relationable_type' => Participant::class,
                    'relationable_id' => $author_id,
                ]);
            }

            $imageQuery = DB::connection('remote')->select("SELECT file_name from media where  model_type like ('%Post%') and model_id=" . $record->id);
            if (count($imageQuery) > 0) {
                foreach ($imageQuery as $image) {
                    $pieces = explode("/", $image->file_name);
                    $file_name = $pieces[count($pieces) - 1];
                    $pieces = explode(".", $image->file_name);
                    $extention = $pieces[count($pieces) - 1];

                    $local_file = Files::create([
                        'original_name' => $file_name,
                        'file_name' => $file_name,
                        'path' => 'uploads/' . $image->file_name,
                        'extension' => $extention,
                    ]);

                    ModelHasFile::create([
                        'file_id' => $local_file->id,
                        'model_type' => Post::class,
                        'model_id' => $post->id,
                        'model_column' => 'image',
                    ]);
                }
            }
        }

    }

    public function importAlmadinaUsers()
    {
        $select = DB::connection('remote')->select(
            "SELECT id , name as first_name,email,password,created_at,updated_at"
            . " FROM `users`");

        foreach ($select as $record) {
            User::updateOrCreate([
                'id' => $record->id
            ], [
                'id' => $record->id,
                'first_name' => $record->first_name,
                'last_name' => '-',
                'email' => $record->email,
                'password' => $record->password,
                'created_at' => $record->created_at,
                'updated_at' => $record->updated_at,
            ]);
        }
    }

    public function importUsersFromWP($prefix)
    {
        $adminEmail = User::find(1)?->email;

        $select = DB::connection('remote')->select(
            "SELECT id , user_nicename as first_name,user_email as email,user_pass as password,user_registered as created_at"
            . " FROM `" . $prefix . "_users`");

        foreach ($select as $record) {
            // Skip if this would overwrite the admin user
            if ($record->id == 1 || $record->email === $adminEmail) continue;

            User::updateOrCreate([
                'email' => $record->email,
            ], [
                'id' => $record->id,
                'first_name' => $record->first_name,
                'last_name' => '-',
                'email' => $record->email,
                'password' => $record->password,
                'created_at' => $record->created_at,
            ]);
        }
    }

    public function importAlmadinaCategories()
    {
        $columns = Schema::getColumnListing('categories');
        if (!in_array('old_parent_id', $columns)) {
            Schema::table('categories', function (Blueprint $table) {
                $table->integer('old_parent_id')->nullable();
            });
        }

        $select = DB::connection('remote')->select(
            "SELECT id , parent as old_parent_id, name as category_title,
            '' as category_description,'أخبار' as category_type,"
            . "created_at,updated_at"
            . " FROM `categories`");

        foreach ($select as $record) {
            Category::withoutGlobalScopes()->updateOrCreate([
                'id' => $record->id,
            ], [
                'id' => $record->id,
                'old_parent_id' => $record->old_parent_id,
                'category_title' => $record->category_title,
                'category_description' => $record->category_description,
                'category_type' => $record->category_type,
                'created_at' => $record->created_at,
                'updated_at' => $record->updated_at,
            ]);
        }

        foreach ($select as $record) {
            Category::withoutGlobalScopes()->updateOrCreate([
                'id' => $record->id,
            ], [
                'id' => $record->id,
                'parent_id' => $record->old_parent_id,
            ]);
        }
    }

    public function importAlmadinaAuthors()
    {
        $select = DB::connection('remote')->select(
            "SELECT id , name,created_at,updated_at, "
            . "'الكتاب' as type FROM `authors`");

        foreach ($select as $record) {
            $Participant = Participant::withoutGlobalScopes()->updateOrCreate([
                'id' => $record->id,
            ], [
                'id' => $record->id,
                'name' => $record->name,
                'type' => $record->type,
                'created_at' => $record->created_at,
                'updated_at' => $record->updated_at,
            ]);

            $imageQuery = DB::connection('remote')->select("SELECT file_name from media where  model_type like ('%Author%') and model_id=" . $record->id);
            if (count($imageQuery) > 0) {
                foreach ($imageQuery as $image) {
                    $pieces = explode("/", $image->file_name);
                    $file_name = $pieces[count($pieces) - 1];
                    $pieces = explode(".", $image->file_name);
                    $extention = $pieces[count($pieces) - 1];

                    $local_file = Files::create([
                        'original_name' => $file_name,
                        'file_name' => $file_name,
                        'path' => 'uploads/' . $image->file_name,
                        'extension' => $extention,
                    ]);

                    ModelHasFile::create([
                        'file_id' => $local_file->id,
                        'model_type' => Participant::class,
                        'model_id' => $Participant->id,
                        'model_column' => 'image',
                    ]);
                }
            }

        }
    }

    public function importAuthorsFromWPUsers($prefix)
    {
        $select = DB::connection('remote')->select(
            "SELECT id , user_nicename as name,user_registered as created_at, "
            . "'الكتاب' as type FROM `" . $prefix . "_users`");

        foreach ($select as $record) {
            Participant::withoutGlobalScopes()->updateOrCreate([
                'id' => $record->id,
            ], [
                'id' => $record->id,
                'name' => $record->name,
                'type' => $record->type,
                'created_at' => $record->created_at,
            ]);
        }
    }

    public function importAds()
    {
        $select = DB::connection('remote')->select(
            "SELECT id ,sort,title,img,sm_img,type,code,link,target,views,"
            . "expired_at,widget_id,status,admin_id,created_at,updated_at,deleted_at from ads");

        foreach ($select as $record) {
            Advertisement::updateOrCreate([
                'id' => $record->id,
            ], [
                'id' => $record->id,
                'title' => $record->title,
                'image' => $record->img,
                'type' => $record->type,
                'url' => $record->link,
                'url_target' => $record->target,
                'code' => $record->code,
                'user_id' => 1,
                'publish_status' => $record->status == 1 ? PublishEnum::PUBLISHED->value : PublishEnum::DRAFT->value,
                'created_at' => $record->created_at,
                'updated_at' => $record->updated_at,
                'deleted_at' => $record->deleted_at,
            ]);
        }

    }

    public function breakNewsAds()
    {
        $select = DB::connection('remote')->select(
            "SELECT id ,title,expired_at,link,status"
            . ",created_at,updated_at,deleted_at from breakings");

        foreach ($select as $record) {
            BreakingNews::updateOrCreate([
                'id' => $record->id,
            ], [
                'id' => $record->id,
                'title' => $record->title,
                'hide_date' => $record->expired_at,
                'url' => $record->link,
                'publish_status' => $record->status == 1 ? PublishEnum::PUBLISHED->value : PublishEnum::DRAFT->value,
                'created_at' => $record->created_at,
                'updated_at' => $record->updated_at,
                'deleted_at' => $record->deleted_at,
            ]);
        }
    }

    public function importAuthors()
    {

        $select = DB::connection('remote')->select(
            "SELECT id , name,img as image,work,created_at,updated_at,"
            . "'الكتاب' as type FROM `authors`");

        foreach ($select as $record) {
            Participant::withoutGlobalScopes()->updateOrCreate([
                'id' => $record->id,
            ], [
                'id' => $record->id,
                'name' => $record->name,
                'image' => $record->image,
                'work' => $record->work,
                'type' => $record->type,
                'created_at' => $record->created_at,
                'updated_at' => $record->updated_at,
            ]);
        }
    }

    public function importTags()
    {

        $select = DB::connection('remote')->select(
            "SELECT id , title as tag_name,created_at,updated_at,deleted_at FROM `tags`");

        foreach ($select as $record) {
            Tag::withoutGlobalScopes()->updateOrCreate([
                'id' => $record->id
            ], [
                'id' => $record->id,
                'tag_name' => $record->tag_name,
                'tag_type' => TagTypeEnum::NEWS->value,
                'created_at' => $record->created_at,
                'updated_at' => $record->updated_at,
                'deleted_at' => $record->deleted_at,
            ]);
        }
    }

    public function importUsers()
    {
        $select = DB::connection('remote')->select(
            "SELECT id , name as first_name,email,password,created_at,updated_at,deleted_at"
            . " FROM `admins`");

        foreach ($select as $record) {
            User::updateOrCreate([
                'id' => $record->id
            ], [
                'id' => $record->id,
                'first_name' => $record->first_name,
                'last_name' => '-',
                'email' => $record->email,
                'password' => $record->password,
                'created_at' => $record->created_at,
                'updated_at' => $record->updated_at,
                'deleted_at' => $record->deleted_at,
            ]);
        }
    }

    public function importCategories()
    {
        $columns = Schema::getColumnListing('categories');
        if (!in_array('old_parent_id', $columns)) {
            Schema::table('categories', function (Blueprint $table) {
                $table->integer('old_parent_id');
            });
        }

        $select = DB::connection('remote')->select(
            "SELECT id , parent_id as old_parent_id, title as category_title,
            description as category_description,'أخبار' as category_type,"
            . "created_at,updated_at"
            . " FROM `categories`");

        foreach ($select as $record) {
            Category::withoutGlobalScopes()->updateOrCreate([
                'id' => $record->id,
            ], [
                'id' => $record->id,
                'old_parent_id' => $record->old_parent_id,
                'category_title' => $record->category_title,
                'category_description' => $record->category_description,
                'category_type' => $record->category_type,
                'created_at' => $record->created_at,
                'updated_at' => $record->updated_at,
            ]);
        }

        foreach ($select as $record) {
            Category::withoutGlobalScopes()->updateOrCreate([
                'id' => $record->id,
            ], [
                'id' => $record->id,
                'parent_id' => $record->old_parent_id,
            ]);
        }
    }

    public function importPosts()
    {
        $maxID = DB::connection('remote')->select("SELECT max(id) as maxid FROM `posts`")[0]->maxid;
        for ($i = 1; $i <= $maxID; $i = $i + 500) {

            $from = $i;
            $to = $i + 500;
            $published = \App\Enums\PublishEnum::PUBLISHED->value;
            $draft = \App\Enums\PublishEnum::DRAFT->value;

            $select = DB::connection('remote')->select("
                select id,
                       id ,
                       style,
                       sort,
                       title as 'title',
                       subnews,
                       title as 'slug',
                       CASE status
                            WHEN 1 THEN $published
                            ELSE $draft
                       END as 'publish_status',
                       img as image,
                       img_caption as image_alt,
                       description as description,
                       published_at as publish_date,
                       author_id as author_id,
                       category_id as category_id,
                       views as views,
                       admin_id as user_id,
                       created_at as created_at,
                       updated_at as updated_at,
                       deleted_at as deleted_at
                from posts
                WHERE id >= ? AND id <= ?
            ", [$from, $to]);
            foreach ($select as $record) {
                $image = $record->image;
                $cat_id = $record->category_id;
                $user_id = $record->user_id;
                $author_id = $record->author_id == null ? null : $record->author_id;
                $body = DB::connection('remote')->select("select body from post_details where post_id =" . $record->id)[0]->body;

                if ($image) {

                    $pieces = explode("/", $image);
                    $file_name = $pieces[count($pieces) - 1];
                    $pieces = explode(".", $image);
                    $extention = $pieces[count($pieces) - 1];

                    $local_file = Files::create([
                        'original_name' => $file_name,
                        'file_name' => $file_name,
                        'path' => $image,
                        'extension' => $extention,
                    ]);

                    $post = Post::withoutGlobalScopes()->updateOrCreate([
                        'id' => $record->id,
                    ], [
                        'id' => $record->id,
                        'publish_date' => $record->created_at,
                        'title' => $record->title,
                        'slug' => $record->title,
                        'image' => $image ?? '',
                        'image_alt' => $record->image_alt,
                        'description' => $record->description,
                        'body' => $body,
                        'publish_status' => $record->publish_status,//$record->publish_status,
                        'publisher_id' => $user_id,
                        'user_id' => $user_id,
                        'image_size' => $author_id !== null ? ImageSizeTypeEnum::COVER_ARTICLE->value : ($record->style == 1 ? ImageSizeTypeEnum::LARGE_IMAGE->value : ImageSizeTypeEnum::MID_IMAGE->value),
                        'news_type' => $record->subnews == 1 ? NewsTypeEnum::SUB_NEWS->value : NewsTypeEnum::MAIN_NEWS->value,
                    ]);

                    $post->sortable()->create([
                        'order_number' => $record->sort,
                    ]);

                    PostRelation::withoutGlobalScopes()->create([
                        'post_id' => $post->id,
                        'relationable_id' => $cat_id,
                        'relationable_type' => Category::class,
                        'relationable_is_main' => 1,
                    ]);

                    if ($author_id !== null) {
                        PostRelation::withoutGlobalScopes()->create([
                            'post_id' => $post->id,
                            'relationable_type' => Participant::class,
                            'relationable_id' => $author_id,
                        ]);
                    }

                    $tags = DB::connection('remote')->table("post_tag")->where("post_id", "=", $record->id)->pluck("tag_id");
                    $newTags = Tag::whereIn('id', $tags)->get()->pluck('id');

                    foreach ($newTags as $key => $id) {
                        PostRelation::withoutGlobalScopes()->create([
                            'post_id' => $post->id,
                            'relationable_id' => $id,
                            'relationable_type' => Tag::class,
                            'relationable_is_main' => $key == 0 ? 1 : 0,
                        ]);
                    }

                    ModelHasFile::create([
                        'file_id' => $local_file->id,
                        'model_type' => Post::class,
                        'model_id' => $post->id,
                        'model_column' => 'image',
                    ]);
                }
            }
        }
    }

    public function CategoryFromWP($prefix)
    {
        $select = DB::connection('remote')
            ->select("SELECT term_id as id,name FROM `" . $prefix . "_terms` where term_id in (select term_id from `".$prefix."_term_taxonomy` where taxonomy = 'category') ;");
        foreach ($select as $record) {
            Category::withoutGlobalScopes()->updateOrCreate([
                'id' => $record->id,
            ], [
                'id' => $record->id,
                'category_title' => $record->name,
                'category_type' => CategoryTypeEnum::NEWS->value,
            ]);
        }
    }

    public function TagsFromWP($prefix)
    {
        $select = DB::connection('remote')
            ->select("SELECT term_id as id,name FROM `" . $prefix . "_terms` where term_id in (select term_id from `".$prefix."_term_taxonomy` where taxonomy = 'post_tag');");
        foreach ($select as $record) {
            Tag::withoutGlobalScopes()->updateOrCreate([
                'id' => $record->id,
            ], [
                'id' => $record->id,
                'tag_name' => $record->name,
                'tag_type' => TagTypeEnum::NEWS->value,
            ]);
        }
    }

    public function importPostsFromWP($prefix, $domain)
    {

        $this->result = trans('messages.import.found') . ' ' . DB::connection('remote')->select("SELECT count('id') as countId FROM `" . $prefix . "_posts` WHERE guid like('%" . $domain . "%') and post_type = 'post'")[0]->countId;
        $maxID = DB::connection('remote')->select("SELECT max(id) as maxid FROM `" . $prefix . "_posts` WHERE post_type = 'post'")[0]->maxid;
//        $maxID = 5000;

        for ($i = 1; $i <= $maxID; $i = $i + 5000) {
            $from = $i;
            $to = $i + 5000;
            $select = DB::connection('remote')->select("SELECT id , post_name as slug, 1 as user_id,1 as publisher_id,post_modified as updated_at,post_date as publish_date, post_title as title,post_content as body,CASE post_status WHEN 'publish' THEN ".PublishEnum::PUBLISHED->value." ELSE ".PublishEnum::DRAFT->value."  END as 'publish_status'"
                . " FROM `" . $prefix . "_posts` WHERE guid like('%" . $domain . "%') and post_type = 'post' and id >= " . $from . " and id <= " . $to);

//            dd($select);

            foreach ($select as $record) {
                if (Post::withoutGlobalScopes()->where('id', $record->id)->count() > 0) continue;

                $image = DB::connection('remote')->select("SELECT meta_value as image_path FROM `" . $prefix . "_postmeta` where meta_key = '_wp_attached_file' and post_id in (SELECT meta_value FROM `" . $prefix . "_postmeta` where post_id = " . $record->id . " and meta_key ='_thumbnail_id')");
//                $views = DB::connection('remote')->select("SELECT meta_value as views FROM `" . $prefix . "_postmeta` where meta_key = 'post_views_count' and post_id = " . $record->id);

                if ($this->source == 'mohager') {
                    $body = $this->proccessMohagerBody($record,$prefix);
                }else{
                    $body = $record->body;
                }


                $post = Post::withoutGlobalScopes()->updateOrCreate([
                    'id' => $record->id,
                ], [
                    'id' => $record->id,
                    'publish_date' => $record->publish_date,
                    'created_at' => $record->publish_date,
                    'updated_at' => $record->updated_at,
                    'title' => $record->title,
                    'slug' => $record->slug,
                    'image_alt' => ' ',
                    'description' => $record->title,
                    'body' => $body,
//                    'views' => $views[0]?->views ?? 0,
                    'publish_status' => $record->publish_status,
                    'publisher_id' => 1,
                    'user_id' => 1,
                ]);


                $cats = DB::connection('remote')->select("select term_id from " . $prefix . "_terms where term_id in(
                        select term_id from " . $prefix . "_term_taxonomy where taxonomy='category' and term_taxonomy_id in (
                            select term_taxonomy_id from " . $prefix . "_term_relationships where object_id = ".$record->id."
                        ))");

                if (count($cats) > 0) {
                    foreach ($cats as $key => $cat) {

                        PostRelation::withoutGlobalScopes()->updateOrCreate([
                            'post_id' => $post->id,
                            'relationable_id' => $cat->term_id,
                            'relationable_type' => Category::class,
                            'relationable_is_main' => $key == 0,
                        ]);
                    }
                }

                $tags = DB::connection('remote')->select("select term_id from " . $prefix . "_terms where term_id in(
                        select term_id from " . $prefix . "_term_taxonomy where taxonomy='post_tag' and term_taxonomy_id in (
                            select term_taxonomy_id from " . $prefix . "_term_relationships where object_id = ".$record->id."
                        ))");
                if (count($tags) > 0) {
                    foreach ($tags as $key => $tag) {
                        PostRelation::withoutGlobalScopes()->updateOrCreate([
                            'post_id' => $post->id,
                            'relationable_id' => $tag->term_id,
                            'relationable_type' => Tag::class,
                            'relationable_is_main' => $key == 0,
                        ]);
                    }
                }

                if (count($image) > 0) {
                    $pieces = explode("/", $image[0]->image_path);
                    $file_name = $pieces[count($pieces) - 1];
                    $pieces = explode(".", $image[0]->image_path);
                    $extention = $pieces[count($pieces) - 1];

                    $local_file = Files::create([
                        'original_name' => $file_name,
                        'file_name' => $file_name,
                        'path' => 'uploads/' . $image[0]->image_path,
                        'extension' => $extention,
                    ]);

                    ModelHasFile::create([
                        'file_id' => $local_file->id,
                        'model_type' => Post::class,
                        'model_id' => $post->id,
                        'model_column' => 'image',
                    ]);
                }
            }
            //            DB::table('posts')->insert($to_fill);
        }
    }

    public function hodhodCategoryUpdate(){
        $this->movePostsToMaterials("#شفتو_شو_صار","هدهد بلس");
        $this->movePostsToMaterials("بودكاست العهده","ميديا");
        $this->movePostsToMaterials("الهدهد في الجيش","هدهد بلس");
        $this->movePostsToMaterials("مع #عبد_العزيز","هدهد بلس");
        $this->movePostsToMaterials("حرب العام 1973","هدهد بلس");
        $this->movePostsToMaterials("اعرف عدوك","هدهد بلس");
        $this->movePostsToMaterials("اطلالة الهدهد","هدهد بلس");
        $this->movePostsToMaterials("مع #سعيد","هدهد بلس");
        $this->movePostsToMaterials("عين على العدو","هدهد بلس");

        $this->movePostsToMaterials("مرئيات","ميديا");
        $this->movePostsToMaterials("كليبات","ميديا");

        $this->changeCategory("مقالات","رأي الهدهد");
        $this->changeCategory("ألبوم الصور","الكاريكاتير");
        $this->changeCategory("شخصيات وأحزاب","أصداء الشارع الإسرائيلي");
        $this->changeCategory("هدهد تك","تطبيقات الهدهد");
        $this->changeCategory("هدهد بوك","تطبيقات الهدهد");
        $this->changeCategory("الأمن التقني","الأخبار");
        $this->changeCategory("اختراقات تقنية","الأخبار");
        $this->changeCategory("برمجيات تقنية","الأخبار");
        $this->changeCategory('البنية التنظيمية لجيش الاحتلال "الإسرائيلي"',"أصداء الشارع الإسرائيلي");
        $this->changeCategory("فلسطينيّو 48","شؤون فلسطينية");
        $this->changeCategory("ترجمات العهده المرئية","تقارير مترجمة");

        $this->changeCategory("#خبر_الهدهد","أرشيف");
        $this->changeCategory("دورة لغة عبرية","أرشيف");
        $this->changeCategory("مقالات استراتيجية","أرشيف");
        $this->changeCategory("الموقف الإيراني","أرشيف");
        $this->changeCategory("أخرى","أرشيف");
        $this->changeCategory("صفقة وفاء الأحرار","أرشيف");

    }

    public function movePostsToMaterials($oldCat, $newCat){
        $cat = Category::where("category_title",$oldCat)->first();
        $toCat = Category::firstOrCreate(['category_title' => $newCat]);

        if($cat){
            $arr = PostRelation::where('relationable_id',$cat->id)->where('relationable_type', Category::class)->pluck('post_id');
            $posts = Post::whereIn('id',$arr)->get();
            foreach ($posts as $post) {
                $material = Material::create([
                    'title' => $post->title,
                    'description' => $post->description,
                    'type' => 2,
                    'video_type' => 3,
                    'publish_status' => \App\Enums\PublishEnum::PUBLISHED->value,
                    'like' => rand(0, 1000),
                ]);
                $lastOrderNumber = SortData::select('order_number')->orderBy('order_number', 'desc')->first()?->order_number;
                $material->sortable()->create([
                    'order_number' => $lastOrderNumber ? $lastOrderNumber + 1 : 1,
                ]);
                MaterialRelation::create([
                    'material_id' => $material->id,
                    'relationable_id' => $toCat->id,
                    'relationable_type' => Category::class,
                    'relationable_is_main' => 1,
                ]);

                $files = ModelHasFile::where('model_type',Post::class)->where('model_id',$post->id)->get();
                foreach ($files as $file){
                    $file->update([
                        'model_type'=>Material::class,
                        'model_id'=>$material->id
                    ]);
                }
            }
            foreach ($posts as $post){
                $post->delete();
            }
        }
    }

    public function update_body_part(){
        $this->updateBodyImageLink($this->fromPart,$this->toPart);
    }

    public function mohajerCategoryUpdate(){
        $this->updateBodyImageLink("<img class",'<img style="display: block;" class');
        $this->updateBodyImageLink("https://www.mohajer.net/wp-content/uploads/","https://fsn1.your-objectstorage.com/devlo-cms/almohajer/uploads/");
        $this->changeCategory("تقارير","قصصنا");
        $this->changeCategory("مجتمع","قصصنا");
        $this->changeCategory("حقوق","قصصنا");
        $this->changeCategory("منظمات","أخبار");
        $this->changeCategory("أنشطة","أخبار");
    }

    public function changeCategory($from, $to){
        $cat = Category::withoutGlobalScopes()->where("category_title", $from)->first();
        $toCat = Category::withoutGlobalScopes()->firstOrCreate(['category_title' => $to]);
        if($cat) {
            return PostRelation::where('relationable_type', Category::class)
                ->where('relationable_id', $cat->id)
                ->update(['relationable_id' => $toCat->id]);
        }
        return false;
    }

    public function changeCategorySubmit(){
        $cat = Category::withoutGlobalScopes()->where("category_title", $this->changeCatFrom)->first();
        $toCat = Category::withoutGlobalScopes()->firstOrCreate(['category_title' => $this->changeCatTo]);
        if($cat) {
            return PostRelation::where('relationable_type', Category::class)
                ->where('relationable_id', $cat->id)
                ->update(['relationable_id' => $toCat->id]);
        }
        return true;
    }

    public function updateBodyImageLink($from, $to){
        DB::update("UPDATE posts SET body = REPLACE(body, ?, ?)", [$from, $to]);
    }

    private function proccessMohagerBody($record, $prefix)
    {
        if ($this->source == 'mohager') {
            $body = str_replace('https://www.mohajer.net/?s=', 'https://www.mohajer.net/search_page?search=', $record->body);
            $body = str_replace('https://www.mohajer.net/?p=', 'https://www.mohajer.net/share/', $body);
//            $body = str_replace($this->oldImagePath, $this->newImagePath, $body);
            $body = preg_replace('/(https?:\/\/[^\/]+\/[^&\s?]*)([&?])slug=[^&\s]+/i', '$1$2', $body);
        }

        //  updateSlugToShareIdLinks
        if (!preg_match('/<a\s+href="https:\/\/www\.mohajer\.net\//', $body)) {
            return $body;
        }

        $arr = preg_split('/<a\s+href="https:\/\/www\.mohajer\.net\//',$body);
        $newBody = $arr[0];
        foreach ($arr as $key => $part){


            if($key==0) continue;
            if (str_starts_with($part, 'search')) {
                $newBody.= '<a href="https://www.mohajer.net/'.$part;
                continue;
            }

            if (str_starts_with($part, 'share')) {
                $newBody.= '<a href="https://www.mohajer.net/'.$part;
                continue;
            }

            $slug = preg_split('/">/',$part)[0];
            if (substr($slug, -1) === '/') $slug = substr($slug, 0, -1);

            $idR = DB::connection('remote')->select("SELECT id FROM `" . $prefix . "_posts` where post_name = '".$slug."'");//?->id;
            if(!$idR){ Log::error("Import: missing record for body: " . substr($body, 0, 100)); continue; }
            $id = $idR[0]->id;

            $newBody.='<a href="https://www.mohajer.net/share/'.$id.'/">';//.$part.$item;

            $position = strpos($part, '>');
            if ($position !== false) {
                $newBody.=substr($part, $position + 1); // +1 to exclude the '>' itself
            }
        }

        return $newBody;
    }

    /**
     * Update file paths in the files table
     * Replace old path prefix with new path prefix
     * 
     * @return void
     */
    public function updateFilePaths()
    {
        try {
            $this->validate([
                'oldPath' => 'nullable|string',
                'newPath' => 'nullable|string',
            ]);

            // Trim slashes for consistency
            $oldPath = trim($this->oldPath, '/');
            $newPath = trim($this->newPath, '/');

            // Get all files
            $files = Files::all();
            $updatedCount = 0;

            foreach ($files as $file) {
                $currentPath = $file->path;
                $newFilePath = $currentPath;

                // If old path is provided, replace it
                if (!empty($oldPath)) {
                    // Check if current path starts with old path
                    if (str_starts_with($currentPath, $oldPath . '/') || str_starts_with($currentPath, $oldPath)) {
                        // Remove old path prefix
                        $newFilePath = preg_replace('/^' . preg_quote($oldPath, '/') . '\/?/', '', $currentPath);
                    }
                }

                // Add new path prefix if provided
                if (!empty($newPath)) {
                    $newFilePath = $newPath . '/' . ltrim($newFilePath, '/');
                }

                // Update if path changed
                if ($newFilePath !== $currentPath) {
                    $file->update(['path' => $newFilePath]);
                    $updatedCount++;
                }
            }

            $this->result = "تم تحديث {$updatedCount} ملف بنجاح!";
            
            // Reset fields
            $this->oldPath = '';
            $this->newPath = '';

        } catch (\Exception $e) {
            $this->result = "خطأ: " . $e->getMessage();
        }
    }
}
