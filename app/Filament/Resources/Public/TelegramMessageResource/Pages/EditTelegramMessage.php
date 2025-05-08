<?php

namespace App\Filament\Resources\Public\TelegramMessageResource\Pages;

use App\Filament\Resources\Public\TelegramMessageResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTelegramMessage extends EditRecord
{
    protected static string $resource = TelegramMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
