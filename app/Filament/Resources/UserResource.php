<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;
    protected static ?string $navigationLabel = 'Usuarios de acceso';
    protected static ?string $modelLabel = 'usuario';
    protected static ?string $pluralModelLabel = 'usuarios';
    protected static ?string $recordTitleAttribute = 'name';

    public static function canAccess(): bool
    {
        return auth()->user()?->isAdministrator() === true;
    }

    public static function canDelete(Model $record): bool
    {
        if (! static::canAccess() || $record->is(auth()->user())) {
            return false;
        }

        return ! ($record->role === 'admin' && User::query()->where('role', 'admin')->count() <= 1);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Datos de acceso')
                ->schema([
                    TextInput::make('name')
                        ->label('Nombre')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('email')
                        ->label('Correo electrónico')
                        ->email()
                        ->required()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true),
                    Select::make('role')
                        ->label('Nivel de acceso')
                        ->options([
                            'admin' => 'Administrador — acceso completo',
                            'user' => 'Usuario — acceso por secciones',
                            'provider' => 'Prestador turístico — acceso a su propia página',
                        ])
                        ->default('user')
                        ->required()
                        ->live()
                        ->disabled(fn (?User $record): bool => $record?->is(auth()->user()) === true
                            || ($record?->role === 'admin' && User::query()->where('role', 'admin')->count() <= 1)),
                    TextInput::make('password')
                        ->label(fn (string $operation): string => $operation === 'create' ? 'Contraseña' : 'Nueva contraseña')
                        ->helperText(fn (string $operation): string => $operation === 'create'
                            ? 'Mínimo 12 caracteres.'
                            : 'Déjela vacía para conservar la contraseña actual.')
                        ->password()
                        ->revealable()
                        ->autocomplete('new-password')
                        ->required(fn (string $operation): bool => $operation === 'create')
                        ->rule(Password::min(12)->letters()->mixedCase()->numbers())
                        ->same('password_confirmation')
                        ->dehydrated(fn ($state): bool => filled($state))
                        ->dehydrateStateUsing(fn ($state): string => Hash::make($state)),
                    TextInput::make('password_confirmation')
                        ->label('Confirmar contraseña')
                        ->password()
                        ->revealable()
                        ->autocomplete('new-password')
                        ->required(fn (Get $get, string $operation): bool => $operation === 'create' || filled($get('password')))
                        ->visible(fn (Get $get, string $operation): bool => $operation === 'create' || filled($get('password')))
                        ->dehydrated(false),
                ])
                ->columns(2),
            Section::make('Secciones permitidas')
                ->description('El usuario podrá abrir y modificar únicamente las secciones seleccionadas.')
                ->visible(fn (Get $get): bool => $get('role') === 'user')
                ->schema([
                    CheckboxList::make('admin_sections')
                        ->label('Acceso al menú administrativo')
                        ->options(collect(config('admin_sections'))
                            ->mapWithKeys(fn (array $section, string $key): array => [$key => $section['label']])
                            ->all())
                        ->columns(2)
                        ->bulkToggleable(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nombre')->searchable()->sortable(),
                TextColumn::make('email')->label('Correo')->searchable()->sortable(),
                TextColumn::make('role')
                    ->label('Nivel')
                    ->formatStateUsing(fn (string $state): string => $state === 'admin' ? 'Administrador' : 'Usuario')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'admin' ? 'danger' : 'info'),
                TextColumn::make('admin_sections')
                    ->label('Secciones')
                    ->state(fn (User $record): string => $record->isAdministrator()
                        ? 'Todas'
                        : count($record->admin_sections ?? []).' asignada(s)'),
                TextColumn::make('updated_at')->label('Actualizado')->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
