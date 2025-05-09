<?php

namespace App\Filament\Resources\Public\TemplateMessageResource\Pages;

use App\Filament\Resources\Public\TemplateMessageResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTemplateMessages extends ListRecords
{
    protected static string $resource = TemplateMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
