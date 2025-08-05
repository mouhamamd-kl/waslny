<?php

// Telegram Bot Integration

namespace App\Services;

use App\Models\Admin;
use App\Models\Driver;
use App\Models\Rider;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Laravel\Facades\Telegram;

class TelegramService
{
    public function handle($update)
    {
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
                    ['text' => 'I am a Driver', 'callback_data' => 'role_driver'],
                    ['text' => 'I am a Rider', 'callback_data' => 'role_rider'],
                ]
            ]
        ];

        Telegram::sendMessage([
            'chat_id' => $chatId,
            'text' => 'Welcome! Please select your role:',
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
                    ['text' => 'Share My Phone Number', 'request_contact' => true]
                ]
            ],
            'resize_keyboard' => true,
            'one_time_keyboard' => true
        ];

        Telegram::sendMessage([
            'chat_id' => $chatId,
            'text' => 'Please share your phone number to verify your account.',
            'reply_markup' => json_encode($keyboard)
        ]);
    }

    private function handleContact($message)
    {
        $chatId = $message->getChat()->getId();
        $phoneNumber = $message->getContact()->getPhoneNumber();

        // Retrieve the role from cache
        $role = cache('telegram_role_' . $chatId);

        if (!$role) {
            Telegram::sendMessage([
                'chat_id' => $chatId,
                'text' => 'Session expired. Please start over by sending /start.'
            ]);
            return;
        }

        $user = $this->findUserByPhoneAndRole($phoneNumber, $role);

        if ($user) {
            // Your existing OTP generation logic goes here
            $otp = $this->getOtpForUser($user);

            Telegram::sendMessage([
                'chat_id' => $chatId,
                'text' => "Your OTP is: {$otp}"
            ]);
        } else {
            Telegram::sendMessage([
                'chat_id' => $chatId,
                'text' => 'Sorry, we could not find an account matching your phone number and role.'
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
        return $user->getTwoFactorCode();
    }
}
