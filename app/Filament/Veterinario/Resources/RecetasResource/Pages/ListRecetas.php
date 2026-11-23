<?php

namespace App\Filament\Veterinario\Resources\RecetasResource\Pages;

use App\Filament\Veterinario\Resources\RecetasResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListRecetas extends ListRecords
{
    protected static string $resource = RecetasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'todas' => Tab::make('Todas'),
            'activas' => Tab::make('Activas')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('estado', 'activa')),
            'dispensadas' => Tab::make('Dispensadas')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('estado', 'dispensada')),
            'vencidas' => Tab::make('Vencidas')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('estado', 'vencida')
                    ->orWhere('fecha_vencimiento', '<', now())),
        ];
    }
}