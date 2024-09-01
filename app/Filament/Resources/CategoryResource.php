<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Models\Category;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\{Forms, Tables};

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getModelLabel(): string
    {
        return __('Category');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set, ?string $state) {
                        if (blank($get('slug'))) {
                            $set('slug', str($state)->slug());
                        }
                    }),
                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->minLength(2)
                    ->maxLength(255)->unique(column: 'slug', ignoreRecord: true)
                    ->live(debounce: 600)
                    ->afterStateUpdated(function (?string $state, Forms\Components\TextInput $component) {
                        $component->state(str($state)->slug());
                    }),
                Forms\Components\RichEditor::make('content')
                    ->columnSpanFull(),

                //                Forms\Components\Select::make('parent_id')
                //                    ->options(fn () => \App\Models\Category::query()->pluck('name', 'id'))
                //                    ->nullable()
                //                    ->columnSpan(2),

                Forms\Components\ColorPicker::make('bg_color')
                    ->label('Background Color'),
                Forms\Components\ColorPicker::make('text_color')
                    ->label('Text Color'),
                Forms\Components\TextInput::make('meta_description'),
                Forms\Components\SpatieMediaLibraryFileUpload::make('cover')
                    ->image()
                    ->imageEditor()
                    ->multiple(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\SpatieMediaLibraryImageColumn::make('thumbnail')
                    ->stacked(),

                Tables\Columns\TextColumn::make('name')
                    ->sortable()
                    ->searchable(),

                //                Tables\Columns\TextColumn::make('parent_id')
                //                    ->searchable(),

                Tables\Columns\ColorColumn::make('bg_color'),
                Tables\Columns\ColorColumn::make('text_color'),

                //                Tables\Columns\TextColumn::make('archived_at')
                //                    ->dateTime()
                //                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                //                Tables\Columns\TextColumn::make('updated_at')
                //                    ->dateTime()
                //                    ->sortable()
                //                    ->toggleable(isToggledHiddenByDefault: true),

            ])
            ->filters([
            ])
            ->filtersTriggerAction(
                fn (\Filament\Tables\Actions\Action $action) => $action
                ->icon('heroicon-s-adjustments-vertical') // Altere o ícone aqui
                ->label('Filtrar registros') // Texto opcional
                ->slideOver()
            )
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
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
            'index'  => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit'   => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
