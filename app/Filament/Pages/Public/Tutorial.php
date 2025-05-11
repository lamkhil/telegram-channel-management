<?php

namespace App\Filament\Pages\Public;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;

class Tutorial extends Page implements HasForms
{
    use InteractsWithForms;
    
    protected static ?string $navigationIcon = 'heroicon-o-information-circle';

    protected static string $view = 'filament.pages.public.tutorial';

    protected static ?string $navigationGroup = 'Management';

    protected static ?int $navigationSort = 4;

    public $data = [];

    public function mount() : void
    {
        $this->form->fill([
            'video' => 'tutorial.mp4'
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                FileUpload::make('video')
                    ->label('')
                    ->disk('public')
                    ->placeholder('Watch the tutorial video '.asset('storage/tutorial.mp4'))
                    ->deletable(false)
                    ->disabled()
                    ->columnSpanFull()
            ])->statePath('data');
    }


}
