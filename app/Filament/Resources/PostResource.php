<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Models\Post;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\{Forms, Tables};
use Illuminate\Support\Facades\Auth;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-m-pencil';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->minLength(2)
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
                    ->grow(true)
                    ->required()
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('meta_description')
                    ->maxLength(255)
                    ->columnSpanFull(),

                //                Forms\Components\TextInput::make('user_id')
                //                    ->required()
                //                    ->numeric(),
                Forms\Components\Hidden::make('user_id')
                    ->dehydrateStateUsing(fn ($state) => Auth::id()),

                Forms\Components\Checkbox::make('is_published'),

                Forms\Components\Checkbox::make('is_featured'),

                Forms\Components\SpatieMediaLibraryFileUpload::make('cover')
                    ->image()
                    ->responsiveImages()
                    ->imageEditor(),

                Forms\Components\Select::make('categories')
                    ->multiple()
                    ->relationship('categories', 'name')
                    ->columnSpanFull(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\SpatieMediaLibraryImageColumn::make('')->stacked(),

                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\CheckboxColumn::make('is_featured'),
                Tables\Columns\CheckboxColumn::make('is_published'),

                //                Tables\Columns\TextColumn::make('created_at')
                //                    ->dateTime()
                //                    ->sortable()
                //                    ->toggleable(isToggledHiddenByDefault: true),
                //                Tables\Columns\TextColumn::make('updated_at')
                //                    ->dateTime()
                //                    ->sortable()
                //                    ->toggleable(isToggledHiddenByDefault: true),
                //                Tables\Columns\TextColumn::make('archived_at')
                //                    ->dateTime()
                //                    ->sortable(),
                //                Tables\Columns\TextColumn::make('deleted_at')
                //                    ->dateTime()
                //                    ->sortable()
                //                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //                Tables\Filters\TrashedFilter::make()
                //                    ->label('Show Trashed'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index'  => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit'   => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
