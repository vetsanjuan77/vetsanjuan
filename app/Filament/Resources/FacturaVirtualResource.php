<?php

namespace App\Filament\Resources;

use App\Models\Factura;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class FacturaVirtualResource extends Resource
{
    protected static ?string $model = Factura::class;

    protected static ?string $navigationIcon = 'heroicon-o-cloud';
    protected static ?string $navigationGroup = 'Documentos';
    protected static ?string $navigationLabel = 'Facturas Virtuales';
    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form->schema([
            // Solo lectura: mostramos información principal si se abre el registro
            Forms\Components\TextInput::make('numero_factura')->disabled(),
            Forms\Components\TextInput::make('tipo_venta')->disabled(),
            Forms\Components\TextInput::make('estado')->disabled(),
            Forms\Components\TextInput::make('total')->disabled(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('numero_factura')->label('Factura')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('cliente.nombre')->label('Cliente')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('sucursal.nombre')->label('Sucursal')->sortable(),
                Tables\Columns\TextColumn::make('estado')->badge()->sortable(),
                Tables\Columns\TextColumn::make('total')->money('COP', true)->sortable(),
                Tables\Columns\TextColumn::make('fecha_emision')->dateTime()->label('Emitida')->sortable(),
                Tables\Columns\TextColumn::make('tipo_venta')->label('Tipo')->badge()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('estado')
                    ->options([
                        'pendiente' => 'Pendiente',
                        'pagada' => 'Pagada',
                        'cancelada' => 'Cancelada',
                        'vencida' => 'Vencida',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => FacturaVirtualResource\Pages\ListFacturasVirtuales::route('/'),
            'view' => FacturaVirtualResource\Pages\ViewFacturaVirtual::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('tipo_venta', 'virtual');
    }
}