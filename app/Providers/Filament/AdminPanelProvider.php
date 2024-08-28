<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
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
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {

        // image Dashboard Background plugin | https://filamentphp.com/plugins/swisnl-backgrounds#installation
        $loginBackgroundImage = \Swis\Filament\Backgrounds\FilamentBackgroundsPlugin::make() //https://filamentphp.com/plugins/swisnl-backgrounds
                ->showAttribution(false)
                ->imageProvider(
                    \Swis\Filament\Backgrounds\ImageProviders\MyImages::make()
                        ->directory('images/background-images'),
                );

        // Archivable plugin | https://filamentphp.com/plugins/okeonline-archivable#installation
        $archivable = \Okeonline\FilamentArchivable\FilamentArchivablePlugin::make();

        // General Settings | https://filamentphp.com/plugins/joaopaulolndev-general-settings#installation
        $generalSettings = \Joaopaulolndev\FilamentGeneralSettings\FilamentGeneralSettingsPlugin::make()
//                    ->canAccess(fn() => auth()->user()->id > 0)
            ->setIcon('heroicon-o-cog')
            ->setNavigationGroup('Settings')
            ->setTitle('General Settings')
            ->setNavigationLabel('General Settings'),
        $plugins = [
            $loginBackgroundImage,
            $archivable,
            $generalSettings,
        ];

        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
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
            ->plugins($plugins);
    }
}
