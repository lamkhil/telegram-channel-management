<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TelegramBot extends Model
{
    public function channels()
    {
        return $this->hasMany(TelegramChannel::class, 'telegram_bot_id');
    }
}
