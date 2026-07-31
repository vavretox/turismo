<?php
namespace App\Filament\Resources;
use App\Filament\Concerns\HasSectionPermission;
use App\Filament\Resources\AttractionTypeResource\Pages;
use App\Models\AttractionType;
use BackedEnum;
use Filament\Actions\{BulkActionGroup,DeleteAction,DeleteBulkAction,EditAction};
use Filament\Forms\Components\{ColorPicker,Select,Textarea,TextInput,Toggle};
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\{ColorColumn,IconColumn,TextColumn};
use Filament\Tables\Table;
class AttractionTypeResource extends Resource {
    use HasSectionPermission;
    protected static ?string $model=AttractionType::class;
    protected static string|BackedEnum|null $navigationIcon=Heroicon::OutlinedTag;
    protected static ?string $navigationLabel='Tipos de atractivo';
    protected static ?string $modelLabel='tipo de atractivo';
    protected static ?string $recordTitleAttribute='nombre';
    public static function form(Schema $schema): Schema { return $schema->components([
        Select::make('parent_id')->label('Categoría superior')->relationship('parent','nombre')->searchable()->preload()->helperText('Ejemplo: Hoteles puede depender de Alojamiento. Déjalo vacío para una categoría principal.'),
        TextInput::make('nombre')->required()->maxLength(255), TextInput::make('slug')->maxLength(255),
        Select::make('icono')->label('Icono')->options(['fa-location-dot'=>'Ubicación','fa-hotel'=>'Hotel','fa-bed'=>'Alojamiento','fa-utensils'=>'Restaurante','fa-wine-glass'=>'Vino y bodegas','fa-landmark'=>'Cultura','fa-tree'=>'Naturaleza','fa-mountain-sun'=>'Aventura','fa-church'=>'Iglesias','fa-cart-shopping'=>'Compras','fa-mug-hot'=>'Cafetería'])->default('fa-location-dot'),
        ColorPicker::make('color')->default('#991b1b'),
        Textarea::make('descripcion')->rows(3)->columnSpanFull(),
        Textarea::make('que_hacer')->label('¿Qué se puede hacer?')->helperText('Describe actividades disponibles dentro de este tipo de atractivo.')->rows(5)->columnSpanFull(),
        TextInput::make('orden')->numeric()->default(0), Toggle::make('activo')->default(true),
    ]); }
    public static function table(Table $table): Table { return $table->columns([ColorColumn::make('color'),TextColumn::make('nombre')->searchable()->sortable(),TextColumn::make('parent.nombre')->label('Categoría superior'),TextColumn::make('places_count')->counts('places')->label('Lugares'),TextColumn::make('orden')->sortable(),IconColumn::make('activo')->boolean()])->recordActions([EditAction::make(),DeleteAction::make()])->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]); }
    public static function getPages(): array { return ['index'=>Pages\ListAttractionTypes::route('/'),'create'=>Pages\CreateAttractionType::route('/create'),'edit'=>Pages\EditAttractionType::route('/{record}/edit')]; }
}
