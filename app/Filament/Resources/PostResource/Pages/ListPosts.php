<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Filament\Resources\PostResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;

class ListPosts extends ListRecords
{
    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label(__('Create Post'))
                ->icon('heroicon-o-plus-circle'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all'       => Tab::make(__('All'))
//                ->iconPosition(\Filament\Support\Enums\IconPosition::After)
                ->icon('heroicon-o-list-bullet')
                ->badge(\App\Models\Post::query()->count())
                ->badgeColor('info'),

            'published' => Tab::make(__('Published'))
                    ->modifyQueryUsing(function ($query) {
                        return $query->where('is_published', true);
                    })
                    ->iconPosition(\Filament\Support\Enums\IconPosition::After)
                    ->icon('heroicon-o-check-circle'),

            'draft' => Tab::make(__('Draft'))
                    ->modifyQueryUsing(function ($query) {
                        return $query->where('is_published', false);
                    })
                    ->iconPosition(\Filament\Support\Enums\IconPosition::After)
                    ->icon('heroicon-o-document-text'),

            'featured' => Tab::make(__('Featured'))
                    ->modifyQueryUsing(function ($query) {
                        return $query->where('is_featured', true);
                    })
                    ->iconPosition(\Filament\Support\Enums\IconPosition::After)
                    ->icon('heroicon-o-star'),
        ];
    }
}
