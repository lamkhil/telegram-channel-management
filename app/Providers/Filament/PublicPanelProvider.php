<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Kenepa\Banner\BannerPlugin;

class PublicPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('public')
            ->path('')
            ->login()
            ->emailVerification(
                isRequired:false
            )
            ->registration()
            ->passwordReset()
            ->colors([
                'primary' => Color::Blue,
            ])
            ->discoverResources(in: app_path('Filament/Resources/Public'), for: 'App\\Filament\\Resources\\Public')
            ->discoverPages(in: app_path('Filament/Pages/Public'), for: 'App\\Filament\\Pages\\Public')
            ->discoverWidgets(in: app_path('Filament/Widgets/Public'), for: 'App\\Filament\\Widgets\\Public')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->favicon(asset('favicon-32x32.png'))
            ->theme(asset('css/filament/public/theme.css'))
            ->plugins([
                BannerPlugin::make()
                    ->disableBannerManager()
             ])
             ->brandLogo(asset('logo.jpg'))
             ->brandName('Instacast')
             ->brandLogoHeight('50px');
    }
}
