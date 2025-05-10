<?php

namespace App\Filament\Resources\Public;

use App\Filament\Resources\Public\TelegramMessageResource\Pages;
use App\Models\ChannelHasMessage;
use App\Models\TelegramMessage;
use App\Services\Telegram\TelegramBotServices;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Enums\Alignment;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Laravel\Octane\Facades\Octane;

class TelegramMessageResource extends Resource
{
    protected static ?string $model = TelegramMessage::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = "Message";

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('channels.name')
                    ->sortable()
                    ->searchable()
                    ->label('Channel'),
                Tables\Columns\TextColumn::make('content')
                    ->sortable()
                    ->wrap()
                    ->searchable()
                    ->label('Message'),
                Tables\Columns\TextColumn::make('success')
                    ->sortable()
                    ->label('Success')
                    ->formatStateUsing(function ($state, $record) {
                        return $state . '/' . $record->total;
                    }),
                Tables\Columns\IconColumn::make('is_sent')
                    ->label('Sent')
                    ->boolean(),
                Tables\Columns\TextColumn::make('sent_at')
                    ->sortable()
                    ->dateTime()
                    ->label('Sent At'),
                Tables\Columns\TextColumn::make('created_at')
                    ->sortable()
                    ->dateTime()
                    ->label('Created At'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('edit')
                    ->fillForm(function($record, $data){
                        $data['content'] = $record->content;
                        return $data;
                    })
                    ->form(function ($form) {
                        return $form->schema([
                            Textarea::make('content')
                                ->autosize()
                                ->label('Message'),
                        ]);
                    })
                    ->modalFooterActionsAlignment(
                        Alignment::End
                    )
                    ->action(function ($record, $data) {
                        $message = $data['content'];
                        $channelMessages = ChannelHasMessage::where('telegram_message_id', $record->id)
                        ->get();
                        $results = Octane::concurrently(
                            collect($channelMessages)->map(fn($channelMessage) => function () use ($channelMessage, $record, $message) {
                                try {
                                    $result = TelegramBotServices::editMessageText(
                                        $channelMessage->channel->bot->token,
                                        $channelMessage->channel->chat_id,
                                        $channelMessage->message_id,
                                        $message
                                    );

                                    return [
                                        'ok' => $result->ok ?? false,
                                        'channel' => $channelMessage->channel->name,
                                        'description' => $result->description ?? '',
                                    ];
                                    
                                } catch (\Throwable $e) {
                                    return [
                                        'ok' => false,
                                        'channel' => $channelMessage->channel->name,
                                        'description' => $e->getMessage(),
                                    ];
                                }
                            })->all()
                        );

                        $success = 0;
                        foreach ($results as $result) {
                            if ($result['ok']) {
                                Notification::make()
                                    ->title('Message Sent to ' . $result['channel'])
                                    ->success()
                                    ->send();
                                $success++;
                            } else {
                                Notification::make()
                                    ->title('Failed to send message to ' . $result['channel'])
                                    ->body($result['description'])
                                    ->danger()
                                    ->send();
                            }
                        }
        
                        $record->update([
                            'is_sent' => $success === count($results),
                            'sent_at' => now(),
                            'success' => $success,
                            'total' => count($results),
                            'content' => $message,
                        ]);
                    })
            ])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTelegramMessages::route('/')
        ];
    }
}
