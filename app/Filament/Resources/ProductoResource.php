<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductoResource\Pages;
use App\Filament\Resources\ProductoResource\RelationManagers;
use App\Models\Producto;
use App\Models\Marca;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Get;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Arr;

class ProductoResource extends Resource
{
    protected static ?string $model = Producto::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Información básica')
                ->schema([
                    TextInput::make('nombre')->required(),
                    Textarea::make('description')->required(),
                    TextInput::make('precio')
                        ->label('Precio Base')
                        ->numeric()
                        ->prefix('$')
                        ->required(),
                ])
                ->columns(2),
                

            Section::make('Presentaciones del producto')
                ->schema([
                    Repeater::make('presentaciones')
                        ->relationship('presentaciones')
                        ->schema([
                            TextInput::make('nombre')
                                ->label('Presentación')
                                ->required(),
                            TextInput::make('precio_unitario')
                                ->label('Precio Base')
                                ->numeric()
                                ->prefix('$')
                                ->required()
                                ->helperText('Precio base para cálculos. Las sucursales pueden ajustar sus precios de venta.'),
                            FileUpload::make('imagen')
                                ->label('Imagen de la presentación')
                                ->image()
                                ->disk('public')
                                ->directory('presentaciones')
                                ->imageEditor()
                                ->nullable(),
                            Hidden::make('cantidad_inventario')
                                ->default(0),
                        ])
                        ->columns(3)
                        ->defaultItems(0)
                        ->label('Presentaciones')
                        ->helperText('Las presentaciones se crean aquí. El stock se gestiona desde cada sucursal.'),
                ])
                ->collapsible(),

            Section::make('Categorización')
                ->schema([
                    Select::make('especie')
                        ->options([
                            'perro' => 'Perro',
                            'gato' => 'Gato',
                            'aves' => 'Aves',
                            'peces' => 'Peces',
                            'otros' => 'Otros',
                        ])
                        ->required(),

                    Select::make('edad')
                        ->options([
                            'cachorro' => 'Cachorro',
                            'adulto' => 'Adulto',
                            'senior' => 'Senior',
                        ])
                        ->required(),

                    Select::make('marca_id')
                        ->label('Marca')
                        ->options(Marca::all()->pluck('nombre', 'id'))
                        ->searchable()
                        ->required(),

                    Select::make('categoria')
                        ->options([
                            'Alimentos' => 'Alimentos',
                            'Accesorios' => 'Accesorios',
                            'Estética e Higiene' => 'Estética e Higiene',
                            'Medicamentos' => 'Medicamentos',
                            'Juguetes' => 'Juguetes',
                        ])
                        ->required(),

                    Forms\Components\Checkbox::make('destacado')
                        ->label('Producto destacado')
                        ->helperText('Si se marca, este producto se mostrará en la página de inicio')
                        ->default(false),

                    FileUpload::make('imagen')
                        ->label('Imagen principal')
                        ->image()
                        ->disk('public')
                        ->directory('productos')
                        ->imageEditor()
                        ->nullable()
                ])
                ->columns(2),
                
            Section::make('Previsualización')
                ->description('Visualiza en tiempo real cómo se verá el producto en la tienda')
                ->schema([
                    Forms\Components\Placeholder::make('product_preview')
                        ->content(function (Get $get): HtmlString {
                            $nombre = (string) ($get('nombre') ?? '');
                            $descripcion = (string) ($get('description') ?? '');
                            $precio = $get('precio');
                            $precio = is_numeric($precio) ? (float) $precio : null;

                            $mainImage = $get('imagen');
                            $resolveUrl = function ($value) {
                                if (is_object($value) && method_exists($value, 'temporaryUrl')) {
                                    return $value->temporaryUrl();
                                }
                                if (is_string($value) && $value !== '') {
                                    return Storage::disk('public')->url($value);
                                }
                                if (is_array($value)) {
                                    $first = Arr::first($value);
                                    if (is_object($first) && method_exists($first, 'temporaryUrl')) {
                                        return $first->temporaryUrl();
                                    }
                                    if (is_string($first) && $first !== '') {
                                        return Storage::disk('public')->url($first);
                                    }
                                    if (is_array($first)) {
                                        $candidate = $first['path'] ?? $first['file'] ?? $first['name'] ?? null;
                                        if (is_string($candidate) && $candidate !== '') {
                                            return Storage::disk('public')->url($candidate);
                                        }
                                    }
                                }
                                return null;
                            };

                            $imagenUrl = $resolveUrl($mainImage);

                            $presentaciones = $get('presentaciones') ?? [];
                            $presentacionesData = [];
                            foreach ($presentaciones as $item) {
                                $presentacionesData[] = [
                                    'nombre' => (string) ($item['nombre'] ?? ''),
                                    'precio' => isset($item['precio_unitario']) && is_numeric($item['precio_unitario']) ? (float) $item['precio_unitario'] : null,
                                    'imagenUrl' => $resolveUrl($item['imagen'] ?? null),
                                ];
                            }

                            return new HtmlString(view('filament.producto.preview', [
                                'nombre' => $nombre,
                                'descripcion' => $descripcion,
                                'precio' => $precio,
                                'imagenUrl' => $imagenUrl,
                                'presentaciones' => $presentacionesData,
                            ])->render());
                        })
                        ->columnSpanFull(),
                ])
                ->collapsible(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('nombre')
                ->searchable()
                ->sortable(),

            TextColumn::make('presentaciones.nombre')
                ->label('Presentaciones')
                ->listWithLineBreaks()
                ->limitList(3)
                ->expandableLimitedList(),

            TextColumn::make('presentaciones.precio_unitario')
                ->label('Precios')
                ->money('cop')
                ->listWithLineBreaks()
                ->limitList(3)
                ->expandableLimitedList(),

            TextColumn::make('presentaciones.stock_total')
                ->label('Stock Total')
                ->getStateUsing(function ($record) {
                    return $record->presentaciones->sum('stock_total');
                })
                ->badge()
                ->color(function ($state): string {
                    return match (true) {
                        $state == 0 => 'danger',
                        $state <= 10 => 'warning',
                        default => 'success',
                    };
                }),

            TextColumn::make('categoria')
                ->sortable(),

            TextColumn::make('marca_id'),

            TextColumn::make('especie'),

            TextColumn::make('created_at')
                ->dateTime()
                ->label('Creado'),
        ])
        ->filters([
            
        ])
        ->actions([
            Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
        ])
        ->bulkActions([
            
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
            'index' => Pages\ListProductos::route('/'),
            'create' => Pages\CreateProducto::route('/create'),
            'edit' => Pages\EditProducto::route('/{record}/edit'),
        ];
    }
}
