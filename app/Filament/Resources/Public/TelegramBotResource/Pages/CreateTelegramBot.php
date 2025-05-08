<?php

namespace App\Filament\Resources\Public\TelegramBotResource\Pages;

use App\Filament\Resources\Public\TelegramBotResource;
use App\Services\Telegram\TelegramBotServices;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateTelegramBot extends CreateRecord
{
    protected static string $resource = TelegramBotResource::class;

    public function mutateFormDataBeforeCreate(array $data): array
    {
        $bot = TelegramBotServices::getBot($data['token']);
        if ($bot->ok) {
            $data['name'] = $bot->result->first_name;
            $data['bot_username'] = $bot->result->username;
            $data['bot_id'] = $bot->result->id;
            $data['can_join_groups'] = $bot->result->can_join_groups;
            $data['can_read_all_group_messages'] = $bot->result->can_read_all_group_messages;
            $data['supports_inline_queries'] = $bot->result->supports_inline_queries;
            $data['can_connect_to_business'] = $bot->result->can_connect_to_business;
            $data['has_main_web_app'] = $bot->result->has_main_web_app;
        } else {
            Notification::make()
                ->title('Error')
                ->body('Token Invalid')
                ->danger()
                ->send();
            $this->halt();
            return [];
        }
        return $data;
    }
}
