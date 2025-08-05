<?php

// Telegram Bot Integration

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Telegram\Bot\Laravel\Facades\Telegram;

class TelegramController extends Controller
{
    protected $telegramService;

    public function __construct(TelegramService $telegramService)
    {
        $this->telegramService = $telegramService;
    }

    public function webhook()
    {
        $update = Telegram::getWebhookUpdate();
        $this->telegramService->handle($update);

        return 'ok';
    }
}
