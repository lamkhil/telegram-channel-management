<?php

namespace App\Filament\Resources\Public;

use App\Filament\Resources\Public\TelegramChannelResource\Pages;
use App\Filament\Resources\Public\TelegramChannelResource\RelationManagers;
use App\Models\TelegramChannel;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\HtmlString;

class TelegramChannelResource extends Resource
{
    protected static ?string $model = TelegramChannel::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $modelLabel = "Channel";

    protected static ?string $navigationGroup = 'Management';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Placeholder::make('channel_id_instructions')
                ->label('How to Get Telegram Channel ID')
                ->content(new HtmlString("
                    <ol style='list-style-type: decimal; padding-left: 1.5rem;'>
                        <li>Open your Telegram app and go to your <strong>Channel</strong>.</li>
                        <li>Make sure the bot is added as an <strong>Administrator</strong> in the channel.</li>
                        <li>Send any message in the channel (or forward an existing one).</li>
                        <li>Use your bot to fetch the update, or enable logging to retrieve the message data.</li>
                        <li>The <code>Channel ID</code> in the update will look like <code>-1001234567890</code>. Alternatively, you can forward a message from your channel to <a style='color:blue;' href='https://t.me/userinfobot' target='_blank'>@userinfobot</a> to get the channel ID.</li>
                        <li>Copy the <code>chat.id</code> and paste it in the field below.</li>
                    </ol>

                "))->dehydrated(false)
                
                ->columnSpanFull(),
                Forms\Components\Select::make('telegram_bot_id')
                    ->relationship('bot', 'name')
                    ->required()
                    ->preload()
                    ->searchable()
                    ->label('Bot'),
                Forms\Components\TextInput::make('name')
                    ->required(),
                Forms\Components\TextInput::make('chat_id')
                    ->required()
                    ->label('Channel ID'),
                Forms\Components\Select::make('type')
                    ->options([
                        'public' => 'Public',
                        'private' => 'Private',
                    ])
                    ->native(false)
                    ->required()
                    ->label('Type'),
                Forms\Components\Toggle::make('default')
                    ->label('Default')
                    ->helperText('Set this channel as default for sending messages'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable()
                    ->searchable()
                    ->label('ID'),
                Tables\Columns\TextColumn::make('name')
                    ->sortable()
                    ->searchable()
                    ->label('Name'),
                Tables\Columns\TextColumn::make('chat_id')
                    ->sortable()
                    ->searchable()
                    ->label('Channel ID'),
                Tables\Columns\TextColumn::make('bot.name'),
                Tables\Columns\TextColumn::make('type')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'public' => 'Public',
                        'private' => 'Private',
                    })
                    ->color(function (string $state): string {
                        return match ($state) {
                            'public' => 'info',
                            'private' => 'success',
                            default => 'secondary',
                        };
                    })
                    ->label('Type'),
                Tables\Columns\ToggleColumn::make('default')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListTelegramChannels::route('/'),
            'create' => Pages\CreateTelegramChannel::route('/create'),
            'edit' => Pages\EditTelegramChannel::route('/{record}/edit'),
        ];
    }
}
