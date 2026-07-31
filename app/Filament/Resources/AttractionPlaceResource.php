<?php
namespace App\Filament\Resources;
use App\Filament\Concerns\HasSectionPermission;
use App\Filament\Resources\AttractionPlaceResource\Pages; use App\Models\AttractionPlace; use BackedEnum;
use Filament\Actions\{BulkActionGroup,DeleteAction,DeleteBulkAction,EditAction}; use Filament\Forms\Components\{FileUpload,Select,TagsInput,Textarea,TextInput,Toggle}; use Filament\Resources\Resource; use Filament\Schemas\Schema; use Filament\Support\Icons\Heroicon; use Filament\Tables\Columns\{IconColumn,ImageColumn,TextColumn}; use Filament\Tables\Table;
class AttractionPlaceResource extends Resource {
    use HasSectionPermission;
    protected static ?string $model=AttractionPlace::class; protected static string|BackedEnum|null $navigationIcon=Heroicon::OutlinedMapPin; protected static ?string $navigationLabel='Lugares del mapa'; protected static ?string $modelLabel='lugar de interés'; protected static ?string $recordTitleAttribute='titulo';
    public static function form(Schema $schema): Schema { return $schema->components([
        Select::make('attraction_type_id')->label('Tipo de atractivo')->relationship('type','nombre')->searchable()->preload()->required(),
        TextInput::make('titulo')->required()->maxLength(255),TextInput::make('slug')->maxLength(255),
        FileUpload::make('imagen')->label('Imagen principal')->image()->disk('public')->directory('lugares-mapa')->visibility('public')->imageEditor()->imagePreviewHeight('240')->maxSize(5120)->columnSpanFull(),
        Textarea::make('resumen')->rows(3)->columnSpanFull(),Textarea::make('descripcion')->rows(6)->columnSpanFull(),
        TextInput::make('latitud')->numeric()->required()->step('0.0000001')->default('-21.5355')->helperText('Cercado, Tarija: aproximadamente -21.5355'),
        TextInput::make('longitud')->numeric()->required()->step('0.0000001')->default('-64.7296')->helperText('Cercado, Tarija: aproximadamente -64.7296'),
        TextInput::make('direccion')->maxLength(255),TextInput::make('telefono')->tel()->maxLength(100),TextInput::make('sitio_web')->url()->maxLength(255),
        TextInput::make('facebook')->url()->maxLength(255),TextInput::make('instagram')->url()->maxLength(255),TextInput::make('tiktok')->url()->maxLength(255),
        TextInput::make('x_url')->label('X (antes Twitter)')->url()->maxLength(255),TextInput::make('youtube_url')->label('YouTube')->url()->maxLength(255),
        TextInput::make('horario')->maxLength(255),TextInput::make('precio')->maxLength(100),
        TagsInput::make('room_options')->label('Tipos de habitación o cama')->suggestions(['Individual', 'Matrimonial', 'Dos camas', 'Familiar', 'Habitaciones múltiples'])->helperText('Úsalo únicamente en alojamientos.'),
        TextInput::make('orden')->numeric()->default(0),Toggle::make('destacado')->default(false),Toggle::make('activo')->default(true),
    ]); }
    public static function table(Table $table): Table { return $table->columns([ImageColumn::make('imagen')->label('Imagen')->disk('public')->square(),TextColumn::make('titulo')->searchable()->sortable(),TextColumn::make('serviceProvider.commercial_name')->label('Prestador')->placeholder('Carga institucional')->badge(),TextColumn::make('type.nombre')->label('Tipo')->sortable(),TextColumn::make('latitud')->label('Latitud'),TextColumn::make('longitud')->label('Longitud'),IconColumn::make('destacado')->boolean(),IconColumn::make('activo')->label('Publicado')->boolean()])->recordActions([EditAction::make(),DeleteAction::make()])->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]); }
    public static function getPages(): array{return ['index'=>Pages\ListAttractionPlaces::route('/'),'create'=>Pages\CreateAttractionPlace::route('/create'),'edit'=>Pages\EditAttractionPlace::route('/{record}/edit')];}
}
