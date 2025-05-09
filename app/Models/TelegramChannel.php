<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TelegramChannel extends Model
{
    public function bot()
    {
        return $this->belongsTo(TelegramBot::class, 'telegram_bot_id');
    }

    public function messages()
    {
        return $this->belongsToMany(TelegramMessage::class, 'channel_has_messages')
            ->withPivot('is_sent', 'sent_at')
            ->withTimestamps();
    }
}
