<?php

namespace App\Console\Commands;

use App\Models\Setting;
use Illuminate\Console\Command;

class IngestionSwitchCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ingestion:control 
                            {action? : Action to execute: pause, resume, toggle, status}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Control the Master RSS Ingestion Switch (pause, resume, toggle, status)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $action = strtolower($this->argument('action') ?? 'status');
        $current = Setting::get('ingestion_enabled', true);

        switch ($action) {
            case 'pause':
                Setting::set('ingestion_enabled', false, 'boolean', 'ingestion');
                $this->warn("⏸️ RSS Ingestion is now PAUSED. The scheduler will not fetch any feeds.");
                break;

            case 'resume':
            case 'start':
                Setting::set('ingestion_enabled', true, 'boolean', 'ingestion');
                $this->info("🟢 RSS Ingestion is now ACTIVE. The scheduler will resume fetching active feeds.");
                break;

            case 'toggle':
                $new = !$current;
                Setting::set('ingestion_enabled', $new, 'boolean', 'ingestion');
                if ($new) {
                    $this->info("🟢 RSS Ingestion toggled to: ACTIVE.");
                } else {
                    $this->warn("⏸️ RSS Ingestion toggled to: PAUSED.");
                }
                break;

            case 'status':
            default:
                if ($current) {
                    $this->info("🟢 RSS Ingestion Status: ACTIVE (Feeds are being polled according to schedule).");
                } else {
                    $this->warn("⏸️ RSS Ingestion Status: PAUSED (Scheduler is skipping all feeds).");
                }
                break;
        }

        return 0;
    }
}