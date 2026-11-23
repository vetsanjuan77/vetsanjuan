<?php

namespace App\Filament\Veterinario\Resources\MisConsultasResource\Pages;

use App\Filament\Veterinario\Resources\MisConsultasResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListMisConsultas extends ListRecords
{
    protected static string $resource = MisConsultasResource::class;

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
            'hoy' => Tab::make('Hoy')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereDate('fecha_consulta', today())),
            'esta_semana' => Tab::make('Esta Semana')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereBetween('fecha_consulta', [
                    now()->startOfWeek(),
                    now()->endOfWeek()
                ])),
            'seguimiento' => Tab::make('Requieren Seguimiento')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('requiere_seguimiento', true)),
        ];
    }
}