<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\{Pages};
use App\Models\User;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\{Forms, Tables};
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function getModelLabel(): string
    {
        return __('User');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Split::make([
                    Forms\Components\Section::make([
                        \Schmeits\FilamentCharacterCounter\Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(120)
                            ->characterLimit(120),
                        //                ->showInsideControl(true),

                        Forms\Components\Split::make([

                            \Schmeits\FilamentCharacterCounter\Forms\Components\TextInput::make('email')
                                ->email()
                                ->required()
                                ->maxLength(120)
                                ->characterLimit(120),

                            Forms\Components\DateTimePicker::make('email_verified_at'),

                        ]),

                        Forms\Components\Split::make([
                            Forms\Components\TextInput::make('password')
                                ->password()
                                ->confirmed()
                                ->required()
                                ->maxLength(255),

                            Forms\Components\TextInput::make('password_confirmation')
                                ->password()
                                ->required()
                                ->maxLength(255),

            //                Forms\Components\TextInput::make('custom_fields'),
                        ]),
                    ]),

                    Forms\Components\Section::make([
                        Forms\Components\FileUpload::make('avatar_url'),
                        Forms\Components\Select::make('roles')
                            ->relationship('roles', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->required(),
                    ])
                        ->grow(false),
                ])
                    ->from('md'),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('avatar_url')
//                    ->circular()
                    ->grow(false),

                Tables\Columns\TextColumn::make('name')
                    ->searchable(),

                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
//                    ->icon('heroicon-m-envelope'),

                Tables\Columns\TextColumn::make('roles.name')
                    ->searchable(),
//                    ->icon('heroicon-m-user-group'),

                Tables\Columns\TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make(__('Export'))
                        ->icon('heroicon-m-arrow-down-tray')
                        ->openUrlInNewTab()
                        ->deselectRecordsAfterCompletion()
                        ->action(function (\Illuminate\Support\Collection $records) {
                            return response()->streamDownload(function () use ($records) {
                                echo \Barryvdh\DomPDF\Facade\Pdf::loadHTML(
                                    \Illuminate\Support\Facades\Blade::render('pdf', ['records' => $records])
                                )->stream();
                            }, 'Report_Users_' . uniqId() . '.pdf');
                        }),
                    ExportBulkAction::make('export')
                        ->label('Exportar Excel')
                        ->deselectRecordsAfterCompletion(),
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
            'index'  => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit'   => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
