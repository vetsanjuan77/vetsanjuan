<?php

namespace App\Filament\Sucursal\Resources;

use App\Filament\Sucursal\Resources\CajaResource\Pages;
use App\Filament\Sucursal\Resources\CajaResource\RelationManagers;
use App\Models\Caja;
use App\Models\Sucursal;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Support\Enums\FontWeight;

class CajaResource extends Resource
{
    protected static ?string $model = Caja::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    protected static ?string $navigationLabel = 'Cajas';

    protected static ?string $modelLabel = 'Caja';

    protected static ?string $pluralModelLabel = 'Cajas';

    protected static ?string $navigationGroup = 'Caja y Finanzas';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información de la Caja')
                    ->schema([
                        Forms\Components\TextInput::make('nombre')
                            ->label('Nombre de la Caja')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Select::make('sucursal_id')
                            ->label('Sucursal')
                            ->relationship('sucursal', 'nombre')
                            ->required()
                            ->searchable()
                            ->preload(),

                        Forms\Components\Select::make('user_apertura_id')
                            ->label('Usuario de Apertura')
                            ->relationship('usuarioApertura', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),

                        Forms\Components\Select::make('estado')
                            ->label('Estado')
                            ->options([
                                'abierta' => 'Abierta',
                                'cerrada' => 'Cerrada',
                            ])
                            ->required()
                            ->native(false)
                            ->default('abierta'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Información Financiera')
                    ->schema([
                        Forms\Components\TextInput::make('saldo_inicial')
                            ->label('Saldo Inicial')
                            ->required()
                            ->numeric()
                            ->prefix('$')
                            ->step(0.01)
                            ->default(0),

                        Forms\Components\TextInput::make('saldo_actual')
                            ->label('Saldo Actual')
                            ->numeric()
                            ->prefix('$')
                            ->step(0.01)
                            ->disabled()
                            ->dehydrated(false),

                        Forms\Components\DateTimePicker::make('fecha_apertura')
                            ->label('Fecha de Apertura')
                            ->required()
                            ->default(now()),

                        Forms\Components\DateTimePicker::make('fecha_cierre')
                            ->label('Fecha de Cierre')
                            ->visible(fn (Forms\Get $get): bool => $get('estado') === 'cerrada'),

                        Forms\Components\Select::make('user_cierre_id')
                            ->label('Usuario de Cierre')
                            ->relationship('usuarioCierre', 'name')
                            ->searchable()
                            ->preload()
                            ->visible(fn (Forms\Get $get): bool => $get('estado') === 'cerrada'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Observaciones')
                    ->schema([
                        Forms\Components\Textarea::make('observaciones')
                            ->label('Observaciones')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('sucursal.nombre')
                    ->label('Sucursal')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('estado')
                    ->label('Estado')
                    ->colors([
                        'success' => 'abierta',
                        'danger' => 'cerrada',
                    ])
                    ->icons([
                        'heroicon-o-lock-open' => 'abierta',
                        'heroicon-o-lock-closed' => 'cerrada',
                    ]),

                Tables\Columns\TextColumn::make('saldo_inicial')
                    ->label('Saldo Inicial')
                    ->money('COP')
                    ->sortable(),

                Tables\Columns\TextColumn::make('saldo_actual')
                    ->label('Saldo Actual')
                    ->money('COP')
                    ->weight(FontWeight::Bold)
                    ->color('success')
                    ->sortable(),

                Tables\Columns\TextColumn::make('usuarioApertura.name')
                    ->label('Usuario Apertura')
                    ->searchable()
                    ->sortable(false)
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('fecha_apertura')
                    ->label('Fecha Apertura')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('fecha_cierre')
                    ->label('Fecha Cierre')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->placeholder('Aún abierta')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('estado')
                    ->label('Estado')
                    ->options([
                        'abierta' => 'Abierta',
                        'cerrada' => 'Cerrada',
                    ]),

                Tables\Filters\SelectFilter::make('sucursal_id')
                    ->label('Sucursal')
                    ->options(function () {
                        return \App\Models\Sucursal::pluck('nombre', 'id');
                    }),

                Tables\Filters\Filter::make('fecha_apertura')
                    ->form([
                        Forms\Components\DatePicker::make('desde')
                            ->label('Desde'),
                        Forms\Components\DatePicker::make('hasta')
                            ->label('Hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['desde'],
                                fn (Builder $query, $date): Builder => $query->whereDate('fecha_apertura', '>=', $date),
                            )
                            ->when(
                                $data['hasta'],
                                fn (Builder $query, $date): Builder => $query->whereDate('fecha_apertura', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('cerrar_caja')
                    ->label('Cerrar Caja')
                    ->icon('heroicon-o-lock-closed')
                    ->color('danger')
                    ->visible(fn (Caja $record): bool => $record->isAbierta())
                    ->requiresConfirmation()
                    ->form([
                        Forms\Components\TextInput::make('saldo_fisico')
                            ->label('Saldo Físico Contado')
                            ->required()
                            ->numeric()
                            ->prefix('$')
                            ->step(0.01),
                        Forms\Components\Textarea::make('observaciones_cierre')
                            ->label('Observaciones del Cierre')
                            ->rows(3),
                    ])
                    ->action(function (Caja $record, array $data): void {
                        $saldoCalculado = $record->calcularSaldoActual();
                        $diferencia = $data['saldo_fisico'] - $saldoCalculado;
                        
                        $record->update([
                            'estado' => 'cerrada',
                            'fecha_cierre' => now(),
                            'user_cierre_id' => auth()->id(),
                            'saldo_actual' => $saldoCalculado,
                            'observaciones' => $record->observaciones . "\n\nCierre: " . ($data['observaciones_cierre'] ?? '') . 
                                             "\nSaldo calculado: $" . number_format($saldoCalculado, 2) . 
                                             "\nSaldo físico: $" . number_format($data['saldo_fisico'], 2) . 
                                             "\nDiferencia: $" . number_format($diferencia, 2),
                        ]);
                    }),

                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn (Caja $record): bool => $record->isCerrada()),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('fecha_apertura', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\MovimientosRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCajas::route('/'),
            'create' => Pages\CreateCaja::route('/create'),
            'view' => Pages\ViewCaja::route('/{record}'),
            'edit' => Pages\EditCaja::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with('sucursal')
            ->where('sucursal_id', auth()->user()->sucursal_id);
    }
}
