<?php

namespace App\Filament\Facturador\Resources;

use App\Filament\Facturador\Resources\MovimientoCajaResource\Pages;
use App\Models\MovimientoCaja;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Support\Enums\FontWeight;

class MovimientoCajaResource extends Resource
{
    protected static ?string $model = MovimientoCaja::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationLabel = 'Movimientos de Caja';
    protected static ?string $modelLabel = 'Movimiento de Caja';
    protected static ?string $pluralModelLabel = 'Movimientos de Caja';
    protected static ?string $navigationGroup = 'Caja y Finanzas';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Movimiento')
                    ->schema([
                        Forms\Components\Select::make('caja_id')
                            ->label('Caja')
                            ->relationship('caja', 'nombre')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->sucursal->nombre . ' - ' . $record->nombre)
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('tipo')
                            ->label('Tipo de Movimiento')
                            ->options([
                                'ingreso' => 'Ingreso',
                                'egreso' => 'Egreso',
                            ])
                            ->required()
                            ->native(false),
                        Forms\Components\TextInput::make('concepto')
                            ->label('Concepto')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('descripcion')
                            ->label('Descripción')
                            ->rows(3)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('valor')
                            ->label('Valor')
                            ->required()
                            ->numeric()
                            ->prefix('$')
                            ->step(0.01),
                        Forms\Components\Select::make('metodo_pago')
                            ->label('Método de Pago')
                            ->options([
                                'efectivo' => 'Efectivo',
                                'tarjeta' => 'Tarjeta',
                                'transferencia' => 'Transferencia',
                                'cheque' => 'Cheque',
                            ]),
                        Forms\Components\TextInput::make('numero_documento')
                            ->label('Documento')
                            ->maxLength(255),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('fecha_movimiento')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('caja.sucursal.nombre')
                    ->label('Caja')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('tipo')
                    ->label('Tipo')
                    ->colors([
                        'success' => 'ingreso',
                        'danger' => 'egreso',
                    ])
                    ->icons([
                        'heroicon-o-arrow-up' => 'ingreso',
                        'heroicon-o-arrow-down' => 'egreso',
                    ]),
                Tables\Columns\TextColumn::make('concepto')
                    ->label('Concepto')
                    ->searchable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('valor')
                    ->label('Valor')
                    ->money('COP')
                    ->weight(FontWeight::Bold)
                    ->color(fn (MovimientoCaja $record): string => $record->tipo === 'ingreso' ? 'success' : 'danger')
                    ->sortable(),
                Tables\Columns\TextColumn::make('metodo_pago')
                    ->label('Método de Pago')
                    ->badge(),
                Tables\Columns\TextColumn::make('numero_documento')
                    ->label('Documento')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // filtros opcionales
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('fecha_movimiento', 'desc');
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
            'index' => Pages\ListMovimientoCajas::route('/'),
            'create' => Pages\CreateMovimientoCaja::route('/create'),
            'view' => Pages\ViewMovimientoCaja::route('/{record}'),
            'edit' => Pages\EditMovimientoCaja::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['caja.sucursal'])
            ->whereHas('caja', function (Builder $query) {
                $query->where('sucursal_id', auth()->user()->sucursal_id);
            });
    }
}