<?php

namespace App\Providers\Filament;

use App\Filament\BKP\NavigationResource;
use App\Filament\Resources\{CategoryResource, PostResource};
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Closure;
use Filament\Http\Middleware\{Authenticate, DisableBladeIconComponents, DispatchServingFilamentEvent};
use Filament\Support\Colors\Color;
use Filament\{Forms\Components\Field,
    Navigation\MenuItem,
    Navigation\NavigationBuilder,
    Navigation\NavigationGroup,
    Pages,
    Panel,
    PanelProvider,
    Tables\Columns\Column,
    Widgets};
use GeoSot\FilamentEnvEditor\FilamentEnvEditorPlugin;
use Illuminate\Cookie\Middleware\{AddQueuedCookiesToResponse, EncryptCookies};
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\{AuthenticateSession, StartSession};
use Illuminate\Support\HtmlString;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use InvalidArgumentException;
use Joaopaulolndev\FilamentEditProfile\FilamentEditProfilePlugin;
use Joaopaulolndev\FilamentEditProfile\Pages\EditProfilePage;
use Joaopaulolndev\FilamentGeneralSettings\FilamentGeneralSettingsPlugin;
use Okeonline\FilamentArchivable\FilamentArchivablePlugin;
use pxlrbt\FilamentSpotlight\SpotlightPlugin;
use Swis\Filament\Backgrounds\FilamentBackgroundsPlugin;
use Swis\Filament\Backgrounds\ImageProviders\MyImages;

class AdminPanelProvider extends PanelProvider
{
    private bool $enablePersonalNavigation = false;

    public function panel(Panel $panel): Panel
    {
        return $panel
            ->bootUsing(fn () => $this->bootUsing())
            ->default()
            ->id('admin')
            ->path('admin')
            ->databaseNotifications()
            ->login()
            ->colors(['primary' => $this->getColor('gray'), ])
            ->discoverResources(
                in: app_path('Filament/Resources'),
                for: 'App\\Filament\\Resources'
            )
            ->discoverPages(
                in: app_path('Filament/Pages'),
                for: 'App\\Filament\\Pages'
            )
            ->navigation($this->getNavigation())
            ->pages($this->getPages())
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets($this->getWidgets())
            ->middleware($this->getMiddleware())
            ->authMiddleware($this->getAuthMiddleware())
            ->plugins($this->getPlugins())
            ->userMenuItems([
                'profile' => MenuItem::make()
                    ->label(fn () => auth()->user()->name ?? 'Profile')
                    ->url(fn (): string => EditProfilePage::getUrl())
                    ->icon('heroicon-m-user-circle'),
            ]);
            // ->theme(new HtmlString('resources/css/themes/filament/classic/index.css'));
            // ->viteTheme('resources/css/themes/filament/classic.css');
            // ->viteTheme('resources/css/filament/admin/theme.css');
    }

    private function bootUsing(): void
    {

        //        \Filament\Facades\Filament::serving(function () {
        ////            \Filament\Facades\Filament::notify('Você foi autenticado com sucesso!');
        //            \Filament\Notifications\Notification::make()
        //                ->title('Operação realizada com sucesso!')
        //                ->success()
        //                ->send();
        //        });

        Field::configureUsing(function (Field $field) {
            $field->translateLabel();
        });

        Column::configureUsing(function (Column $column) {
            $column->translateLabel();
        });
    }

    protected function getNavigation(): true|Closure
    {
        return $this->enablePersonalNavigation ? $this->makePersonalNavigation() : true;
    }

    protected function makePersonalNavigation(): Closure
    {
        return function (NavigationBuilder $builder): NavigationBuilder {
            return $builder->groups([
//                NavigationGroup::make('Shop')->items([
//                    ...\App\Filament\Resources\OrderResource::getNavigationItems(),
//                    ...\App\Filament\Resources\ProductResource::getNavigationItems(),
//                    ...\App\Filament\Resources\StockResource::getNavigationItems(),
//                    ...\App\Filament\Resources\ShippingTypeResource::getNavigationItems(),
//                ]),
                NavigationGroup::make('Content')->items([
                    ...PostResource::getNavigationItems(),
                    ...CategoryResource::getNavigationItems(),
//                    ...NavigationResource::getNavigationItems(),
                ]),
//                NavigationGroup::make('Users & Roles')->items([
//                    ...UserResource::getNavigationItems()
//                ]),
            ]);
        };
    }

