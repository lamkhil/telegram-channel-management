<?php

namespace App\Filament\Resources\Public;

use App\Filament\Resources\Public\TemplateMessageResource\Pages;
use App\Filament\Resources\Public\TemplateMessageResource\RelationManagers;
use App\Models\TemplateMessage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TemplateMessageResource extends Resource
{
    protected static ?string $model = TemplateMessage::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $modelLabel = "Template";

    protected static ?string $navigationGroup = 'Management';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->columnSpanFull()
                    ->label('Template Name'),
                Forms\Components\Textarea::make('text')
                    ->columnSpanFull()
                    ->autosize()
                    ->label('Message'),
                Forms\Components\FileUpload::make('file')
                    ->label('File')
                    ->acceptedFileTypes(['image/*', 'video/*', 'audio/*', 'gif/*'])
                    ->maxSize(1024 * 10)
                    ->columnSpanFull(),
                Forms\Components\ColorPicker::make('button_color')
                    ->label('Button Color')
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('active')
                    ->label('Active')
                    ->helperText('Set this template as active for sending messages'),
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
                    ->badge()
                    ->color(function ($record) {
                        return Color::hex($record->button_color);
                    })
                    ->searchable()
                    ->label('Template Name'),
                Tables\Columns\TextColumn::make('text')
                    ->sortable()
                    ->wrap()
                    ->searchable()
                    ->label('Message'),
                Tables\Columns\ToggleColumn::make('active')
                    ->label('Active'),
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
            'index' => Pages\ListTemplateMessages::route('/'),
            'create' => Pages\CreateTemplateMessage::route('/create'),
            'edit' => Pages\EditTemplateMessage::route('/{record}/edit'),
        ];
    }
}
