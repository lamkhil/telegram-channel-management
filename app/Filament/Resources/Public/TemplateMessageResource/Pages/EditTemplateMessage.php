<?php

namespace App\Filament\Resources\Public\TemplateMessageResource\Pages;

use App\Filament\Resources\Public\TemplateMessageResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTemplateMessage extends EditRecord
{
    protected static string $resource = TemplateMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
