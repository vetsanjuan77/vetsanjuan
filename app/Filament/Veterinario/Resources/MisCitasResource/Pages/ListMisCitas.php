<?php

namespace App\Filament\Veterinario\Resources\MisCitasResource\Pages;

use App\Filament\Veterinario\Resources\MisCitasResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListMisCitas extends ListRecords
{
    protected static string $resource = MisCitasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'todas' => Tab::make('Todas mis Citas'),
            'hoy' => Tab::make('Hoy')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereDate('fecha', today())),
            'programadas' => Tab::make('Programadas')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('estado', 'programada')),
            'confirmadas' => Tab::make('Confirmadas')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('estado', 'confirmada')),
            'en_proceso' => Tab::make('En Proceso')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('estado', 'en_proceso')),
            'completadas' => Tab::make('Completadas')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('estado', 'completada')),
        ];
    }
}