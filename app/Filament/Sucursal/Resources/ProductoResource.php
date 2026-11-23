<?php

namespace App\Filament\Sucursal\Resources;

use App\Filament\Sucursal\Resources\ProductoResource\Pages;
use App\Filament\Sucursal\Resources\ProductoResource\RelationManagers;
use App\Models\Producto;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;

class ProductoResource extends Resource
{
    protected static ?string $model = Producto::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?string $navigationGroup = 'Inventario';
    protected static ?string $navigationLabel = 'Productos';
    protected static ?string $modelLabel = 'Producto';
    protected static ?string $pluralModelLabel = 'Productos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del producto')
                    ->schema([
                        Forms\Components\TextInput::make('nombre')
                            ->disabled()
                            ->dehydrated(false),
                        Forms\Components\Textarea::make('description')
                            ->label('Descripción')
                            ->disabled()
                            ->dehydrated(false)
                            ->rows(2),
                        Forms\Components\TextInput::make('categoria')
                            ->disabled()
                            ->dehydrated(false),
                        Forms\Components\TextInput::make('marca.nombre')
                            ->label('Marca')
                            ->disabled()
                            ->dehydrated(false),
                    ])
                    ->columns(2)
                    ->description('Información del producto (solo lectura). Los productos se crean desde el panel de administración.'),

                Forms\Components\Section::make('Gestión de Stock por Presentación')
                    ->schema([
                        Forms\Components\Repeater::make('presentaciones')
                            ->relationship('presentaciones')
                            ->schema([
                                Forms\Components\Hidden::make('id')
                                    ->dehydrated(true),
                                Forms\Components\TextInput::make('nombre')
                                    ->label('Presentación')
                                    ->disabled()
                                    ->dehydrated(false),
                                Forms\Components\TextInput::make('precio_unitario')
                                    ->label('Precio Base')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->prefix('$'),
                                Forms\Components\TextInput::make('stock_sucursal')
                                    ->label('Stock en esta Sucursal')
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0)
                                    ->afterStateHydrated(function (Forms\Components\TextInput $component, $state, $record) {
                                        if ($record) {
                                            $sucursalId = auth()->user()->sucursal_id;
                                            $stock = $record->sucursales()->where('sucursal_id', $sucursalId)->first();
                                            $component->state($stock ? $stock->pivot->stock : 0);
                                        }
                                    }),
                                Forms\Components\TextInput::make('precio_venta')
                                    ->label('Precio de Venta')
                                    ->numeric()
                                    ->prefix('$')
                                    ->step(0.01)
                                    ->afterStateHydrated(function (Forms\Components\TextInput $component, $state, $record) {
                                        if ($record) {
                                            $sucursalId = auth()->user()->sucursal_id;
                                            $stock = $record->sucursales()->where('sucursal_id', $sucursalId)->first();
                                            $component->state($stock ? $stock->pivot->precio_venta : $record->precio_unitario);
                                        }
                                    }),
                            ])
                            ->columns(4)
                            ->addable(false)
                            ->deletable(false)
                            ->reorderable(false)
                            ->label('Presentaciones del Producto'),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('imagen')
                    ->circular()
                    ->size(40),
                Tables\Columns\TextColumn::make('nombre')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('marca.nombre')
                    ->label('Marca')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('categoria')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'medicamentos' => 'danger',
                        'alimentos' => 'success',
                        'accesorios' => 'info',
                        'juguetes' => 'warning',
                        'higiene' => 'primary',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('especie')
                    ->badge(),
                Tables\Columns\TextColumn::make('precio')
                    ->money('COP')
                    ->sortable(),
                Tables\Columns\TextColumn::make('stock_actual')
                    ->label('Stock')
                    ->getStateUsing(function ($record) {
                        // Sumar stock desde presentaciones por sucursal (pivot: presentacion_sucursal)
                        $sucursalId = auth()->user()->sucursal_id;
                        if (!$sucursalId) {
                            return 0;
                        }
                        return DB::table('presentacion_sucursal')
                            ->join('presentaciones', 'presentacion_sucursal.presentacion_id', '=', 'presentaciones.id')
                            ->where('presentacion_sucursal.sucursal_id', $sucursalId)
                            ->where('presentaciones.producto_id', $record->id)
                            ->sum('presentacion_sucursal.stock');
                    })
                    ->color(fn ($state) => $state <= 5 ? 'danger' : ($state <= 10 ? 'warning' : 'success'))
                    ->sortable(false),
                Tables\Columns\IconColumn::make('destacado')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('categoria')
                    ->options([
                        'medicamentos' => 'Medicamentos',
                        'alimentos' => 'Alimentos',
                        'accesorios' => 'Accesorios',
                        'juguetes' => 'Juguetes',
                        'higiene' => 'Higiene',
                        'otros' => 'Otros',
                    ]),
                Tables\Filters\SelectFilter::make('especie')
                    ->options([
                        'perro' => 'Perro',
                        'gato' => 'Gato',
                        'ave' => 'Ave',
                        'pez' => 'Pez',
                        'reptil' => 'Reptil',
                        'roedor' => 'Roedor',
                        'general' => 'General',
                    ]),
                Tables\Filters\TernaryFilter::make('destacado')
                    ->label('Destacado')
                    ->boolean()
                    ->trueLabel('Destacados')
                    ->falseLabel('No destacados')
                    ->native(false),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('ajustar_stock')
                    ->label('Ajustar Stock')
                    ->icon('heroicon-o-adjustments-horizontal')
                    ->color('warning')
                    ->form([
                        Forms\Components\Select::make('presentacion_id')
                            ->label('Presentación')
                            ->options(function ($record) {
                                return $record->presentaciones()->orderBy('nombre')->pluck('nombre', 'id');
                            })
                            ->required(),
                        Forms\Components\TextInput::make('nuevo_stock')
                            ->label('Nuevo Stock')
                            ->required()
                            ->numeric()
                            ->minValue(0),
                        Forms\Components\Textarea::make('motivo')
                            ->label('Motivo del Ajuste')
                            ->required(),
                    ])
                    ->action(function (array $data, $record): void {
                        $sucursalId = auth()->user()->sucursal_id;
                        if (! $sucursalId) {
                            \Filament\Notifications\Notification::make()
                                ->title('Error de Sucursal')
                                ->body('No se encontró la sucursal del usuario para ajustar stock.')
                                ->danger()
                                ->send();
                            return;
                        }

                        \Illuminate\Support\Facades\DB::transaction(function () use ($record, $sucursalId, $data) {
                            $presentacionId = (int) $data['presentacion_id'];
                            $presentacion = $record->presentaciones()->find($presentacionId);
                            if (! $presentacion) {
                                \Filament\Notifications\Notification::make()
                                    ->title('Presentación no encontrada')
                                    ->body('No se pudo localizar la presentación seleccionada.')
                                    ->danger()
                                    ->send();
                                return;
                            }

                            // Leer valor previo directamente de la tabla pivot
                            $pivotAntes = \Illuminate\Support\Facades\DB::table('presentacion_sucursal')
                                ->where('presentacion_id', $presentacionId)
                                ->where('sucursal_id', $sucursalId)
                                ->first();
                            \Illuminate\Support\Facades\Log::info('Ajuste manual de stock (tabla acción)', [
                                'producto_id' => $record->id,
                                'presentacion_id' => $presentacionId,
                                'sucursal_id' => $sucursalId,
                                'antes' => $pivotAntes ? (int) $pivotAntes->stock : null,
                                'nuevo' => (int) $data['nuevo_stock'],
                                'motivo' => $data['motivo'] ?? null,
                            ]);

                            // Actualizar/insertar directamente en la pivot para garantizar persistencia
                            \Illuminate\Support\Facades\DB::table('presentacion_sucursal')
                                ->updateOrInsert(
                                    [
                                        'presentacion_id' => $presentacionId,
                                        'sucursal_id' => $sucursalId,
                                    ],
                                    [
                                        'stock' => (int) $data['nuevo_stock'],
                                        'updated_at' => now(),
                                    ]
                                );

                            // Releer y verificar
                            $pivotDespues = \Illuminate\Support\Facades\DB::table('presentacion_sucursal')
                                ->where('presentacion_id', $presentacionId)
                                ->where('sucursal_id', $sucursalId)
                                ->first();
                            if (! $pivotDespues || (int) $pivotDespues->stock !== (int) $data['nuevo_stock']) {
                                \Illuminate\Support\Facades\Log::error('Fallo al ajustar stock en acción de tabla', [
                                    'producto_id' => $record->id,
                                    'presentacion_id' => $presentacionId,
                                    'sucursal_id' => $sucursalId,
                                    'esperado' => (int) $data['nuevo_stock'],
                                    'obtenido' => $pivotDespues ? (int) $pivotDespues->stock : null,
                                ]);
                                throw new \RuntimeException('No se pudo confirmar el ajuste de stock en la base de datos.');
                            }

                            // Refrescar el registro y relaciones para que la tabla refleje el nuevo valor
                            $record->refresh();
                            $record->load(['presentaciones' => function ($q) use ($sucursalId) {
                                $q->with(['sucursales' => function ($qq) use ($sucursalId) {
                                    $qq->where('sucursal_id', $sucursalId);
                                }]);
                            }]);
                        });
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Stock Ajustado')
                            ->body('El stock fue actualizado y verificado correctamente.')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('nombre');
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
            'index' => Pages\ListProductos::route('/'),
            'view' => Pages\ViewProducto::route('/{record}'),
            'edit' => Pages\EditProducto::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('presentaciones.sucursales', function (Builder $query) {
                $query->where('sucursal_id', auth()->user()->sucursal_id);
            });
    }
}
