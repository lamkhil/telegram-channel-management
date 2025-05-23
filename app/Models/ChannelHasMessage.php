<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChannelHasMessage extends Model
{
    public function channel()
    {
        return $this->belongsTo(TelegramChannel::class, 'telegram_channel_id');
    }

    public function message()
    {
        return $this->belongsTo(TelegramMessage::class, 'telegram_message_id');
    }
}
