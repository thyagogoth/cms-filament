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
            'all'       => Tab::make(__('All')),
            'published' => Tab::make(__('Published'))->modifyQueryUsing(function ($query) {
                return $query->where('is_published', true);
            }),
            'draft' => Tab::make(__('Draft'))->modifyQueryUsing(function ($query) {
                return $query->where('is_published', false);
            }),
            'featured' => Tab::make(__('Featured'))->modifyQueryUsing(function ($query) {
                return $query->where('is_featured', true);
            }),
        ];
    }
}
