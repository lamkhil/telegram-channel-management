<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TelegramMessage extends Model
{
    public function channels()
    {
        return $this->belongsToMany(TelegramChannel::class, 'channel_has_messages')
            ->withPivot('is_sent', 'sent_at')
            ->withTimestamps();
    }
}
