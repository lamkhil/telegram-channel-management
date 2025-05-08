<?php

namespace App\Filament\Resources\Public;

use App\Filament\Resources\Public\TelegramMessageResource\Pages;
use App\Filament\Resources\Public\TelegramMessageResource\RelationManagers;
use App\Models\TelegramMessage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

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
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable()
                    ->searchable()
                    ->label('ID'),
                Tables\Columns\TextColumn::make('channel.name')
                    ->sortable()
                    ->searchable()
                    ->label('Channel'),
                Tables\Columns\TextColumn::make('content')
                    ->sortable()
                    ->searchable()
                    ->label('Message'),
                Tables\Columns\IconColumn::make('is_sent')
                    ->label('Sent')
                    ->boolean()
                    ->trueIcon('heroicon-o-check')
                    ->falseIcon('heroicon-o-x'),
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
            'index' => Pages\ListTelegramMessages::route('/'),
            'create' => Pages\CreateTelegramMessage::route('/create'),
            'edit' => Pages\EditTelegramMessage::route('/{record}/edit'),
        ];
    }
}
