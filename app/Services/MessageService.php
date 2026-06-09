<?php

namespace App\Services;

use App\Enums\MessageTypes;
use App\Models\Message;
use Throwable;

class MessageService
{
    /**
     * @throws Throwable
     */
    public function send(array $fields): Message
    {
        try {
            $message = Message::query()->create([
                'user_id' => auth('moonshine')->user()->id,
                'message' => $fields['message'],
                'type' => MessageTypes::USER->value
            ]);
        } catch (\Exception $e) {
            dd($e);
        }

        return $message;
    }
}
