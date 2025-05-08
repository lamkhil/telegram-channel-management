<?php

namespace App\Filament\Resources\Public\TelegramMessageResource\Pages;

use App\Filament\Resources\Public\TelegramMessageResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTelegramMessage extends CreateRecord
{
    protected static string $resource = TelegramMessageResource::class;
}
