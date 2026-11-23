<?php

namespace App\Filament\Sucursal\Resources\CajaResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\MovimientoCaja;
use Filament\Support\Enums\FontWeight;

class MovimientosRelationManager extends RelationManager
{
    protected static string $relationship = 'movimientos';

    protected static ?string $title = 'Movimientos de Caja';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Movimiento')
                    ->schema([
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
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Detalles Financieros')
                    ->schema([
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
                            ])
                            ->required()
                            ->native(false),

                        Forms\Components\DateTimePicker::make('fecha_movimiento')
                            ->label('Fecha del Movimiento')
                            ->required()
                            ->default(now()),

                        Forms\Components\TextInput::make('numero_documento')
                            ->label('Número de Documento')
                            ->maxLength(255),
                    ])
                    ->columns(2),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('concepto')
            ->columns([
                Tables\Columns\TextColumn::make('fecha_movimiento')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i')
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
                    ->badge()
                    ->colors([
                        'primary' => 'efectivo',
                        'success' => 'tarjeta',
                        'warning' => 'transferencia',
                        'info' => 'cheque',
                    ]),

                Tables\Columns\TextColumn::make('numero_documento')
                    ->label('Documento')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('tipo')
                    ->label('Tipo')
                    ->options([
                        'ingreso' => 'Ingreso',
                        'egreso' => 'Egreso',
                    ]),

                Tables\Filters\SelectFilter::make('metodo_pago')
                    ->label('Método de Pago')
                    ->options([
                        'efectivo' => 'Efectivo',
                        'tarjeta' => 'Tarjeta',
                        'transferencia' => 'Transferencia',
                        'cheque' => 'Cheque',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Nuevo Movimiento'),
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
}