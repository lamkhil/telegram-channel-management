<?php

namespace App\Filament\Resources\Public\TelegramBotResource\Pages;

use App\Filament\Resources\Public\TelegramBotResource;
use App\Services\Telegram\TelegramBotServices;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ViewRecord;

class ViewTelegramBot extends ViewRecord
{
    protected static string $resource = TelegramBotResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    public function mount(int | string $record): void
    {
        $this->record = $this->resolveRecord($record);

        $this->authorizeAccess();


        $bot = TelegramBotServices::getBot($this->record->token);

        if ($bot->ok) {
            $this->record->name = $bot->result->first_name;
            $this->record->bot_username = $bot->result->username;
            $this->record->bot_id = $bot->result->id;
            $this->record->can_join_groups = $bot->result->can_join_groups;
            $this->record->can_read_all_group_messages = $bot->result->can_read_all_group_messages;
            $this->record->supports_inline_queries = $bot->result->supports_inline_queries;
            $this->record->can_connect_to_business = $bot->result->can_connect_to_business;
            $this->record->has_main_web_app = $bot->result->has_main_web_app;

            $this->record->save();
        } 

        if (! $this->hasInfolist()) {
            $this->fillForm();
        }
    }
}
