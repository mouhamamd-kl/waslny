<?php

// Telegram Bot Integration

namespace App\Console\Commands;

use App\Events\TestNotification;
use Illuminate\Console\Command;
use Telegram\Bot\Laravel\Facades\Telegram;

class TestNotificationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test-notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set the webhook for the Telegram bot';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        event(new TestNotification([
            'قائد الطوفان' => 'القائد يحيى السنوار'
        ]));
    }
}
