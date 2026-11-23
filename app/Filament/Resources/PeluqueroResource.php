<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PeluqueroResource\Pages;
use App\Models\User;
use App\Mail\PeluqueroCredentials;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Filters\SelectFilter;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

class PeluqueroResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-scissors';
    
    protected static ?string $navigationLabel = 'Peluqueros';
    
    protected static ?string $modelLabel = 'Peluquero';

    protected static ?string $pluralModelLabel = 'Peluqueros';

    protected static ?string $navigationGroup = 'Gestión de Personal';

    protected static ?int $navigationSort = 2;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('role', 'peluquero');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Información del Peluquero')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nombre Completo')
                                    ->required()
                                    ->maxLength(255),
                                
                                TextInput::make('email')
                                    ->label('Correo Electrónico')
                                    ->email()
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255),
                            ]),
                        
                        Grid::make(2)
                            ->schema([
                                TextInput::make('password')
                                    ->label('Contraseña')
                                    ->password()
                                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                                    ->dehydrated(fn ($state) => filled($state))
                                    ->required(fn (string $context): bool => $context === 'create')
                                    ->helperText('Deja en blanco para mantener la contraseña actual al editar'),
                                
                                Select::make('estado')
                                    ->label('Estado')
                                    ->options([
                                        'activo' => 'Activo',
                                        'inactivo' => 'Inactivo',
                                        'suspendido' => 'Suspendido',
                                    ])
                                    ->default('activo')
                                    ->required(),
                            ]),
                        
                        TextInput::make('role')
                            ->label('Rol')
                            ->default('peluquero')
                            ->disabled()
                            ->dehydrated(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Medium),
                
                TextColumn::make('email')
                    ->label('Correo Electrónico')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                
                TextColumn::make('role')
                    ->label('Rol')
                    ->badge()
                    ->color('info'),
                
                TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'activo' => 'success',
                        'inactivo' => 'gray',
                        'suspendido' => 'danger',
                    }),
                
                TextColumn::make('password_changed_at')
                    ->label('Contraseña Cambiada')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->placeholder('No cambiada')
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('created_at')
                    ->label('Fecha de Registro')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('estado')
                    ->label('Estado')
                    ->options([
                        'activo' => 'Activo',
                        'inactivo' => 'Inactivo',
                        'suspendido' => 'Suspendido',
                    ]),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\Action::make('generate_credentials')
                        ->label('Generar Credenciales')
                        ->icon('heroicon-o-key')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Generar Credenciales')
                        ->modalDescription('¿Estás seguro de que quieres generar nuevas credenciales para este peluquero?')
                        ->action(function (User $record) {
                            $password = Str::random(8);
                            $record->update([
                                'password' => Hash::make($password)
                            ]);
                            
                            // Almacenar temporalmente la contraseña en la sesión
                            session(['temp_password_' . $record->id => $password]);
                            
                            Notification::make()
                                ->title('Credenciales generadas')
                                ->body("Nuevas credenciales generadas para {$record->name}. Ahora puedes enviarlas por correo.")
                                ->success()
                                ->send();
                        }),
                    
                    Tables\Actions\Action::make('send_credentials')
                        ->label('Enviar Credenciales')
                        ->icon('heroicon-o-envelope')
                        ->color('info')
                        ->action(function (User $record) {
                            $temporaryPassword = session('temp_password_' . $record->id);
                            
                            if (!$temporaryPassword) {
                                Notification::make()
                                    ->title('Error')
                                    ->body('No hay credenciales temporales disponibles. Genera las credenciales primero.')
                                    ->danger()
                                    ->send();
                                return;
                            }
                            
                            Mail::to($record->email)->send(new PeluqueroCredentials($record, $temporaryPassword));
                            
                            // Limpiar la contraseña temporal de la sesión
                            session()->forget('temp_password_' . $record->id);
                            
                            Notification::make()
                                ->title('Credenciales enviadas')
                                ->body("Se han enviado las credenciales a {$record->email}")
                                ->success()
                                ->send();
                        }),
                    
                    Tables\Actions\Action::make('regenerate_credentials')
                        ->label('Regenerar Credenciales')
                        ->icon('heroicon-o-arrow-path')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalHeading('Regenerar Credenciales')
                        ->modalDescription('¿Estás seguro de que quieres regenerar las credenciales de este peluquero? Se generará una nueva contraseña.')
                        ->action(function (User $record) {
                            $password = Str::random(8);
                            $record->update([
                                'password' => Hash::make($password)
                            ]);
                            
                            // Almacenar temporalmente la contraseña en la sesión
                            session(['temp_password_' . $record->id => $password]);
                            
                            Notification::make()
                                ->title('Credenciales regeneradas')
                                ->body("Credenciales regeneradas para {$record->name}. Ahora puedes enviarlas por correo.")
                                ->success()
                                ->send();
                        }),
                    
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
            'index' => Pages\ListPeluqueros::route('/'),
            'create' => Pages\CreatePeluquero::route('/create'),
            'view' => Pages\ViewPeluquero::route('/{record}'),
            'edit' => Pages\EditPeluquero::route('/{record}/edit'),
        ];
    }
}