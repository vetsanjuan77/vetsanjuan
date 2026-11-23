<?php

namespace App\Filament\Sucursal\Resources;

use App\Filament\Sucursal\Resources\ProveedorResource\Pages;
use App\Filament\Sucursal\Resources\ProveedorResource\RelationManagers;
use App\Models\Proveedor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProveedorResource extends Resource
{
    protected static ?string $model = Proveedor::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';
    protected static ?string $navigationGroup = 'Compras y Proveedores';
    protected static ?string $navigationLabel = 'Proveedores';
    protected static ?string $modelLabel = 'Proveedor';
    protected static ?string $pluralModelLabel = 'Proveedores';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información General')
                    ->schema([
                        Forms\Components\TextInput::make('nombre')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(2),
                        Forms\Components\TextInput::make('nit')
                            ->label('NIT/RUC')
                            ->maxLength(20),
                        Forms\Components\Select::make('tipo')
                            ->options([
                                'productos' => 'Productos',
                                'servicios' => 'Servicios',
                                'mixto' => 'Mixto',
                                'ambos' => 'Ambos',
                            ])
                            ->required(),
                        Forms\Components\Toggle::make('activo')
                            ->default(true),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Información de Contacto')
                    ->schema([
                        Forms\Components\TextInput::make('telefono')
                            ->tel()
                            ->maxLength(20),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('direccion')
                            ->maxLength(255)
                            ->columnSpan(2),
                        Forms\Components\TextInput::make('ciudad')
                            ->maxLength(100),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Contacto Principal')
                    ->schema([
                        Forms\Components\TextInput::make('contacto_nombre')
                            ->label('Nombre del Contacto')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('contacto_telefono')
                            ->label('Teléfono del Contacto')
                            ->tel()
                            ->maxLength(20),
                        Forms\Components\TextInput::make('contacto_email')
                            ->label('Email del Contacto')
                            ->email()
                            ->maxLength(255),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Observaciones')
                    ->schema([
                        Forms\Components\Textarea::make('observaciones')
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
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nit')
                    ->label('NIT/RUC')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tipo')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'productos' => 'success',
                        'servicios' => 'info',
                        'mixto' => 'warning',
                        'ambos' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('telefono')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ciudad')
                    ->searchable(),
                Tables\Columns\IconColumn::make('activo')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('tipo')
                    ->options([
                        'productos' => 'Productos',
                        'servicios' => 'Servicios',
                        'mixto' => 'Mixto',
                        'ambos' => 'Ambos',
                    ]),
                Tables\Filters\TernaryFilter::make('activo')
                    ->label('Estado')
                    ->boolean()
                    ->trueLabel('Activos')
                    ->falseLabel('Inactivos')
                    ->native(false),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListProveedors::route('/'),
            'create' => Pages\CreateProveedor::route('/create'),
            'edit' => Pages\EditProveedor::route('/{record}/edit'),
        ];
    }
}
