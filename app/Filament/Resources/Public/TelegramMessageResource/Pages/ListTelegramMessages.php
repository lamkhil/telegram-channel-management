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
                    $set('file', $template->file);
                });
        }

        return $templateAction;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                FileUpload::make('file')
                    ->label('File')
                    ->acceptedFileTypes(['image/*', 'video/*', 'audio/*', 'gif/*'])
                    ->maxSize(1024 * 10)
                    ->columnSpanFull(),
                Textarea::make('message')
                    ->required()
                    ->autosize()
                    ->label('Message'),
                ComponentsActions::make($this->getTemplate()),
                Select::make('telegram_channel_ids')
                    ->label('Channels')
                    ->multiple()
                    ->options(
                        TelegramChannel::all()->pluck('name', 'id')
                    )
                    ->native(false)
                    ->default(TelegramChannel::where('default', true)->pluck('id')->toArray())


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

                $telegramMessage = TelegramMessage::create([
                    'content' => $message,
                    'file' => $file,
                    'is_sent' => false,
                    'sent_at' => now(),
                ]);

                $sent = true;

                $success = 0;

                $total = count($channels);

                foreach ($channels as $chatId) {
                    $channel = TelegramChannel::find($chatId);
                    $bot = $channel->bot;

                    ChannelHasMessage::create([
                        'telegram_channel_id' => $chatId,
                        'telegram_message_id' => $telegramMessage->id,
                    ]);

                    if ($file) {
                        $extension = strtolower($file->getClientOriginalExtension());

                        if (in_array($extension, ['jpg', 'jpeg', 'png'])) {
                            $result = TelegramBotServices::sendPhoto($bot->token, $channel->chat_id, $file, $message);
                        } elseif (in_array($extension, ['mp4', 'mov'])) {
                            try {
                                $result = TelegramBotServices::sendAnimation($bot->token, $channel->chat_id, $file, $message);
                            } catch (\Exception $e) {
                                $result = TelegramBotServices::sendVideo($bot->token, $channel->chat_id, $file, $message);
                            }
                        } elseif (in_array($extension, ['gif', 'webp'])) {
                            $result = TelegramBotServices::sendAnimation($bot->token, $channel->chat_id, $file, $message);
                        } else {
                            // fallback kirim sebagai document
                            $result = TelegramBotServices::sendDocument($bot->token, $channel->chat_id, $file, $message);
                        }
                    } else {
                        // Kirim teks saja
                        $result = TelegramBotServices::sendMessage($bot->token, $channel->chat_id, $message);
                    }


                    if ($result->ok) {
                        Notification::make()
                            ->title('Message Sent to '.$channel->name )
                            ->success()
                            ->send();
                        $success++;
                    } else {
                        Notification::make()
                            ->title('Failed to send message to '.$channel->name)
                            ->body($result->description)
                            ->danger()
                            ->send();

                        $sent = false;
                    }
                }

                $telegramMessage->update([
                    'is_sent' => $sent,
                    'sent_at' => now(),
                    'success' => $success,
                    'total' => $total,
                ]);

                $this->form->fill();

            });
    }
}
