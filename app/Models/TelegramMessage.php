<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TelegramMessage extends Model
{
    public function channel()
    {
        return $this->belongsTo(TelegramChannel::class, 'telegram_channel_id');
    }
}
