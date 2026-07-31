<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\HasSectionPermission;
use App\Filament\Resources\TourismServiceProviderResource\Pages;
use App\Models\TourismServiceProvider;
use App\Models\User;
use App\Mail\ProviderStatusMail;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ViewField;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class TourismServiceProviderResource extends Resource
{
    use HasSectionPermission;
    protected static ?string $model = TourismServiceProvider::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;
    protected static ?string $navigationLabel = 'Prestadores turísticos';
    protected static ?string $modelLabel = 'prestador turístico';
    protected static ?string $pluralModelLabel = 'prestadores turísticos';
    protected static ?string $recordTitleAttribute = 'commercial_name';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Estado del expediente')->schema([
                Select::make('status')->label('Estado')->options(['pending' => 'Pendiente', 'reviewing' => 'En revisión', 'approved' => 'Dar de alta', 'rejected' => 'No aprobado', 'suspended' => 'Dar de baja'])->required(),
                Textarea::make('admin_notes')->label('Observaciones para el prestador')->helperText('Se incluirán en el correo de notificación.')->columnSpanFull(),
            ])->columns(2),
            Section::make('Cuenta y página pública')->schema([
                Select::make('user_id')->label('Usuario del prestador')->options(fn () => User::query()->where('role', 'provider')->orderBy('email')->pluck('email', 'id'))->searchable()->preload()->helperText('Permite asignar una cuenta a prestadores registrados anteriormente.'),
                TextInput::make('attraction_place_id')->label('ID de ficha en el mapa')->disabled()->helperText('Se genera cuando el prestador envía su página.'),
            ])->columns(2),
            Section::make('Datos generales')->schema([
                Select::make('provider_type')->label('Tipo')->options(self::providerTypes())->required(),
                TextInput::make('provider_type_other')->label('Otro tipo'),
                TextInput::make('commercial_name')->label('Nombre comercial')->required(),
                TextInput::make('business_name')->label('Razón social'),
                TextInput::make('nit')->label('NIT'),
                TextInput::make('legal_representative')->label('Representante legal')->required(),
                TextInput::make('identity_document')->label('Documento de identidad')->required(),
                Toggle::make('has_tourism_license')->label('Cuenta con licencia turística'),
                DatePicker::make('tourism_license_issued_at')->label('Emisión de licencia'),
                DatePicker::make('tourism_license_renewed_at')->label('Renovación de licencia'),
            ])->columns(2),
            Section::make('Contacto y ubicación')->schema([
                TextInput::make('landline')->label('Teléfono fijo'),
                TextInput::make('whatsapp')->label('WhatsApp')->required(),
                TextInput::make('email')->email()->required(),
                TextInput::make('website')->label('Página web'),
                TextInput::make('facebook'), TextInput::make('instagram'), TextInput::make('tiktok'),
                TextInput::make('x_url')->label('X (antes Twitter)')->url(),
                TextInput::make('youtube_url')->label('YouTube')->url(),
                TextInput::make('other_social_network')->label('Otra red social'),
                TextInput::make('department')->label('Departamento')->required(),
                TextInput::make('municipality')->label('Municipio')->required(),
                TextInput::make('address')->label('Dirección')->required(),
                Textarea::make('maps_location')->label('Coordenadas / Google Maps')->columnSpanFull(),
            ])->columns(2),
            Section::make('Información específica del servicio')->schema([
                TextInput::make('lodging_type')->label('Tipo de hospedaje'),
                TextInput::make('room_count')->numeric()->label('Habitaciones'),
                TextInput::make('guest_capacity')->numeric()->label('Capacidad de huéspedes'),
                TextInput::make('agency_type')->label('Tipo de agencia'),
                TextInput::make('package_types')->label('Tipos de paquetes'),
                Textarea::make('main_destinations')->label('Principales destinos'),
                Toggle::make('has_guide_credential')->label('Cuenta con credencial de guía'),
                DatePicker::make('guide_credential_issued_at')->label('Emisión de credencial'),
                DatePicker::make('guide_credential_renewed_at')->label('Renovación de credencial'),
                TextInput::make('experience_years')->numeric()->label('Años de experiencia'),
                TextInput::make('lodging_services')->formatStateUsing(fn ($state) => is_array($state) ? implode(', ', $state) : $state)->disabled()->label('Servicios de hospedaje'),
                TextInput::make('agency_services')->formatStateUsing(fn ($state) => is_array($state) ? implode(', ', $state) : $state)->disabled()->label('Servicios de agencia'),
                TextInput::make('tourism_modalities')->formatStateUsing(fn ($state) => is_array($state) ? implode(', ', $state) : $state)->disabled()->label('Modalidades'),
                TextInput::make('languages')->formatStateUsing(fn ($state) => is_array($state) ? implode(', ', $state) : $state)->disabled()->label('Idiomas'),
                TextInput::make('specialties')->formatStateUsing(fn ($state) => is_array($state) ? implode(', ', $state) : $state)->disabled()->label('Especialidades'),
            ])->columns(2)->collapsed(),
            Section::make('Declaración y solicitud')->schema([
                TextInput::make('applicant_name')->label('Solicitante')->required(),
                TextInput::make('application_place')->label('Lugar')->required(),
                DatePicker::make('application_date')->label('Fecha')->required(),
                Toggle::make('declaration_accepted')->label('Declaración aceptada')->disabled(),
                ViewField::make('documents')->label('Documentos cargados')->view('filament.forms.provider-documents')->columnSpanFull(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('commercial_name')->label('Nombre comercial')->searchable()->sortable(),
            TextColumn::make('provider_type')->label('Tipo')->formatStateUsing(fn ($state) => self::providerTypes()[$state] ?? $state)->badge(),
            TextColumn::make('services_provided')
                ->label('Servicios que presta')
                ->state(fn (TourismServiceProvider $record): array => $record->servicesProvided())
                ->badge()
                ->color('info')
                ->placeholder('No especificado')
                ->wrap()
                ->toggleable(),
            TextColumn::make('municipality')->label('Municipio')->searchable()->sortable(),
            TextColumn::make('legal_representative')->label('Representante')->searchable()->toggleable(),
            TextColumn::make('whatsapp')->label('WhatsApp')->searchable()->toggleable(),
            TextColumn::make('email')->searchable()->toggleable(),
            TextColumn::make('documents')->label('Documentos')->state(fn (TourismServiceProvider $record): string => count($record->documents ?? []).' archivo(s)')->badge()->color(fn (TourismServiceProvider $record): string => count($record->documents ?? []) > 0 ? 'success' : 'gray'),
            TextColumn::make('status')->label('Estado')->formatStateUsing(fn ($state) => ['pending'=>'Pendiente','reviewing'=>'En revisión','approved'=>'Dado de alta','rejected'=>'No aprobado','suspended'=>'Dado de baja'][$state] ?? $state)->badge()->color(fn ($state) => ['pending'=>'warning','reviewing'=>'info','approved'=>'success','rejected'=>'danger','suspended'=>'gray'][$state] ?? 'gray'),
            TextColumn::make('created_at')->label('Registrado')->dateTime('d/m/Y H:i')->sortable(),
        ])->filters([
            SelectFilter::make('provider_type')->label('Tipo')->options(self::providerTypes()),
            SelectFilter::make('status')->label('Estado')->options(['pending'=>'Pendiente','reviewing'=>'En revisión','approved'=>'Dado de alta','rejected'=>'No aprobado','suspended'=>'Dado de baja']),
        ])->recordActions([
            Action::make('create_access')
                ->label('Crear acceso')
                ->icon(Heroicon::OutlinedKey)
                ->color('success')
                ->visible(fn (TourismServiceProvider $record): bool => $record->status === 'approved' && ! $record->user_id)
                ->requiresConfirmation()
                ->action(function (TourismServiceProvider $record): void {
                    $password = Str::password(14);
                    $user = User::query()->where('email', $record->email)->first() ?: User::create([
                        'name' => $record->legal_representative,
                        'email' => $record->email,
                        'password' => $password,
                        'role' => 'provider',
                        'is_admin' => false,
                    ]);
                    abort_unless($user->role === 'provider' && ! $user->tourismServiceProvider, 422, 'El correo ya pertenece a otra cuenta.');
                    $user->update(['password' => $password]);
                    $record->updateQuietly(['user_id' => $user->id]);
                    Mail::to($record->email)->send(new ProviderStatusMail($record->fresh(), $password));
                }),
            Action::make('documents')
                ->label('Ver documentación')
                ->icon(Heroicon::OutlinedFolderOpen)
                ->color('info')
                ->modalHeading(fn (TourismServiceProvider $record): string => 'Documentación de '.$record->commercial_name)
                ->modalContent(fn (TourismServiceProvider $record) => view('filament.provider-documents-modal', ['provider' => $record]))
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Cerrar'),
            EditAction::make()->label('Ver expediente'),
            DeleteAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTourismServiceProviders::route('/'),
            'edit' => Pages\EditTourismServiceProvider::route('/{record}/edit'),
        ];
    }

    private static function providerTypes(): array
    {
        return ['hospedaje'=>'Hospedaje','gastronomia'=>'Gastronomía','agencia_viajes'=>'Agencia de viajes','operadora_turismo'=>'Operadora de turismo','guia_departamental'=>'Guía departamental','transporte'=>'Transporte turístico','artesania_comercio'=>'Artesanía o comercio turístico','actividad_turistica'=>'Actividad recreativa o turística','otro'=>'Otro'];
    }
}
