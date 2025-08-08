<?php

namespace App\Console\Commands;

use App\Events\TestNotification;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class QueueWorkWithLock extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'queue:work-with-lock';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run the queue worker with a lock to prevent overlapping';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $lockFile = '/tmp/queue.lock';

        if (file_exists($lockFile)) {
            Log::info('Queue worker is already running.');
            event(new TestNotification('Queue worker is already running.'));
            return 0;
        }

        Log::info('Starting queue worker.');
        event(new TestNotification('Starting queue worker.'));
        touch($lockFile);

        try {
            Log::info('Running queue:work');
            event(new TestNotification('Running queue:work'));
            Artisan::call('queue:work', ['--stop-when-empty' => true]);
            
            Log::info('Finished queue:work');
            event(new TestNotification('Finished queue:work'));
        } catch (Exception $e) {
            Log::info($e);
        } finally {
            Log::info('Removing lock file.');
            event(new TestNotification('Removing lock file.'));
            unlink($lockFile);
        }

        Log::info('Queue worker finished.');
        event(new TestNotification('Queue worker finished.'));
        return 0;
    }
}
