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
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors(['primary' => $this->getColor('gray'),])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->navigation($this->makeMenu())
            ->pages($this->getPages())
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets($this->getWidgets())
            ->middleware($this->getMiddleware())
            ->authMiddleware($this->getAuthMiddleware())
            ->plugins($this->getPlugins());
    }

    protected function makeMenu(): \Closure
    {
        return function (\Filament\Navigation\NavigationBuilder $builder): \Filament\Navigation\NavigationBuilder {
            return $builder->groups([
                \Filament\Navigation\NavigationGroup::make('Shop')->items([
//                        ...\App\Filament\Resources\OrderResource::getNavigationItems(),
//                        ...\App\Filament\Resources\ProductResource::getNavigationItems(),
//                        ...\App\Filament\Resources\StockResource::getNavigationItems(),
//                        ...\App\Filament\Resources\ShippingTypeResource::getNavigationItems(),
                ]),
                \Filament\Navigation\NavigationGroup::make('Content')->items([
                    ...\App\Filament\Resources\PostResource::getNavigationItems(),
                    ...\App\Filament\Resources\CategoryResource::getNavigationItems(),
//                        ...NavigationResource::getNavigationItems()
                ]),
                \Filament\Navigation\NavigationGroup::make('Users & Roles')->items([
//                        ...UserResource::getNavigationItems()
                ])
            ]);
        };
    }

    protected function getColor(?string $index = null): array
    {

        $colors = [
            'slate' => Color::Slate,
            'gray' => Color::Gray,
            'zinc' => Color::Zinc,
            'neutral' => Color::Neutral,
            'stone' => Color::Stone,
            'red' => Color::Red,
            'orange' => Color::Orange,
            'amber' => Color::Amber,
            'yellow' => Color::Yellow,
            'lime' => Color::Lime,
            'green' => Color::Green,
            'emerald' => Color::Emerald,
            'teal' => Color::Teal,
            'cyan' => Color::Cyan,
            'sky' => Color::Sky,
            'blue' => Color::Blue,
            'indigo' => Color::Indigo,
            'violet' => Color::Violet,
            'purple' => Color::Purple,
            'fuchsia' => Color::Fuchsia,
            'pink' => Color::Pink,
            'rose' => Color::Rose,
        ];

        if ($index) {
            $index = strtolower($index);

            return $colors[$index];
        } else {
            // return a random color
            return $colors[array_rand($colors)];
        }

    }

    protected function getPages(): array
    {
        return [
            Pages\Dashboard::class,
        ];
    }

    protected function getWidgets(): array
    {
        return [
            Widgets\AccountWidget::class,
            Widgets\FilamentInfoWidget::class,
        ];
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

    protected function getPlugins(): array
    {
        return [
            $this->initArchivablePlugin(),
            $this->initGeneralSettingsPlugin(),
            $this->initLoginBackgroundImagePlugin(),
            $this->initSpotlightPlugin(),
            $this->initShieldPlugin(),
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

    // Shield Plugin | https://filamentphp.com/plugins/bezhansalleh-shield#installation
    protected function initShieldPlugin()
    {
        return \BezhanSalleh\FilamentShield\FilamentShieldPlugin::make();
    }
}
