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
    protected function getPlugins(): array
    {
        return [
            $this->initArchivablePlugin(),
            $this->initGeneralSettingsPlugin(),
            $this->initLoginBackgroundImagePlugin(),
            $this->initSpotlightPlugin(),
        ];
    }

    // Archivable plugin | https://filamentphp.com/plugins/okeonline-archivable#installation
    protected function initArchivablePlugin()
    {
        return \Okeonline\FilamentArchivable\FilamentArchivablePlugin::make();
    }

    // General Settings | https://filamentphp.com/plugins/joaopaulolndev-general-settings#installation
    protected function initGeneralSettingsPlugin()
    {
        return \Joaopaulolndev\FilamentGeneralSettings\FilamentGeneralSettingsPlugin::make()
            ->setIcon('heroicon-o-cog')
            ->setNavigationGroup('Settings')
            ->setTitle('General Settings')
            ->setNavigationLabel('General Settings');
    }

    // image Dashboard Background plugin | https://filamentphp.com/plugins/swisnl-backgrounds#installation
    protected function initLoginBackgroundImagePlugin()
    {
        return \Swis\Filament\Backgrounds\FilamentBackgroundsPlugin::make()
            ->showAttribution(false)
            ->imageProvider(
                \Swis\Filament\Backgrounds\ImageProviders\MyImages::make()
                    ->directory('images/background-images'),
            );
    }

    // Spotlight plugin | https://filamentphp.com/plugins/pxlrbt-spotlight#installation
    protected function initSpotlightPlugin()
    {
        return \pxlrbt\FilamentSpotlight\SpotlightPlugin::make();
    }

    public function panel(Panel $panel): Panel
    {
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
            ->middleware($this->getMiddleware())
            ->authMiddleware($this->getAuthMiddleware())
            ->plugins($this->getPlugins());
    }

    protected function getMiddleware(): array
    {
        return [
            EncryptCookies::class,
            AddQueuedCookiesToResponse::class,
            StartSession::class,
            AuthenticateSession::class,
            ShareErrorsFromSession::class,
            VerifyCsrfToken::class,
            SubstituteBindings::class,
            DisableBladeIconComponents::class,
            DispatchServingFilamentEvent::class,
        ];
    }

    protected function getAuthMiddleware(): array
    {
        return [
            Authenticate::class,
        ];
    }
}
