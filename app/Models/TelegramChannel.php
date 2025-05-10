<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class TelegramChannel extends Model
{
    protected static function booted()
    {
        static::creating(function ($model) {
            if (Auth::check() && empty($model->user_id)) {
                $model->user_id = Auth::id();
            }
        });
        static::addGlobalScope('user_id', function ($builder) {
            if (Auth::check()) {
                $builder->where('user_id', Auth::id());
            }
        });
    }

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
