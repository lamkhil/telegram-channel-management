<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TelegramChannel extends Model
{
    public function bot()
    {
        return $this->belongsTo(TelegramBot::class, 'telegram_bot_id');
    }
}
