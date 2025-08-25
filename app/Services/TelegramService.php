<?php

// Telegram Bot Integration

namespace App\Services;

use App\Events\TestNotification;
use App\Models\Admin;
use App\Models\Driver;
use App\Models\Rider;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Laravel\Facades\Telegram;

class TelegramService
{
    public function handle($update)
    {
        Log::info('Telegram update received:', $update->all());
        event(new TestNotification([
            'قائد الطوفان' => 'القائد يحيى السنوار'
        ]));

        if ($update->getMessage()) {
            $from = $update->getMessage()->getFrom();
            if ($from) {
                $lang = $from->getLanguageCode();
                App::setLocale($lang);
            }
        }

        if ($update->getMessage()) {
            $message = $update->getMessage();
            $chatId = $message->getChat()->getId();
            $text = $message->getText();

            if ($text === '/start') {
                $this->askForRole($chatId);
                return;
            }

            if ($message->getContact()) {
                $this->handleContact($message);
                return;
            }
        }

        if (isset($update['callback_query'])) {
            $this->handleCallbackQuery($update['callback_query']);
            return;
        }
    }

    private function askForRole($chatId)
    {
        $keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => trans('telegram.driver'), 'callback_data' => 'role_driver'],
                    ['text' => trans('telegram.rider'), 'callback_data' => 'role_rider'],
                ]
            ]
        ];

        Telegram::sendMessage([
            'chat_id' => $chatId,
            'text' => trans('telegram.welcome'),
            'reply_markup' => json_encode($keyboard)
        ]);
    }



    private function handleCallbackQuery($callbackQuery)
    {
        $chatId = $callbackQuery['message']['chat']['id'];
        $data = $callbackQuery['data'];

        // Extract role from callback_data
        $role = substr($data, 5);

        // Store the role in cache or session, associated with the user's chat_id
        cache(['telegram_role_' . $chatId => $role], now()->addMinutes(10));

        $this->requestPhoneNumber($chatId);
    }

    private function requestPhoneNumber($chatId)
    {
        $keyboard = [
            'keyboard' => [
                [
                    ['text' => trans('telegram.share_phone'), 'request_contact' => true]
                ]
            ],
            'resize_keyboard' => true,
            'one_time_keyboard' => true
        ];

        Telegram::sendMessage([
            'chat_id' => $chatId,
            'text' => trans('telegram.request_phone'),
            'reply_markup' => json_encode($keyboard)
        ]);
    }

    private function handleContact($message)
    {
        $chatId = $message->getChat()->getId();
        $phoneNumber = $message->getContact()->getPhoneNumber();
        $phoneNumber =  str_replace("+", "", $phoneNumber);
        event(new TestNotification([
            ' $phoneNumber' =>  $phoneNumber
        ]));
        // Retrieve the role from cache
        $role = cache('telegram_role_' . $chatId);

        if (!$role) {
            Telegram::sendMessage([
                'chat_id' => $chatId,
                'text' => trans('telegram.session_expired')
            ]);
            return;
        }

        $user = $this->findUserByPhoneAndRole($phoneNumber, $role);

        if ($user) {
            // Your existing OTP generation logic goes here
            $otp = $this->getOtpForUser($user);
            if ($otp == '' || $otp == null) {
                Telegram::sendMessage([
                    'chat_id' => $chatId,
                    'text' => trans('telegram.no_otp')
                ]);
            } else {
                Telegram::sendMessage([
                    'chat_id' => $chatId,
                    'text' => trans('telegram.otp_message')
                ]);
                Telegram::sendMessage([
                    'chat_id' => $chatId,
                    'text' => $otp
                ]);
            }
        } else {
            Telegram::sendMessage([
                'chat_id' => $chatId,
                'text' => trans('telegram.no_otp')
            ]);
        }

        // Clear the cached role
        cache()->forget('telegram_role_' . $chatId);
    }

    private function findUserByPhoneAndRole($phoneNumber, $role)
    {
        $model = null;
        switch ($role) {
            case 'driver':
                $model = new Driver();
                break;
            case 'rider':
                $model = new Rider();
                break;
        }

        if ($model) {
            // Assuming the phone number is stored in a 'phone' column
            return $model->where('phone', $phoneNumber)->first();
        }

        return null;
    }

    private function getOtpForUser($user): ?string
    {
        if ($user->getExpirationOfCode() < now()) {
            return null;
        }
        return $user->getTwoFactorCode();
    }
}
