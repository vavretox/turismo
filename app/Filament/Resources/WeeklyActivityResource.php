<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\HasSectionPermission;
use App\Filament\Resources\WeeklyActivityResource\Pages;
use App\Models\WeeklyActivity;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ViewField;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class WeeklyActivityResource extends Resource
{
    use HasSectionPermission;
    protected static ?string $model = WeeklyActivity::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMegaphone;
    protected static ?string $navigationLabel = 'Actividad de la semana';
    protected static ?string $modelLabel = 'actividad semanal';
    protected static ?string $pluralModelLabel = 'actividades de la semana';
    protected static ?string $recordTitleAttribute = 'titulo';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Contenido principal')->schema([
                TextInput::make('titulo')->required()->maxLength(255),
                TextInput::make('subtitulo')->maxLength(255),
                ViewField::make('vista_previa_imagen')->label('Fotografía principal')->view('filament.forms.weekly-activity-image-preview')->dehydrated(false)->columnSpanFull(),
                Textarea::make('descripcion')->label('Resumen')->rows(4)->columnSpanFull(),
                Textarea::make('contenido')->label('Información detallada')->rows(12)->columnSpanFull(),
            ])->columns(2)->columnSpanFull(),

            Section::make('Sectores y puntos de interés')->schema([
                Repeater::make('sectores_interes')->label('')
                    ->schema([
                        TextInput::make('titulo')->label('Nombre')->required()->maxLength(255),
                        TextInput::make('icono')->label('Icono')->placeholder('fa-landmark')->maxLength(80),
                        Textarea::make('descripcion')->label('Descripción')->rows(3)->columnSpanFull(),
                        TextInput::make('enlace')->label('Enlace opcional')->maxLength(1000)->columnSpanFull(),
                    ])->defaultItems(0)->addActionLabel('Añadir sector de interés')->reorderable()->collapsible()
                    ->itemLabel(fn (array $state): ?string => $state['titulo'] ?? 'Nuevo sector')->columns(2)->columnSpanFull(),
            ])->columnSpanFull(),

            Section::make('Galería de fotografías')->schema([
                ViewField::make('galeria_actividad')->label('')->view('filament.forms.weekly-activity-gallery')->dehydrated(false)->columnSpanFull(),
            ])->columnSpanFull(),

            Section::make('Ubicación, mapa y horarios')->schema([
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
                    ->helperText('La actividad se mostrará y buscará dentro de este municipio.'),
                TextInput::make('lugar')->maxLength(255),
                TextInput::make('direccion')->label('Dirección')->maxLength(255),
                Textarea::make('horarios')->label('Horarios e indicaciones')->rows(3)->columnSpanFull(),
                TextInput::make('mapa_url')->label('Enlace de Google Maps')->placeholder('https://www.google.com/maps/...')->maxLength(1000)->columnSpanFull(),
                DateTimePicker::make('fecha_actividad')->label('Fecha de la actividad')->seconds(false),
            ])->columns(2)->columnSpanFull(),

            Section::make('Contacto y redes sociales')->schema([
                TextInput::make('telefono')->label('Teléfono')->tel()->maxLength(80),
                TextInput::make('whatsapp')->label('WhatsApp')->tel()->maxLength(80),
                TextInput::make('correo')->label('Correo electrónico')->email()->maxLength(255),
                TextInput::make('sitio_web')->label('Sitio web')->url()->maxLength(1000),
                TextInput::make('facebook')->label('Facebook')->url()->maxLength(1000),
                TextInput::make('instagram')->label('Instagram')->url()->maxLength(1000),
                TextInput::make('x_url')->label('X (antes Twitter)')->url()->maxLength(1000),
                TextInput::make('youtube_url')->label('YouTube')->url()->maxLength(1000),
            ])->columns(2)->columnSpanFull(),

            Section::make('Publicación del anuncio')->schema([
                DateTimePicker::make('visible_desde')->label('Mostrar desde')->seconds(false),
                DateTimePicker::make('visible_hasta')->label('Mostrar hasta')->seconds(false),
                TextInput::make('texto_boton')
                    ->label('Texto del botón')
                    ->default('Ver información de la actividad')
                    ->disabled()
                    ->dehydrated(false)
                    ->helperText('El botón siempre mostrará “Ver información de la actividad”.'),
                TextInput::make('enlace')->label('Enlace externo opcional')->maxLength(255),
                TextInput::make('orden')->numeric()->default(0),
                Toggle::make('activo')->default(true),
            ])->columns(2)->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            ImageColumn::make('imagen')->label('Imagen')->disk('public')->height(60)->width(90),
            TextColumn::make('titulo')->searchable()->sortable(),
            TextColumn::make('municipio.nombre')->label('Municipio')->searchable()->sortable(),
            TextColumn::make('fecha_actividad')->label('Actividad')->dateTime('d/m/Y H:i')->sortable(),
            TextColumn::make('visible_hasta')->label('Visible hasta')->dateTime('d/m/Y H:i'),
            TextColumn::make('orden')->sortable(),
            IconColumn::make('activo')->boolean(),
        ])->filters([
            SelectFilter::make('municipio_id')
                ->label('Municipio')
                ->relationship('municipio', 'nombre')
                ->searchable()
                ->preload(),
        ])->recordActions([EditAction::make(), DeleteAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWeeklyActivities::route('/'),
            'create' => Pages\CreateWeeklyActivity::route('/create'),
            'edit' => Pages\EditWeeklyActivity::route('/{record}/edit'),
        ];
    }
}
