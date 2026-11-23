<?php

namespace App\Filament\Resources\VeterinariosResource\Pages;

use App\Filament\Resources\VeterinariosResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListVeterinarios extends ListRecords
{
    protected static string $resource = VeterinariosResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Nuevo Veterinario'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'todos' => Tab::make('Todos'),
            'activos' => Tab::make('Activos')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('estado', 'activo')),
            'inactivos' => Tab::make('Inactivos')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('estado', 'inactivo')),
            'suspendidos' => Tab::make('Suspendidos')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('estado', 'suspendido')),
        ];
    }
}