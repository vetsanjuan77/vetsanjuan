<?php

namespace App\Filament\Sucursal\Resources;

use App\Filament\Sucursal\Resources\CompraResource\Pages;
use App\Filament\Sucursal\Resources\CompraResource\RelationManagers;
use App\Models\Compra;
use App\Models\Proveedor;
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
use Illuminate\Support\Facades\Auth;

class CompraResource extends Resource
{
    protected static ?string $model = Compra::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationGroup = 'Compras y Proveedores';
    protected static ?string $navigationLabel = 'Compras';
    protected static ?string $modelLabel = 'Compra';
    protected static ?string $pluralModelLabel = 'Compras';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información de la Compra')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('numero_compra')
                                    ->label('Número de Compra')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true)
                                    ->default(fn () => 'COM-' . date('Y') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT)),
                                Forms\Components\Select::make('proveedor_id')
                                    ->label('Proveedor')
                                    ->relationship('proveedor', 'nombre')
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('nombre')
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('nit')
                                            ->required()
                                            ->maxLength(20),
                                        Forms\Components\TextInput::make('telefono')
                                            ->tel()
                                            ->maxLength(20),
                                        Forms\Components\TextInput::make('email')
                                            ->email()
                                            ->maxLength(255),
                                    ]),
                                Forms\Components\Select::make('sucursal_id')
                                    ->label('Sucursal')
                                    ->relationship('sucursal', 'nombre')
                                    ->required()
                                    ->default(fn () => Auth::user()->sucursal_id ?? null),
                                Forms\Components\Select::make('user_id')
                                    ->label('Usuario Responsable')
                                    ->relationship('user', 'name')
                                    ->required()
                                    ->default(fn () => Auth::id()),
                            ]),
                    ]),
                
                Forms\Components\Section::make('Fechas')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\DateTimePicker::make('fecha_compra')
                                    ->label('Fecha de Compra')
                                    ->required()
                                    ->default(now()),
                                Forms\Components\DateTimePicker::make('fecha_recepcion')
                                    ->label('Fecha de Recepción'),
                            ]),
                    ]),
                
                Forms\Components\Section::make('Información Financiera')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('subtotal')
                                    ->label('Subtotal')
                                    ->required()
                                    ->numeric()
                                    ->prefix('$')
                                    ->step(0.01),
                                Forms\Components\TextInput::make('impuestos')
                                    ->label('Impuestos')
                                    ->required()
                                    ->numeric()
                                    ->prefix('$')
                                    ->default(0.00)
                                    ->step(0.01),
                                Forms\Components\TextInput::make('descuento')
                                    ->label('Descuento')
                                    ->required()
                                    ->numeric()
                                    ->prefix('$')
                                    ->default(0.00)
                                    ->step(0.01),
                            ]),
                        Forms\Components\TextInput::make('total')
                            ->label('Total')
                            ->required()
                            ->numeric()
                            ->prefix('$')
                            ->step(0.01),
                    ]),
                
                Forms\Components\Section::make('Estado y Documentos')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('estado')
                                    ->label('Estado')
                                    ->required()
                                    ->options([
                                        'pendiente' => 'Pendiente',
                                        'recibida' => 'Recibida',
                                        'parcial' => 'Parcial',
                                        'cancelada' => 'Cancelada',
                                    ])
                                    ->default('pendiente'),
                                Forms\Components\TextInput::make('numero_factura_proveedor')
                                    ->label('Número de Factura del Proveedor')
                                    ->maxLength(255),
                            ]),
                        Forms\Components\Textarea::make('observaciones')
                            ->label('Observaciones')
                            ->columnSpanFull()
                            ->rows(3),
                        Forms\Components\Textarea::make('documentos_soporte')
                            ->label('Documentos de Soporte')
                            ->columnSpanFull()
                            ->rows(3),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('numero_compra')
                    ->label('Número de Compra')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold),
                Tables\Columns\TextColumn::make('proveedor.nombre')
                    ->label('Proveedor')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sucursal.nombre')
                    ->label('Sucursal')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Usuario')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('fecha_compra')
                    ->label('Fecha de Compra')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('fecha_recepcion')
                    ->label('Fecha de Recepción')
                    ->date('d/m/Y')
                    ->sortable()
                    ->placeholder('Pendiente'),
                Tables\Columns\TextColumn::make('total')
                    ->label('Total')
                    ->money('COP')
                    ->sortable()
                    ->weight(FontWeight::Bold),
                Tables\Columns\BadgeColumn::make('estado')
                    ->label('Estado')
                    ->colors([
                        'warning' => 'pendiente',
                        'success' => 'recibida',
                        'info' => 'parcial',
                        'danger' => 'cancelada',
                    ])
                    ->icons([
                        'heroicon-o-clock' => 'pendiente',
                        'heroicon-o-check-circle' => 'recibida',
                        'heroicon-o-exclamation-triangle' => 'parcial',
                        'heroicon-o-x-circle' => 'cancelada',
                    ]),
                Tables\Columns\TextColumn::make('numero_factura_proveedor')
                    ->label('Factura Proveedor')
                    ->searchable()
                    ->placeholder('Sin factura')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('proveedor_id')
                    ->label('Proveedor')
                    ->relationship('proveedor', 'nombre')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('estado')
                    ->label('Estado')
                    ->options([
                        'pendiente' => 'Pendiente',
                        'recibida' => 'Recibida',
                        'parcial' => 'Parcial',
                        'cancelada' => 'Cancelada',
                    ]),
                Tables\Filters\Filter::make('fecha_compra')
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
                                fn (Builder $query, $date): Builder => $query->whereDate('fecha_compra', '>=', $date),
                            )
                            ->when(
                                $data['hasta'],
                                fn (Builder $query, $date): Builder => $query->whereDate('fecha_compra', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('marcar_recibida')
                    ->label('Marcar como Recibida')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Compra $record): bool => $record->estado === 'pendiente')
                    ->action(function (Compra $record): void {
                        $record->update([
                            'estado' => 'recibida',
                            'fecha_recepcion' => now(),
                        ]);
                    })
                    ->requiresConfirmation(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('fecha_compra', 'desc');
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
            'index' => Pages\ListCompras::route('/'),
            'create' => Pages\CreateCompra::route('/create'),
            'view' => Pages\ViewCompra::route('/{record}'),
            'edit' => Pages\EditCompra::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('sucursal_id', auth()->user()->sucursal_id);
    }
}
