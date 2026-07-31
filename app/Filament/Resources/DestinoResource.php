<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\HasSectionPermission;
use App\Filament\Resources\DestinoResource\Pages;
use App\Models\Destino;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class DestinoResource extends Resource
{
    use HasSectionPermission;
    protected static ?string $model = Destino::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMapPin;
    protected static ?string $recordTitleAttribute = 'nombre';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('categoria_id')->relationship('categoria', 'nombre')->searchable()->preload(),
            Select::make('municipio_id')
                ->label('Municipio al que pertenece')
                ->relationship(
                    'municipio',
                    'nombre',
                    modifyQueryUsing: fn ($query) => $query->where('activo', true)->orderBy('orden')->orderBy('nombre'),
                )
                ->getOptionLabelFromRecordUsing(fn ($record): string => $record->nombre.' · '.$record->provincia)
                ->searchable(['nombre', 'provincia'])
                ->preload()
                ->required()
                ->helperText('Este dato permite encontrar el destino por municipio y utilizarlo correctamente en Inspírame.'),
            TextInput::make('nombre')->required()->maxLength(255),
            TextInput::make('slug')->maxLength(255),
            TextInput::make('subtitulo')
                ->label('Frase de portada')
                ->helperText('Una frase breve y evocadora que aparecerá sobre la fotografía principal.')
                ->maxLength(255)
                ->columnSpanFull(),
            TextInput::make('ubicacion')->label('Ubicación')->maxLength(255),
            FileUpload::make('imagen')
                ->label('Imagen principal')
                ->helperText('Esta fotografía se utilizará en la portada del destino y en las tarjetas del portal.')
                ->image()
                ->disk('public')
                ->directory('destinos')
                ->visibility('public')
                ->imageEditor()
                ->imagePreviewHeight('220')
                ->maxSize(4096)
                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                ->columnSpanFull(),
            FileUpload::make('imagenes_secundarias')
                ->label('Banco de fotografías')
                ->helperText('Sube varias imágenes para la galería del destino. Puedes arrastrarlas para cambiar su orden.')
                ->image()
                ->multiple()
                ->reorderable()
                ->appendFiles()
                ->disk('public')
                ->directory('destinos/galeria')
                ->visibility('public')
                ->imageEditor()
                ->imagePreviewHeight('160')
                ->maxSize(5120)
                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                ->columnSpanFull(),
            TextInput::make('precio')->numeric()->prefix('$'),
            TextInput::make('latitud')->numeric(),
            TextInput::make('longitud')->numeric(),
            Textarea::make('resumen')
                ->helperText('Resumen utilizado en las tarjetas y resultados de búsqueda.')
                ->rows(3)
                ->columnSpanFull(),
            Textarea::make('introduccion')
                ->label('Introducción editorial')
                ->helperText('Primer texto que recibe al visitante en la página del destino.')
                ->rows(4)
                ->columnSpanFull(),
            Textarea::make('descripcion')
                ->label('Historia y experiencia')
                ->rows(8)
                ->columnSpanFull(),
            Textarea::make('como_llegar')
                ->label('Cómo llegar')
                ->helperText('Introducción general antes de mostrar las rutas disponibles.')
                ->rows(5)
                ->columnSpanFull(),
            Repeater::make('rutas_llegada')
                ->label('Rutas para llegar')
                ->helperText('Crea alternativas desde distintas ciudades. Cada tramo puede tener su propio transporte, duración y coordenadas.')
                ->schema([
                    TextInput::make('nombre')->label('Nombre de la ruta')->placeholder('Ruta desde La Paz')->required()->maxLength(255),
                    TextInput::make('origen')->label('Punto de origen')->placeholder('La Paz')->required()->maxLength(255),
                    TextInput::make('origen_latitud')->label('Latitud del origen')->numeric(),
                    TextInput::make('origen_longitud')->label('Longitud del origen')->numeric(),
                    Textarea::make('descripcion')->label('Descripción breve')->rows(2)->columnSpanFull(),
                    Repeater::make('tramos')
                        ->label('Puntos de control del recorrido')
                        ->helperText('Cada tramo se mostrará como un waypoint numerado en el mapa. Completa las coordenadas de llegada de todos los puntos.')
                        ->schema([
                            TextInput::make('desde')->label('Desde')->required()->maxLength(255),
                            TextInput::make('hasta')->label('Hasta')->required()->maxLength(255),
                            Select::make('medio')
                                ->label('Medio')
                                ->options([
                                    'avion' => 'Avión',
                                    'bus' => 'Bus',
                                    'auto' => 'Automóvil',
                                    'caminata' => 'Caminata',
                                    'bicicleta' => 'Bicicleta',
                                    'otro' => 'Otro',
                                ])
                                ->required(),
                            TextInput::make('duracion')->label('Duración aproximada')->placeholder('2 h 30 min')->maxLength(100),
                            TextInput::make('latitud')->label('Latitud de llegada')->numeric(),
                            TextInput::make('longitud')->label('Longitud de llegada')->numeric(),
                            TextInput::make('indicaciones')->label('Indicaciones adicionales')->maxLength(500)->columnSpanFull(),
                        ])
                        ->defaultItems(1)
                        ->addActionLabel('Añadir punto de control')
                        ->reorderable()
                        ->collapsible()
                        ->itemLabel(fn (array $state): ?string => filled($state['desde'] ?? null) ? ($state['desde'].' → '.($state['hasta'] ?? '')) : 'Nuevo tramo')
                        ->columns(2)
                        ->columnSpanFull(),
                ])
                ->defaultItems(0)
                ->addActionLabel('Añadir alternativa de ruta')
                ->reorderable()
                ->collapsible()
                ->itemLabel(fn (array $state): ?string => $state['nombre'] ?? 'Nueva ruta')
                ->columns(2)
                ->columnSpanFull(),
            TextInput::make('mejor_epoca')->label('Mejor época para visitar')->maxLength(255),
            TextInput::make('duracion_recomendada')->label('Duración recomendada')->maxLength(255),
            Textarea::make('recomendaciones')
                ->label('Recomendaciones para el viajero')
                ->helperText('Escribe una recomendación por línea.')
                ->rows(5)
                ->columnSpanFull(),
            Toggle::make('destacado')->default(false),
            Toggle::make('activo')->default(true),
            TextInput::make('orden')->numeric()->default(0),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('imagen')->label('Imagen')->disk('public')->square(),
                TextColumn::make('nombre')->searchable()->sortable(),
                TextColumn::make('categoria.nombre')->label('Categoria')->sortable(),
                TextColumn::make('municipio.nombre')->label('Municipio')->searchable()->sortable(),
                TextColumn::make('ubicacion')->searchable(),
                IconColumn::make('destacado')->boolean(),
                IconColumn::make('activo')->boolean(),
            ])
            ->filters([
                SelectFilter::make('municipio_id')
                    ->label('Municipio')
                    ->relationship('municipio', 'nombre')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDestinos::route('/'),
            'create' => Pages\CreateDestino::route('/create'),
            'edit' => Pages\EditDestino::route('/{record}/edit'),
        ];
    }
}
