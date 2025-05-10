<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class TemplateMessage extends Model
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
                $builder->where('template_messages.user_id', Auth::id());
            }
        });
    }
}