    protected function getColor(?string $index = 'amber'): array
    {
        $colors = [
            'slate'   => Color::Slate,
            'gray'    => Color::Gray,
            'zinc'    => Color::Zinc,
            'neutral' => Color::Neutral,
            'stone'   => Color::Stone,
            'red'     => Color::Red,
            'orange'  => Color::Orange,
            'amber'   => Color::Amber,
            'yellow'  => Color::Yellow,
            'lime'    => Color::Lime,
            'green'   => Color::Green,
            'emerald' => Color::Emerald,
            'teal'    => Color::Teal,
            'cyan'    => Color::Cyan,
            'sky'     => Color::Sky,
            'blue'    => Color::Blue,
            'indigo'  => Color::Indigo,
            'violet'  => Color::Violet,
            'purple'  => Color::Purple,
            'fuchsia' => Color::Fuchsia,
            'pink'    => Color::Pink,
            'rose'    => Color::Rose,
        ];

        $index = strtolower($index);

        if (!array_key_exists($index, $colors)) {
            throw new InvalidArgumentException("Invalid color index: {$index}");
        }

        return $colors[$index];
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
            \Hasnayeen\Themes\Http\Middleware\SetTheme::class
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
            $this->initEditProfilePlugin(),
            $this->initEnvironmentEditorPlugin(),
            $this->initEnvironmentIndicatorPlugin(),
            $this->initThemeManagementPlugin(),

            \TomatoPHP\FilamentArtisan\FilamentArtisanPlugin::make(),

            \TomatoPHP\FilamentBrowser\FilamentBrowserPlugin::make()
                ->hiddenFolders([
//                    base_path(),
//                    base_path('lang'),

                ])
                ->hiddenFiles([
                    base_path('.env')
                ])
                ->hiddenExtantions([
                    "php"
                ])
                ->allowCreateFolder()
                ->allowEditFile()
                ->allowCreateNewFile()
                ->allowCreateFolder()
                ->allowRenameFile()
                ->allowDeleteFile()
                ->allowMarkdown()
                ->allowCode()
                ->allowPreview()
                ->basePath(storage_path())
        ];
    }

    // Archivable plugin | https://filamentphp.com/plugins/okeonline-archivable#installation
    protected function initArchivablePlugin(): FilamentArchivablePlugin
    {
        return FilamentArchivablePlugin::make();
    }

    // General Settings | https://filamentphp.com/plugins/joaopaulolndev-general-settings#installation
    protected function initGeneralSettingsPlugin(): FilamentGeneralSettingsPlugin
    {
        return FilamentGeneralSettingsPlugin::make()
            ->setIcon('heroicon-o-cog')
            ->setNavigationGroup(__('Settings'))
            ->setTitle(__('General Settings'));
    }

    // image Dashboard Background plugin | https://filamentphp.com/plugins/swisnl-backgrounds#installation
    protected function initLoginBackgroundImagePlugin(): FilamentBackgroundsPlugin
    {
        return FilamentBackgroundsPlugin::make()
            ->showAttribution(false)
            ->imageProvider(
                MyImages::make()
                    ->directory('images/background-images'),
            );
    }

    // Spotlight plugin | https://filamentphp.com/plugins/pxlrbt-spotlight#installation
    protected function initSpotlightPlugin(): SpotlightPlugin
    {
        return SpotlightPlugin::make();
    }

    // Edit Profile Plugin | https://filamentphp.com/plugins/joaopaulolndev-edit-profile#installation
    protected function initEditProfilePlugin(): FilamentEditProfilePlugin
    {
        return FilamentEditProfilePlugin::make()
            ->slug('my-profile')
        //  ->setTitle('Meu perfil')
        //  ->setNavigationLabel('Meu perfil')
        //  ->setNavigationGroup('Minha Conta')
            ->shouldRegisterNavigation(false)
            ->setIcon('heroicon-o-user')
            ->setSort(10)
            ->shouldShowBrowserSessionsForm(true)
            ->shouldShowDeleteAccountForm(false)
            ->shouldShowAvatarForm(
                value: true,
                directory: 'avatars',
                rules: 'mimes:jpeg,png|max:1024',
            );
    }

    // Shield Plugin | https://filamentphp.com/plugins/bezhansalleh-shield#installation
    protected function initShieldPlugin(): FilamentShieldPlugin
    {
        return FilamentShieldPlugin::make();
    }

    // Environment Editor Plugin | https://filamentphp.com/plugins/pxlrbt-environment-editor#installation
    protected function initEnvironmentEditorPlugin(): FilamentEnvEditorPlugin
    {
        return FilamentEnvEditorPlugin::make()
            ->authorize(
                // fn () => auth()->user()->isAdmin()
            )
            ->navigationGroup(__('Settings'))
            ->navigationLabel(__('Environment Editor'))
            ->navigationIcon('heroicon-o-cog-8-tooth')
            ->navigationSort(1)
            ->slug('env-editor');
    }

    // Environment Indicator Plugin | https://filamentphp.com/plugins/pxlrbt-environment-indicator#installation
    protected function initEnvironmentIndicatorPlugin(): \pxlrbt\FilamentEnvironmentIndicator\EnvironmentIndicatorPlugin
    {
        return \pxlrbt\FilamentEnvironmentIndicator\EnvironmentIndicatorPlugin::make()
            ->showBadge(true)
            ->showBorder(true);
    }
    // Theme Management Plugin | https://filamentphp.com/plugins/hasnayeen-themes#installation
    protected function initThemeManagementPlugin(): \Hasnayeen\Themes\ThemesPlugin
    {
        return \Hasnayeen\Themes\ThemesPlugin::make();
    }
}
