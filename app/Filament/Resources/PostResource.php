<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Models\Post;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\{Forms, Tables};
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-m-pencil';

    protected static ?string $recordTitleAttribute = 'title';

    public function getTabs(): array
    {
        $tabs = [];

        $tabs[] = Tab::make('All Tasks')
            // Add badge to the tab
            ->badge(Task::count());
        // No need to modify the query as we want to show all tasks

        return $tabs;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('post')->tabs([
                    Tab::make('Content')->schema([

                        // load indicator
                        //                        Forms\Components\DatePicker::make('date')
                        //                            ->native(false)
                        //                            // ... more methods
                        //                            ->hint(new \Illuminate\Support\HtmlString(\Illuminate\Support\Facades\Blade::render('<x-filament::loading-indicator class="w-5 h-5" wire:loading wire:target="data.date" />')))
                        //                            ->live(),

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

                        Forms\Components\Select::make('categories')
                            ->multiple()
                            ->relationship('categories', 'name')
                            ->columnSpanFull(),

                        Forms\Components\Checkbox::make('is_published'),

                        Forms\Components\Checkbox::make('is_featured'),

                        Forms\Components\Hidden::make('user_id')
                            ->dehydrateStateUsing(fn ($state) => Auth::id()),
                    ]),

                    Tab::make('Meta')->schema([
                        Forms\Components\TextInput::make('meta_description')
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Forms\Components\SpatieMediaLibraryFileUpload::make('cover')
                            ->image()
                            ->responsiveImages()
                            ->imageEditor(),
                    ]),
                ]),
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\SpatieMediaLibraryImageColumn::make('thumbnail')->stacked(),

                Tables\Columns\TextColumn::make('title')
                    ->searchable(),

                Tables\Columns\TextColumn::make('categories.name')
                    ->searchable()
                    ->badge(),

                Tables\Columns\CheckboxColumn::make('is_featured'),

                Tables\Columns\CheckboxColumn::make('is_published'),

                Tables\Columns\TextColumn::make('created_at')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('is_featured')
                    ->label('Featured')
                    ->query(fn (Builder $query): Builder => $query->where('is_featured', true)),

                Tables\Filters\Filter::make('is_published')
                    ->label('Published')
                    ->query(fn (Builder $query): Builder => $query->where('is_published', true)),

                Tables\Filters\SelectFilter::make('categories')
                    ->multiple()
                    ->relationship('categories', 'name'),

                Tables\Filters\TrashedFilter::make(),
            ], Tables\Enums\FiltersLayout::Dropdown)
            ->filtersTriggerAction(
                fn (\Filament\Tables\Actions\Action $action) => $action
                ->icon('heroicon-s-adjustments-vertical') // Altere o ícone aqui
                ->label('Filtrar registros') // Texto opcional
                ->slideOver()
            )

//            ->actions([
//                Tables\Actions\EditAction::make(),
//                Tables\Actions\DeleteAction::make(),
//            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make()
                        ->hidden(fn ($record) => $record->trashed()),
                    Tables\Actions\DeleteAction::make(),
                    Tables\Actions\RestoreAction::make(),
                    //                    Tables\Actions\Action::make('Move to Stage')
                    //                        ->hidden(fn($record) => $record->trashed())
                    //                        ->icon('heroicon-m-pencil-square')
                    //                        ->form([
                    //
                    //                            Forms\Components\Textarea::make('notes')
                    //                                ->label('Notes')
                    //                        ])
                    //                        ->action(function (Customer $customer, array $data): void {
                    //                            $customer->pipeline_stage_id = $data['pipeline_stage_id'];
                    //                            $customer->save();
                    //
                    //                            $customer->pipelineStageLogs()->create([
                    //                                'pipeline_stage_id' => $data['pipeline_stage_id'],
                    //                                'notes' => $data['notes'],
                    //                                'user_id' => auth()->id()
                    //                            ]);
                    //
                    //                            Notification::make()
                    //                                ->title('Customer Pipeline Updated')
                    //                                ->success()
                    //                                ->send();
                    //                        }),
                    //                    Tables\Actions\Action::make('Add Task')
                    //                        ->icon('heroicon-s-clipboard-document')
                    //                        ->form([
                    //                            Forms\Components\RichEditor::make('description')
                    //                                ->required(),
                    //                            Forms\Components\Select::make('user_id')
                    //                                ->preload()
                    //                                ->searchable()
                    //                                ->relationship('employee', 'name'),
                    //                            Forms\Components\DatePicker::make('due_date')
                    //                                ->native(false),
                    //
                    //                        ])
                    //                        ->action(function (Customer $customer, array $data) {
                    //                            $customer->tasks()->create($data);
                    //
                    //                            Notification::make()
                    //                                ->title('Task created successfully')
                    //                                ->success()
                    //                                ->send();
                    //                        }),

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
            'index'  => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit'   => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
