<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class TelegramBot extends Model
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
                $builder->where('telegram_bots.user_id', Auth::id());
            }
        });
    }

    public function channels()
    {
        return $this->hasMany(TelegramChannel::class, 'telegram_bot_id');
    }
}
