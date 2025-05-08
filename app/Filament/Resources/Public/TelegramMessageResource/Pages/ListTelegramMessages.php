<?php

namespace App\Filament\Resources\Public\TelegramMessageResource\Pages;

use App\Filament\Resources\Public\TelegramMessageResource;
use App\Models\TelegramChannel;
use App\Models\TelegramMessage;
use App\Services\Telegram\TelegramBotServices;
use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListTelegramMessages extends ListRecords
{
    protected static string $resource = TelegramMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('send')
                ->form(function (Form $form) {
                    return $form->schema([
                        Textarea::make('message')
                            ->required()
                            ->label('Message'),
                        Select::make('telegram_channel_ids')
                            ->multiple()
                            ->options(
                                TelegramChannel::all()->pluck('name', 'id')
                            )
                    ]);
                })->action(function (array $data) {
                    // Send message to all selected channels
                    foreach ($data['telegram_channel_ids'] as $channelId) {
                        try {
                            $telegramChannel = TelegramChannel::find($channelId);
                            $bot = $telegramChannel->bot;
                            $result = TelegramBotServices::sendMessage(
                                token: $bot->token,
                                chatId: $telegramChannel->chat_id,
                                message: $data['message']
                            );

                            if ($result->ok) {
                                TelegramMessage::create([
                                    'telegram_channel_id' => $telegramChannel->id,
                                    'content' => $data['message'],
                                    'is_sent' => true,
                                    'sent_at' => now(),
                                ]);
                                Notification::make()
                                    ->title('Success')
                                    ->body('Message sent to channel: ' . $telegramChannel->name)
                                    ->success()
                                    ->send();
                            } else {
                                Notification::make()
                                    ->title('Failed to send message to channel: ' . $telegramChannel->name)
                                    ->body($result->description ?? 'Unknown error')
                                    ->danger()
                                    ->send();
                                TelegramMessage::create([
                                    'telegram_channel_id' => $telegramChannel->id,
                                    'content' => $data['message'],
                                    'is_sent' => false,
                                    'sent_at' => now(),
                                ]);
                            }
                        } catch (\Throwable $th) {
                            Notification::make()
                                ->title('Error')
                                ->body('Failed to send message to channel: ' . $telegramChannel->name)
                                ->danger()
                                ->send();
                            TelegramMessage::create([
                                'telegram_channel_id' => $telegramChannel->id,
                                'content' => $data['message'],
                                'is_sent' => false,
                                'sent_at' => now(),
                            ]);
                        }
                    }
                })
        ];
    }
}
