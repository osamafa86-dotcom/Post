<?php

namespace App\Console\Commands;

use App\Services\StaticPageRenderer;
use Illuminate\Console\Command;

class RenderStaticPages extends Command
{
    protected $signature = 'static:render {--purge : Purge all cached pages before rendering}';

    protected $description = 'Render the homepage as static HTML (synchronous, for initial warm-up or debugging)';

    public function handle(StaticPageRenderer $renderer): int
    {
        if ($this->option('purge')) {
            $renderer->purge();
            $this->info('Purged all cached static pages.');
        }

        $this->info('Rendering homepage variants...');

        $renderer->renderHomepage();

        $this->info('Done. Files written to: storage/framework/cache/pages/');

        return self::SUCCESS;
    }
}
