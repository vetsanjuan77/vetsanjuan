<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VeterinariosResource\Pages;
use App\Models\User;
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

class VeterinariosResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'Veterinarios';

    protected static ?string $modelLabel = 'Veterinario';

    protected static ?string $pluralModelLabel = 'Veterinarios';

    protected static ?string $navigationGroup = 'Gestión de Personal';

    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('role', 'veterinario');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Información del Veterinario')
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
                            ->default('veterinario')
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
                
                TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'activo' => 'success',
                        'inactivo' => 'gray',
                        'suspendido' => 'danger',
                    }),
                
                TextColumn::make('citasAsignadas_count')
                    ->label('Citas Asignadas')
                    ->counts('citasAsignadas')
                    ->sortable(),
                
                TextColumn::make('created_at')
                    ->label('Fecha de Registro')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('email_verified_at')
                    ->label('Email Verificado')
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
                    Tables\Actions\Action::make('regenerar_credenciales')
                        ->label('Regenerar Credenciales')
                        ->icon('heroicon-o-key')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalHeading('Regenerar Credenciales')
                        ->modalDescription('¿Estás seguro de que quieres regenerar las credenciales de este veterinario? Se generará una nueva contraseña y se enviará por correo.')
                        ->action(function (User $record) {
                            $nuevaPassword = Str::random(8);
                            $record->update([
                                'password' => Hash::make($nuevaPassword)
                            ]);
                            
                            // Aquí puedes agregar el envío de email
                            // Mail::to($record->email)->send(new NuevasCredenciales($record, $nuevaPassword));
                            
                            Notification::make()
                                ->title('Credenciales regeneradas')
                                ->body("Nueva contraseña para {$record->name}: {$nuevaPassword}")
                                ->success()
                                ->persistent()
                                ->send();
                        }),
                    Tables\Actions\Action::make('enviar_credenciales')
                        ->label('Enviar Credenciales')
                        ->icon('heroicon-o-envelope')
                        ->color('info')
                        ->action(function (User $record) {
                            // Aquí implementarías el envío de credenciales por email
                            Notification::make()
                                ->title('Credenciales enviadas')
                                ->body("Se han enviado las credenciales a {$record->email}")
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
            'index' => Pages\ListVeterinarios::route('/'),
            'create' => Pages\CreateVeterinarios::route('/create'),
            'view' => Pages\ViewVeterinarios::route('/{record}'),
            'edit' => Pages\EditVeterinarios::route('/{record}/edit'),
        ];
    }
}