<?php

namespace App\Filament\Resources\Public\TelegramBotResource\Pages;

use App\Filament\Resources\Public\TelegramBotResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTelegramBots extends ListRecords
{
    protected static string $resource = TelegramBotResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
