<?php

namespace App\Filament\Resources\Public\TelegramMessageResource\Pages;

use App\Filament\Resources\Public\TelegramMessageResource;
use App\Filament\Resources\Public\TelegramMessageResource\Widgets\SendMessageWidget;
use App\Models\ChannelHasMessage;
use App\Models\TelegramChannel;
use App\Models\TelegramMessage;
use App\Models\TemplateMessage;
use App\Services\Telegram\TelegramBotServices;
use Filament\Actions\Action;
use Filament\Forms\Components\Actions as ComponentsActions;
use Filament\Forms\Components\Actions\Action as ComponentsAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Colors\Color;
use Illuminate\Contracts\View\View;
use Laravel\Octane\Facades\Octane;

class ListTelegramMessages extends ListRecords implements HasForms
{
    use InteractsWithForms;
    protected static string $resource = TelegramMessageResource::class;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function getHeader(): ?View
    {
        return view('form-message');
    }

    public function getTemplate()
    {

        $templateAction = [];
        $templates = TemplateMessage::where('active', true)->get();

        foreach ($templates as $template) {
            $templateAction[] = ComponentsAction::make($template->name)
                ->color(Color::hex($template->button_color))
                ->action(function ($set) use ($template) {
                    $set('message', $template->text);
                    if ($template->file) {
                        $set('file', [$template->file]);
                    }else{
                        $set('file', null);
                    }
                });
        }

        return $templateAction;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([

                Select::make('telegram_channel_ids')
                    ->label('Channels')
                    ->multiple()
                    ->options(
                        TelegramChannel::all()->pluck('name', 'id')
                    )
                    ->required()
                    ->native(false)
                    ->default(TelegramChannel::where('default', true)->pluck('id')->toArray()),
                Textarea::make('message')
                    ->autosize()
                    ->label('Message'),
                ComponentsActions::make($this->getTemplate()),
                FileUpload::make('file')
                    ->label('File')
                    ->acceptedFileTypes(['image/*', 'video/*', 'audio/*', 'gif/*'])
                    ->maxSize(1024 * 10)
                    ->columnSpanFull(),


            ])
            ->statePath('data');
    }


    public function sendAction(): Action
    {
        return Action::make('send')
            ->label('Send')
            ->keyBindings([
                'command+s',
                'ctrl+s',
                'meta+s'
            ])
            ->action(function (): void {
                $data = $this->form->getState();

                $message = $data['message'];
                $file = $data['file'];
                $channels = $data['telegram_channel_ids'];



                if (count($channels) == 0) {
                    Notification::make('konekinChannel')
                        ->title('Oops!!')
                        ->body('Choose your channel first!!!')
                        ->danger()
                        ->send();
                    return;
                }

                if ($file == null && $message == null) {
                    return;
                }

                $telegramMessage = TelegramMessage::create([
                    'content' => $message,
                    'file' => $file,
                    'is_sent' => false,
                    'sent_at' => now(),
                ]);

                $success = 0;

                $total = count($channels);

                $telegramMessageId = $telegramMessage->id;

                $fileUrl = $file != null ? asset('storage/' . $file) : null;

                $results = Octane::concurrently(
                    collect($channels)->map(fn($chatId) => function () use ($chatId, $telegramMessageId, $message, $fileUrl) {
                        try {
                            $channel = TelegramChannel::find($chatId);
                            $bot = $channel->bot;

                            $channelMessage = ChannelHasMessage::create([
                                'telegram_channel_id' => $chatId,
                                'telegram_message_id' => $telegramMessageId,
                            ]);

                            if ($fileUrl) {
                                $extension = strtolower(pathinfo($fileUrl, PATHINFO_EXTENSION));
                                if (in_array($extension, ['jpg', 'jpeg', 'png'])) {
                                    $result = TelegramBotServices::sendPhoto($bot->token, $channel->chat_id, $fileUrl, $message);
                                } else if (in_array($extension, ['mp4', 'mov'])) {
                                    $result = TelegramBotServices::sendVideo($bot->token, $channel->chat_id, $fileUrl, $message);
                                } else if (in_array($extension, ['mp3', 'wav'])) {
                                    $result = TelegramBotServices::sendAudio($bot->token, $channel->chat_id, $fileUrl, $message);
                                } else if (in_array($extension, ['gif'])) {
                                    $result = TelegramBotServices::sendAnimation($bot->token, $channel->chat_id, $fileUrl, $message);
                                }
                            } else {
                                $result = TelegramBotServices::sendMessage($bot->token, $channel->chat_id, $message);
                            }


                            $channelMessage->update([
                                'message_id' => $result?->result?->message_id ?? null
                            ]);

                            return [
                                'ok' => $result->ok ?? false,
                                'channel' => $channel->name,
                                'description' => $result->description ?? '',
                            ];
                        } catch (\Throwable $e) {
                            return [
                                'ok' => false,
                                'channel' => $chatId,
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

                $telegramMessage->update([
                    'is_sent' => $success === $total,
                    'sent_at' => now(),
                    'success' => $success,
                    'total' => $total,
                ]);

                $this->form->fill();
            });
    }
}
