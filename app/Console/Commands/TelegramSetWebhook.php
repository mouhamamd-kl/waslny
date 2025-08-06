<?php

// Telegram Bot Integration

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Telegram\Bot\Laravel\Facades\Telegram;

class TelegramSetWebhook extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:set-webhook';

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
        $url = config('app.url') . '/api/api/telegram/webhook';
        $response = Telegram::setWebhook(['url' => $url]);
        $this->info('Webhook set to: ' . $url);
        $this->info('Response: ' . $response);
    }
}
