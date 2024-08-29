<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NavigationResource\{Pages};
use App\Models\Navigation;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\{Forms, Tables};
use Illuminate\Support\Facades\Route;

class NavigationResource extends Resource
{
    protected static ?string $model = Navigation::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Repeater::make('items')->schema([
                    Forms\Components\TextInput::make('title')->required(),
                    Forms\Components\Select::make('url')
                        ->searchable()
                        ->options(function () {
                            return collect(Route::getRoutes()->getRoutesByMethod()['GET'])->mapWithKeys(function ($route) {
                                return [$route->getName() => $route->uri()];
                            });
                        })
                        ->required()
                        ->placeholder('example-page'),
                    Forms\Components\Checkbox::make('external_link')->label('Open in new tab'),
                    Forms\Components\Select::make('show_for')->options([
                        'users'    => 'Users',
                        'everyone' => 'Everyone',
                        'public'   => 'Public',
                    ]),
                ]),
                Forms\Components\Repeater::make('items_sidebar')->schema([
                    Forms\Components\TextInput::make('title')->required(),
                    Forms\Components\Select::make('url')
                        ->searchable()
                        ->options(function () {
                            return collect(Route::getRoutes()->getRoutesByMethod()['GET'])->mapWithKeys(function ($route) {
                                return [$route->getName() => $route->uri()];
                            });
                        })
                        ->required()
                        ->placeholder('example-page'),
                    Forms\Components\Checkbox::make('external_link')->label('Open in new tab'),
                    Forms\Components\Select::make('show_for')->options([
                        'users'    => 'Users',
                        'everyone' => 'Everyone',
                        'public'   => 'Public',
                    ]),
                ]),
                Forms\Components\ColorPicker::make('bg_color')->label('Background Color'),
                Forms\Components\Checkbox::make('is_active'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ColorColumn::make('bg_color'),
                Tables\Columns\CheckboxColumn::make('is_active'),
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
            'index'  => Pages\ListNavigations::route('/'),
            'create' => Pages\CreateNavigation::route('/create'),
            'edit'   => Pages\EditNavigation::route('/{record}/edit'),
        ];
    }
}
