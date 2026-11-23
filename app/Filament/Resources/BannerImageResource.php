<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BannerImageResource\Pages; 
use App\Models\BannerImage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Get;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Arr;

class BannerImageResource extends Resource
{
    protected static ?string $model = BannerImage::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationGroup = 'Sitio';

    protected static ?string $navigationLabel = 'Banners';

    protected static ?string $modelLabel = 'Imagen de Banner';

    protected static ?string $pluralModelLabel = 'Imágenes de Banner';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make([
                    'default' => 1,
                    'sm' => 1,
                    'lg' => 2,
                ])->schema([
                    Forms\Components\Section::make('Datos de la imagen')
                        ->description('Sube la imagen y define su comportamiento en el banner')
                        ->schema([
                            Forms\Components\FileUpload::make('image_path')
                                ->label('Imagen')
                                ->image()
                                ->disk('public')
                                ->directory('banners')
                                ->imageEditor()
                                ->maxSize(5120) // 5MB
                                ->acceptedFileTypes(['image/jpeg','image/png','image/webp'])
                                ->required()
                                ->columnSpanFull()
                                ->hint('Formatos permitidos: JPEG, PNG, WebP. Tamaño máximo: 5MB')
                                ->validationAttribute('imagen')
                                ->rules(['dimensions:min_width=1200,min_height=400']),

                            Forms\Components\TextInput::make('title')
                                ->label('Título (opcional)')
                                ->maxLength(120),

                            Forms\Components\TextInput::make('alt_text')
                                ->label('Texto alternativo (accesibilidad)')
                                ->maxLength(160),

                            Forms\Components\TextInput::make('display_seconds')
                                ->label('Tiempo de visualización (segundos)')
                                ->numeric()
                                ->minValue(1)
                                ->maxValue(60)
                                ->default(5)
                                ->required(),

                            Forms\Components\Toggle::make('is_active')
                                ->label('Activo')
                                ->default(true)
                                ->helperText('Desactiva para ocultar esta imagen del banner.'),
                        ]),

                    Forms\Components\Section::make('Previsualización')
                        ->description('Visualiza en tiempo real la imagen y el tiempo configurado')
                        ->schema([
                            Forms\Components\Placeholder::make('banner_preview')
                                ->content(function (Get $get) : HtmlString {
                                    $secondsRaw = $get('display_seconds');
                                    $seconds = is_numeric($secondsRaw) ? (int) $secondsRaw : 5;

                                    $path = $get('image_path');
                                    $url = null;

                                    // Soportar archivos temporales de Livewire (v2/v3) con temporaryUrl()
                                    if (is_object($path) && method_exists($path, 'temporaryUrl')) {
                                        $url = $path->temporaryUrl();
                                    } elseif (is_string($path) && $path !== '') {
                                        $url = Storage::disk('public')->url($path);
                                    } elseif (is_array($path)) {
                                        $first = Arr::first($path);
                                        if (is_object($first) && method_exists($first, 'temporaryUrl')) {
                                            $url = $first->temporaryUrl();
                                        } elseif (is_string($first) && $first !== '') {
                                            $url = Storage::disk('public')->url($first);
                                        } elseif (is_array($first)) {
                                            $candidate = $first['path'] ?? $first['file'] ?? $first['name'] ?? null;
                                            if (is_string($candidate) && $candidate !== '') {
                                                $url = Storage::disk('public')->url($candidate);
                                            }
                                        }
                                    }

                                    return new HtmlString(view('filament.banner.preview', [
                                        'seconds' => $seconds,
                                        'url' => $url,
                                    ])->render());
                                })
                                ->columnSpanFull(),
                        ]),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image_path')
                    ->label('Imagen')
                    ->disk('public')
                    ->square(),
                Tables\Columns\TextColumn::make('title')
                    ->label('Título')
                    ->limit(30)
                    ->searchable(),
                Tables\Columns\TextColumn::make('display_seconds')
                    ->label('Segundos')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean(),
                Tables\Columns\TextColumn::make('order')
                    ->label('Orden')
                    ->sortable(),
            ])
            ->defaultSort('order')
            ->reorderable('order')
            ->filters([
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBannerImages::route('/'),
            'create' => Pages\CreateBannerImage::route('/create'),
            'edit' => Pages\EditBannerImage::route('/{record}/edit'),
        ];
    }
}