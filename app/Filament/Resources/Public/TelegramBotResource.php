<?php

namespace App\Filament\Resources\Public;

use App\Filament\Resources\Public\TelegramBotResource\Pages;
use App\Filament\Resources\Public\TelegramBotResource\RelationManagers;
use App\Models\TelegramBot;
use App\Services\Telegram\TelegramBotServices;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TelegramBotResource extends Resource
{
    protected static ?string $model = TelegramBot::class;


    protected static ?int $navigationSort = 2;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $modelLabel = "Bot Settings";

    protected static ?string $navigationGroup = 'Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('token')
                    ->label('Token')
                    ->required()
                    ->password()
                    ->columnSpanFull()
                    ->disabledOn('edit'),
                Forms\Components\TextInput::make('name')
                    ->label('Name')
                    ->disabled()
                    ->reactive()
                    ->visibleOn('view')
                    ->required()
                    ->dehydrated(true),
                Forms\Components\TextInput::make('bot_username')
                    ->label('Bot Username')
                    ->disabled()
                    ->reactive()
                    ->visibleOn('view')
                    ->required()
                    ->dehydrated(true),
                Forms\Components\TextInput::make('bot_id')
                    ->label('Bot ID')
                    ->disabled()
                    ->visibleOn('view')
                    ->columnSpanFull()
                    ->required()
                    ->reactive()
                    ->dehydrated(true),
                Forms\Components\Toggle::make('can_join_groups')
                    ->label('Can Join Groups')
                    ->disabled()
                    ->reactive()
                    ->visibleOn('view')
                    ->dehydrated(true),
                Forms\Components\Toggle::make('can_read_all_group_messages')
                    ->label('Can Read All Group Messages')
                    ->disabled()
                    ->visibleOn('view')
                    ->reactive()
                    ->dehydrated(true),
                Forms\Components\Toggle::make('supports_inline_queries')
                    ->label('Supports Inline Queries')
                    ->disabled()
                    ->visibleOn('view')
                    ->reactive()
                    ->dehydrated(true),
                Forms\Components\Toggle::make('can_connect_to_business')
                    ->label('Can Connect To Business')
                    ->disabled()
                    ->visibleOn('view')
                    ->reactive()
                    ->dehydrated(true),
                Forms\Components\Toggle::make('has_main_web_app')
                    ->label('Has Main Web App')
                    ->disabled()
                    ->visibleOn('view')
                    ->reactive()
                    ->dehydrated(true),
                Hidden::make('user_id')
                    ->default(auth()->user()->id)


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('bot_username')
                    ->label('Bot Username')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('bot_id')
                    ->label('Bot ID')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListTelegramBots::route('/'),
            'create' => Pages\CreateTelegramBot::route('/create'),
            'view' => Pages\ViewTelegramBot::route('/{record}/edit'),
        ];
    }
}
