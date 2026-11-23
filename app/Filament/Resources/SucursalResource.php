<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SucursalResource\Pages;
use App\Models\Sucursal;
use App\Models\User;
use App\Mail\SucursalCredentials;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Filters\SelectFilter;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

class SucursalResource extends Resource
{
    protected static ?string $model = Sucursal::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    protected static ?string $navigationLabel = 'Sucursales';

    protected static ?string $modelLabel = 'Sucursal';

    protected static ?string $pluralModelLabel = 'Sucursales';

    protected static ?string $navigationGroup = 'Administración';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Información de la Sucursal')
                    ->description('Datos básicos de la sucursal')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('nombre')
                                    ->label('Nombre de la Sucursal')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Ej: Sucursal Centro'),
                                
                                TextInput::make('codigo')
                                    ->label('Código')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(10)
                                    ->placeholder('Ej: SUC001')
                                    ->alphaDash(),
                            ]),
                        
                        Textarea::make('direccion')
                            ->label('Dirección')
                            ->required()
                            ->rows(2)
                            ->placeholder('Dirección completa de la sucursal'),
                        
                        Grid::make(3)
                            ->schema([
                                TextInput::make('ciudad')
                                    ->label('Ciudad')
                                    ->required()
                                    ->maxLength(255),
                                
                                TextInput::make('departamento')
                                    ->label('Departamento')
                                    ->required()
                                    ->maxLength(255),
                                
                                TextInput::make('nit')
                                    ->label('NIT')
                                    ->maxLength(255)
                                    ->placeholder('Opcional'),
                            ]),
                    ]),

                Section::make('Información de Contacto')
                    ->description('Datos de contacto de la sucursal')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('telefono')
                                    ->label('Teléfono')
                                    ->tel()
                                    ->maxLength(255)
                                    ->placeholder('Ej: +57 300 123 4567'),
                                
                                TextInput::make('email')
                                    ->label('Email')
                                    ->email()
                                    ->maxLength(255)
                                    ->placeholder('sucursal@veterinaria.com'),
                            ]),
                    ]),

                Section::make('Administrador de Sucursal')
                    ->description('Crear usuario administrador para esta sucursal')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('admin_name')
                                    ->label('Nombre del Administrador')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Nombre completo del administrador'),
                                
                                TextInput::make('admin_email')
                                    ->label('Email del Administrador')
                                    ->email()
                                    ->required()
                                    ->unique(
                                        table: User::class,
                                        column: 'email',
                                        modifyRuleUsing: function ($rule, $get, $record) {
                                            // Si estamos editando y la sucursal tiene un usuario asignado, ignorar ese usuario
                                            if ($record && $record->user_id) {
                                                return $rule->ignore($record->user_id);
                                            }
                                            return $rule;
                                        }
                                    )
                                    ->maxLength(255)
                                    ->placeholder('admin@sucursal.com'),
                            ]),
                        
                        TextInput::make('admin_password')
                            ->label('Contraseña')
                            ->password()
                            ->required(fn (string $context): bool => $context === 'create')
                            ->minLength(8)
                            ->placeholder('Mínimo 8 caracteres')
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->dehydrated(fn ($state) => filled($state))
                            ->revealable(),
                    ]),

                Section::make('Configuración')
                    ->description('Configuraciones adicionales')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Select::make('regimen_tributario')
                                    ->label('Régimen Tributario')
                                    ->options([
                                        'simplificado' => 'Simplificado',
                                        'ordinario' => 'Ordinario',
                                        'gran_contribuyente' => 'Gran Contribuyente',
                                    ])
                                    ->default('simplificado')
                                    ->required(),
                                
                                Toggle::make('activa')
                                    ->label('Sucursal Activa')
                                    ->default(true)
                                    ->helperText('Desactivar temporalmente la sucursal'),
                            ]),
                        
                        Grid::make(2)
                            ->schema([
                                TextInput::make('latitud')
                                    ->label('Latitud')
                                    ->numeric()
                                    ->placeholder('Ej: 4.6097100')
                                    ->step(0.0000001),
                                
                                TextInput::make('longitud')
                                    ->label('Longitud')
                                    ->numeric()
                                    ->placeholder('Ej: -74.0817500')
                                    ->step(0.0000001),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('codigo')
                    ->label('Código')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold),
                
                TextColumn::make('nombre')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Sucursal $record): string => $record->direccion),
                
                TextColumn::make('ciudad')
                    ->label('Ciudad')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('telefono')
                    ->label('Teléfono')
                    ->searchable()
                    ->icon('heroicon-m-phone'),
                
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->icon('heroicon-m-envelope'),
                
                TextColumn::make('usuario.name')
                    ->label('Administrador')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Sucursal $record): ?string => $record->usuario?->email),
                
                Tables\Columns\IconColumn::make('activa')
                    ->label('Estado')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                
                TextColumn::make('created_at')
                    ->label('Creada')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('activa')
                    ->label('Estado')
                    ->options([
                        '1' => 'Activa',
                        '0' => 'Inactiva',
                    ]),
                
                SelectFilter::make('departamento')
                    ->label('Departamento')
                    ->options(
                        Sucursal::query()
                            ->distinct()
                            ->pluck('departamento', 'departamento')
                            ->toArray()
                    ),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make()
                        ->color('warning'),
                    
                    Tables\Actions\Action::make('reset_password')
                        ->label('Resetear Contraseña')
                        ->icon('heroicon-o-key')
                        ->color('info')
                        ->action(function (Sucursal $record) {
                            if (!$record->usuario) {
                                Notification::make()
                                    ->title('Error')
                                    ->body('Esta sucursal no tiene un administrador asignado.')
                                    ->danger()
                                    ->send();
                                return;
                            }

                            $newPassword = Str::random(12);
                            $record->usuario->update([
                                'password' => Hash::make($newPassword),
                                'password_changed_at' => now(),
                            ]);

                            // Enviar email con nueva contraseña
                            try {
                                Mail::to($record->usuario->email)->send(
                                    new SucursalCredentials($record->usuario, $newPassword, $record)
                                );
                                
                                Notification::make()
                                    ->title('Contraseña Reseteada')
                                    ->body('Se ha enviado la nueva contraseña al email del administrador.')
                                    ->success()
                                    ->send();
                            } catch (\Exception $e) {
                                Notification::make()
                                    ->title('Contraseña Reseteada')
                                    ->body("Nueva contraseña: {$newPassword}")
                                    ->warning()
                                    ->persistent()
                                    ->send();
                            }
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Resetear Contraseña')
                        ->modalDescription('¿Estás seguro de que quieres resetear la contraseña del administrador de esta sucursal?'),
                    
                    Tables\Actions\DeleteAction::make(),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListSucursales::route('/'),
            'create' => Pages\CreateSucursal::route('/create'),
            'edit' => Pages\EditSucursal::route('/{record}/edit'),
        ];
    }
}