<?php

namespace App\Filament\Facturador\Resources;

use App\Filament\Facturador\Resources\FacturaResource\Pages;
use App\Models\Factura;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class FacturaResource extends Resource
{
    protected static ?string $model = Factura::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Facturar';
    protected static ?string $navigationGroup = 'Ventas y Facturación';
    protected static ?int $navigationSort = 0;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Datos de la factura')
                    ->columns(3)
                    ->schema([
                        Forms\Components\Select::make('cliente_id')
                            ->label('Cliente')
                            ->searchable()
                            ->options(function () {
                                return \App\Models\Cliente::query()
                                    ->orderBy('nombre')
                                    ->get()
                                    ->mapWithKeys(fn ($c) => [$c->id => $c->nombre . (isset($c->apellido) ? ' ' . $c->apellido : '')])
                                    ->toArray();
                            })
                            ->required(),
                        Forms\Components\DateTimePicker::make('fecha_emision')
                            ->label('Fecha de emisión')
                            ->default(now())
                            ->required(),
                        Forms\Components\Select::make('metodo_pago')
                            ->label('Método de pago')
                            ->options([
                                'efectivo' => 'Efectivo',
                                'tarjeta' => 'Tarjeta',
                                'transferencia' => 'Transferencia',
                            ]),
                    ]),

                Forms\Components\Section::make('Productos')
                    ->schema([
                        Forms\Components\Repeater::make('detalles')
                            ->relationship('detalles')
                            ->default([])
                            ->columns(6)
                            ->schema([
                                Forms\Components\TextInput::make('codigo_barras')
                                    ->label('Código barras')
                                    ->placeholder('Escanee o escriba')
                                    ->reactive()
                                    ->dehydrated(false)
                                    ->columnSpan(2)
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        if (!$state) return;
                                        $presentacion = \App\Models\Presentacion::where('codigo_barras', $state)->first();
                                        if (!$presentacion) {
                                            return; // No coincide
                                        }
                                        $prevPresentacionId = $get('presentacion_id');
                                        $set('producto_id', $presentacion->producto_id);
                                        $set('presentacion_id', $presentacion->id);
                                        $sucursalId = auth()->user()->sucursal_id ?? null;
                                        $precio = (float) $presentacion->precio_unitario;
                                        if ($sucursalId) {
                                            $pivot = $presentacion->sucursales()->where('sucursal_id', $sucursalId)->first()?->pivot;
                                            if ($pivot && isset($pivot->precio_venta)) {
                                                $precio = (float) $pivot->precio_venta;
                                            }
                                        }
                                        $set('precio_unitario', $precio);
                                        $currentCantidad = (float) ($get('cantidad') ?? 0);
                                        $cantidad = ($prevPresentacionId === $presentacion->id && $currentCantidad > 0)
                                            ? $currentCantidad + 1
                                            : 1;
                                        $set('cantidad', $cantidad);
                                        $descuento = (float) ($get('descuento') ?? 0);
                                        $subtotalPreciso = round($cantidad * $precio, 4);
                                        $set('total', max(0, round($subtotalPreciso - $descuento, 2)));
                                    }),
                                Forms\Components\Select::make('producto_id')
                                    ->label('Producto')
                                    ->searchable()
                                    ->reactive()
                                    ->getSearchResultsUsing(function (string $search) {
                                        return \App\Models\Producto::query()
                                            ->where('nombre', 'like', "%{$search}%")
                                            ->limit(50)
                                            ->pluck('nombre', 'id')
                                            ->toArray();
                                    })
                                    ->getOptionLabelUsing(fn ($value) => optional(\App\Models\Producto::find($value))->nombre ?? 'Producto')
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        $prod = \App\Models\Producto::find($state);
                                        $set('presentacion_id', null);
                                        $precio = (float) ($prod?->precio ?? 0);
                                        $set('precio_unitario', $precio);
                                        $cantidad = (float) ($get('cantidad') ?? 1);
                                        $descuento = (float) ($get('descuento') ?? 0);
                                        $subtotalPreciso = round($cantidad * $precio, 4);
                                        $set('total', max(0, round($subtotalPreciso - $descuento, 2)));
                                    })
                                    ->required()
                                    ->columnSpan(2),
                                Forms\Components\Select::make('presentacion_id')
                                    ->label('Presentación')
                                    ->reactive()
                                    ->options(function (callable $get) {
                                        $productoId = $get('producto_id');
                                        if (!$productoId) return [];
                                        $sucursalId = auth()->user()->sucursal_id ?? null;
                                        return \App\Models\Presentacion::query()
                                            ->where('producto_id', $productoId)
                                            ->orderBy('nombre')
                                            ->get()
                                            ->mapWithKeys(function($pr) use ($sucursalId) {
                                                $pivot = $sucursalId ? $pr->sucursales()->where('sucursal_id', $sucursalId)->first()?->pivot : null;
                                                $stock = $pivot->stock ?? 0;
                                                $precio = (float) ($pivot->precio_venta ?? $pr->precio_unitario ?? 0);
                                                $label = $pr->nombre . " | Stock: " . $stock . " | $ " . number_format($precio, 2);
                                                return [$pr->id => $label];
                                            })
                                            ->toArray();
                                    })
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        if (!$state) return;
                                        $presentacion = \App\Models\Presentacion::find($state);
                                        $sucursalId = auth()->user()->sucursal_id ?? null;
                                        if ($presentacion && $sucursalId) {
                                            $pivot = $presentacion->sucursales()->where('sucursal_id', $sucursalId)->first()?->pivot;
                                            if ($pivot && isset($pivot->precio_venta)) {
                                                $set('precio_unitario', (float) $pivot->precio_venta);
                                            } else {
                                                $set('precio_unitario', (float) ($presentacion?->precio_unitario ?? 0));
                                            }
                                        } else {
                                            $set('precio_unitario', (float) ($presentacion?->precio_unitario ?? 0));
                                        }
                                        $cantidad = (float) ($get('cantidad') ?? 1);
                                        $descuento = (float) ($get('descuento') ?? 0);
                                        $precio = (float) ($get('precio_unitario') ?? 0);
                                        $subtotalPreciso = round($cantidad * $precio, 4);
                                        $set('total', max(0, round($subtotalPreciso - $descuento, 2)));

                                        // Actualizar totales del formulario
                                        $detalles = (array) ($get('detalles') ?? []);
                                        $formSubtotal = 0.0;
                                        $formDescuento = 0.0;
                                        foreach ($detalles as $d) {
                                            $dc = (float) ($d['cantidad'] ?? 1);
                                            $dp = (float) ($d['precio_unitario'] ?? 0);
                                            $dd = (float) ($d['descuento'] ?? 0);
                                            $formSubtotal += round($dc * $dp, 4);
                                            $formDescuento += $dd;
                                        }
                                        $formImpuestos = 0.0; // IVA configurable
                                        $set('subtotal', $formSubtotal);
                                        $set('descuento', $formDescuento);
                                        $set('impuestos', $formImpuestos);
                                        $set('total', max(0, round($formSubtotal + $formImpuestos - $formDescuento, 2)));
                                    })
                                    ->required()
                                    ->columnSpan(2),
                                Forms\Components\TextInput::make('cantidad')
                                    ->label('Cant.')
                                    ->numeric()
                                    ->default(1)
                                    ->reactive()
                                    ->minValue(function (callable $get) {
                                        $producto = $get('producto_id') ? \App\Models\Producto::find($get('producto_id')) : null;
                                        $presentacion = $get('presentacion_id') ? \App\Models\Presentacion::find($get('presentacion_id')) : null;
                                        $descProd = $producto?->description ?? '';
                                        $nombrePr = $presentacion?->nombre ?? '';
                                        $isKileada = (stripos($descProd, 'kileada') !== false || stripos($nombrePr, 'kileada') !== false);
                                        if (!$isKileada) {
                                            $isKileada = (stripos($descProd, 'kileado') !== false || stripos($nombrePr, 'kileado') !== false);
                                        }
                                        return $isKileada ? 0.01 : 1;
                                    })
                                    ->step(function (callable $get) {
                                        $producto = $get('producto_id') ? \App\Models\Producto::find($get('producto_id')) : null;
                                        $presentacion = $get('presentacion_id') ? \App\Models\Presentacion::find($get('presentacion_id')) : null;
                                        $descProd = $producto?->description ?? '';
                                        $nombrePr = $presentacion?->nombre ?? '';
                                        $isKileada = (stripos($descProd, 'kileada') !== false || stripos($nombrePr, 'kileada') !== false);
                                        if (!$isKileada) {
                                            $isKileada = (stripos($descProd, 'kileado') !== false || stripos($nombrePr, 'kileado') !== false);
                                        }
                                        return $isKileada ? '0.01' : '1';
                                    })
                                    ->rules(function (callable $get) {
                                        $producto = $get('producto_id') ? \App\Models\Producto::find($get('producto_id')) : null;
                                        $presentacion = $get('presentacion_id') ? \App\Models\Presentacion::find($get('presentacion_id')) : null;
                                        $descProd = $producto?->description ?? '';
                                        $nombrePr = $presentacion?->nombre ?? '';
                                        $isKileada = (stripos($descProd, 'kileada') !== false || stripos($nombrePr, 'kileada') !== false);
                                        if (!$isKileada) {
                                            $isKileada = (stripos($descProd, 'kileado') !== false || stripos($nombrePr, 'kileado') !== false);
                                        }
                                        return $isKileada ? ['numeric','gt:0'] : ['numeric','gte:1','integer'];
                                    })
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        $precio = (float) ($get('precio_unitario') ?? 0);
                                        $descuento = (float) ($get('descuento') ?? 0);
                                        $cantidad = (float) ($state ?? 0);
                                        $subtotalPreciso = round($cantidad * $precio, 4);
                                        $set('total', max(0, round($subtotalPreciso - $descuento, 2)));

                                        // Actualizar totales del formulario
                                        $detalles = (array) ($get('detalles') ?? []);
                                        $formSubtotal = 0.0;
                                        $formDescuento = 0.0;
                                        foreach ($detalles as $d) {
                                            $dc = (float) ($d['cantidad'] ?? 1);
                                            $dp = (float) ($d['precio_unitario'] ?? 0);
                                            $dd = (float) ($d['descuento'] ?? 0);
                                            $formSubtotal += round($dc * $dp, 4);
                                            $formDescuento += $dd;
                                        }
                                        $formImpuestos = 0.0;
                                        $set('subtotal', $formSubtotal);
                                        $set('descuento', $formDescuento);
                                        $set('impuestos', $formImpuestos);
                                        $set('total', max(0, round($formSubtotal + $formImpuestos - $formDescuento, 2)));
                                    }),
                                Forms\Components\TextInput::make('precio_unitario')
                                    ->label(function (callable $get) {
                                        $producto = $get('producto_id') ? \App\Models\Producto::find($get('producto_id')) : null;
                                        $presentacion = $get('presentacion_id') ? \App\Models\Presentacion::find($get('presentacion_id')) : null;
                                        $descProd = $producto?->description ?? '';
                                        $nombrePr = $presentacion?->nombre ?? '';
                                        $isKileada = (stripos($descProd, 'kileada') !== false || stripos($nombrePr, 'kileada') !== false);
                                        if (!$isKileada) {
                                            $isKileada = (stripos($descProd, 'kileado') !== false || stripos($nombrePr, 'kileado') !== false);
                                        }
                                        return $isKileada ? 'Precio kilo' : 'Precio unit.';
                                    })
                                    ->numeric()
                                    ->reactive()
                                    ->disabled()
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        $cantidad = (float) ($get('cantidad') ?? 1);
                                        $descuento = (float) ($get('descuento') ?? 0);
                                        $subtotalPreciso = round($cantidad * ((float) $state), 4);
                                        $set('total', max(0, round($subtotalPreciso - $descuento, 2)));

                                        // Actualizar totales del formulario
                                        $detalles = (array) ($get('detalles') ?? []);
                                        $formSubtotal = 0.0;
                                        $formDescuento = 0.0;
                                        foreach ($detalles as $d) {
                                            $dc = (float) ($d['cantidad'] ?? 1);
                                            $dp = (float) ($d['precio_unitario'] ?? 0);
                                            $dd = (float) ($d['descuento'] ?? 0);
                                            $formSubtotal += round($dc * $dp, 4);
                                            $formDescuento += $dd;
                                        }
                                        $formImpuestos = 0.0;
                                        $set('subtotal', $formSubtotal);
                                        $set('descuento', $formDescuento);
                                        $set('impuestos', $formImpuestos);
                                        $set('total', max(0, round($formSubtotal + $formImpuestos - $formDescuento, 2)));
                                    })
                                    ->required(),
                                Forms\Components\TextInput::make('descuento')
                                    ->label('Desc.')
                                    ->numeric()
                                    ->default(0)
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        $cantidad = (float) ($get('cantidad') ?? 1);
                                        $precio = (float) ($get('precio_unitario') ?? 0);
                                        $subtotalPreciso = round($cantidad * $precio, 4);
                                        $set('total', max(0, round($subtotalPreciso - ((float) $state), 2)));

                                        // Actualizar totales del formulario
                                        $detalles = (array) ($get('detalles') ?? []);
                                        $formSubtotal = 0.0;
                                        $formDescuento = 0.0;
                                        foreach ($detalles as $d) {
                                            $dc = (float) ($d['cantidad'] ?? 1);
                                            $dp = (float) ($d['precio_unitario'] ?? 0);
                                            $dd = (float) ($d['descuento'] ?? 0);
                                            $formSubtotal += round($dc * $dp, 4);
                                            $formDescuento += $dd;
                                        }
                                        $formImpuestos = 0.0;
                                        $set('subtotal', $formSubtotal);
                                        $set('descuento', $formDescuento);
                                        $set('impuestos', $formImpuestos);
                                        $set('total', max(0, round($formSubtotal + $formImpuestos - $formDescuento, 2)));
                                    }),
                                Forms\Components\TextInput::make('total')
                                    ->label('Total línea')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->numeric()
                                    ->default(0),
                            ])
                            ->mutateRelationshipDataBeforeCreateUsing(function (array $data) {
                                $cantidad = (float) ($data['cantidad'] ?? 1);
                                $descuento = (float) ($data['descuento'] ?? 0);
                                $precio = (float) ($data['precio_unitario'] ?? 0);
                                if ($precio <= 0) {
                                    $presentacionId = $data['presentacion_id'] ?? null;
                                    if ($presentacionId) {
                                        $presentacion = \App\Models\Presentacion::find($presentacionId);
                                        $sucursalId = auth()->user()->sucursal_id ?? null;
                                        if ($presentacion && $sucursalId) {
                                            $pivot = $presentacion->sucursales()->where('sucursal_id', $sucursalId)->first()?->pivot;
                                            if ($pivot && isset($pivot->precio_venta)) {
                                                $precio = (float) $pivot->precio_venta;
                                            } else {
                                                $precio = (float) ($presentacion->precio_unitario ?? 0);
                                            }
                                        }
                                    } else {
                                        $productoId = $data['producto_id'] ?? null;
                                        $producto = $productoId ? \App\Models\Producto::find($productoId) : null;
                                        $precio = (float) ($producto?->precio ?? 0);
                                    }
                                }
                                $subtotal = round($cantidad * $precio, 4);
                                $data['precio_unitario'] = $precio;
                                $data['subtotal'] = $subtotal;
                                $data['total'] = max(0, round($subtotal - $descuento, 2));
                                return $data;
                            })
                            ->mutateRelationshipDataBeforeSaveUsing(function (array $data) {
                                $cantidad = (float) ($data['cantidad'] ?? 1);
                                $descuento = (float) ($data['descuento'] ?? 0);
                                $precio = (float) ($data['precio_unitario'] ?? 0);
                                if ($precio <= 0) {
                                    $presentacionId = $data['presentacion_id'] ?? null;
                                    if ($presentacionId) {
                                        $presentacion = \App\Models\Presentacion::find($presentacionId);
                                        $sucursalId = auth()->user()->sucursal_id ?? null;
                                        if ($presentacion && $sucursalId) {
                                            $pivot = $presentacion->sucursales()->where('sucursal_id', $sucursalId)->first()?->pivot;
                                            if ($pivot && isset($pivot->precio_venta)) {
                                                $precio = (float) $pivot->precio_venta;
                                            } else {
                                                $precio = (float) ($presentacion->precio_unitario ?? 0);
                                            }
                                        }
                                    } else {
                                        $productoId = $data['producto_id'] ?? null;
                                        $producto = $productoId ? \App\Models\Producto::find($productoId) : null;
                                        $precio = (float) ($producto?->precio ?? 0);
                                    }
                                }
                                $subtotal = $cantidad * $precio;
                                $data['precio_unitario'] = $precio;
                                $data['subtotal'] = $subtotal;
                                $data['total'] = max(0, $subtotal - $descuento);
                                return $data;
                            }),
                    ]),

                Forms\Components\Section::make('Totales')
                    ->schema([
                        Forms\Components\TextInput::make('subtotal')
                            ->label('Subtotal')
                            ->disabled()
                            ->dehydrated(false)
                            ->numeric()
                            ->default(0),
                        Forms\Components\TextInput::make('impuestos')
                            ->label('IVA')
                            ->disabled()
                            ->dehydrated(false)
                            ->numeric()
                            ->default(0),
                        Forms\Components\TextInput::make('descuento')
                            ->label('Descuento total')
                            ->disabled()
                            ->dehydrated(false)
                            ->numeric()
                            ->default(0),
                        Forms\Components\TextInput::make('total')
                            ->label('Total')
                            ->disabled()
                            ->dehydrated(false)
                            ->numeric()
                            ->default(0),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('numero_factura')
                    ->searchable(),
                Tables\Columns\TextColumn::make('cliente_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sucursal_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('consulta_medica_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('cirugia_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('fecha_emision')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('fecha_vencimiento')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('subtotal')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('impuestos')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('descuento')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('estado'),
                Tables\Columns\TextColumn::make('metodo_pago')
                    ->searchable(),
                Tables\Columns\TextColumn::make('fecha_pago')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('recibo')
                    ->label('Imprimir')
                    ->icon('heroicon-o-printer')
                    ->url(fn($record) => route('facturas.recibo', ['factura' => $record->id]))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListFacturas::route('/'),
            'create' => Pages\CreateFactura::route('/create'),
            'edit' => Pages\EditFactura::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('sucursal_id', auth()->user()->sucursal_id)
            ->where('tipo_venta', 'presencial');
    }
}