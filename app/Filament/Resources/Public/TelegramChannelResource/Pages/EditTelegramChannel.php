<?php

namespace App\Filament\Resources\Public\TelegramChannelResource\Pages;

use App\Filament\Resources\Public\TelegramChannelResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTelegramChannel extends EditRecord
{
    protected static string $resource = TelegramChannelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
