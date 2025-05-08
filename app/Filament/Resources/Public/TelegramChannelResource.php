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
