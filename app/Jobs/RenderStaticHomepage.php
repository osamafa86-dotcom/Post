<?php

namespace App\Jobs;

use App\Services\StaticPageRenderer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class RenderStaticHomepage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Debounce: if multiple posts are saved in quick succession,
     * only the last job actually renders.
     */
    public int $uniqueFor = 10;

    /**
     * Number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * Seconds to wait before retrying.
     */
    public int $backoff = 5;

    /**
     * The reason the render was triggered (for logging).
     */
    protected string $reason;

    public function __construct(string $reason = 'post_change')
    {
        $this->reason = $reason;
        $this->onQueue('static-render');
    }

    /**
     * Unique ID for debounce — all homepage renders collapse into one.
     */
    public function uniqueId(): string
    {
        return 'render_static_homepage';
    }

    public function handle(StaticPageRenderer $renderer): void
    {
        Log::info("RenderStaticHomepage: starting render (reason: {$this->reason})");

        $renderer->renderHomepage();

        Log::info('RenderStaticHomepage: completed');
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("RenderStaticHomepage: job failed — {$exception->getMessage()}");
    }
}
