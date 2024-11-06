<?php

declare(strict_types=1);

namespace RichanFongdasen\Turso\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;

class TursoSyncJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected string $connectionName;

    public function __construct(string $connectionName = 'turso')
    {
        $this->connectionName = $connectionName;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Artisan::call('turso:sync', ['connectionName' => $this->connectionName]);
    }
}
