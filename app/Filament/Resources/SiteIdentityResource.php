<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\HasSectionPermission;
use App\Filament\Resources\SiteIdentityResource\Pages;
use App\Models\SiteIdentity;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\ViewField;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SiteIdentityResource extends Resource
{
    use HasSectionPermission;
    protected static ?string $model = SiteIdentity::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;
    protected static ?string $navigationLabel = 'Identidad del sitio';
    protected static ?string $modelLabel = 'identidad del sitio';
    protected static ?string $pluralModelLabel = 'identidad del sitio';
    protected static ?string $recordTitleAttribute = 'nombre';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('clave', 'main');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('nombre')
                ->label('Nombre institucional')
                ->required()
                ->maxLength(255)
                ->default('Secretaria Departamental de Turismo - GADT')
                ->columnSpanFull(),
            TextInput::make('clave')
                ->default('main')
                ->required()
                ->disabled()
                ->dehydrated()
                ->unique(ignoreRecord: true)
                ->maxLength(255),
            FileUpload::make('logo')
                ->label('Logo o icono')
                ->image()
                ->disk('public')
                ->directory('identidad')
                ->visibility('public')
                ->imageEditor()
                ->imagePreviewHeight('180')
                ->maxSize(2048)
                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                ->columnSpanFull(),
            Textarea::make('descripcion')->rows(3)->columnSpanFull(),
            Section::make('Redes sociales oficiales')
                ->description('Estos enlaces alimentan los botones flotantes y los del pie de página. Deja vacío lo que no quieras mostrar.')
                ->schema([
                    TextInput::make('facebook_url')->label('Facebook')->url()->maxLength(1000)->placeholder('https://www.facebook.com/tu-pagina'),
                    TextInput::make('instagram_url')->label('Instagram')->url()->maxLength(1000)->placeholder('https://www.instagram.com/tu-cuenta'),
                    TextInput::make('x_url')->label('X (Twitter)')->url()->maxLength(1000)->placeholder('https://x.com/tu-cuenta'),
                    TextInput::make('youtube_url')->label('YouTube')->url()->maxLength(1000)->placeholder('https://www.youtube.com/@tu-canal'),
                    TextInput::make('tiktok_url')->label('TikTok')->url()->maxLength(1000)->placeholder('https://www.tiktok.com/@tu-cuenta'),
                    TextInput::make('whatsapp_url')->label('WhatsApp')->url()->maxLength(1000)->placeholder('https://wa.me/591XXXXXXXX'),
                ])
                ->columns(2)
                ->columnSpanFull(),
            ViewField::make('vista_tipografica')
                ->label('Catálogo y vista previa de fuentes')
                ->view('filament.forms.typography-preview')
                ->dehydrated(false)
                ->columnSpanFull(),
            Select::make('fuente_texto')
                ->label('Fuente de textos')
                ->options(self::fontOptions())
                ->default('Inter')->required()->helperText('Selecciona una fuente para párrafos, menús y contenido.'),
            Select::make('fuente_titulos')
                ->label('Fuente de títulos')
                ->options(self::fontOptions())
                ->default('Montserrat')->required()->helperText('Se aplicará a todos los encabezados y títulos.'),
            TextInput::make('tamano_texto')
                ->label('Tamaño general del texto')
                ->numeric()->minValue(12)->maxValue(24)->step(1)->suffix('px')
                ->default(16)->required()->helperText('Puedes escribir cualquier tamaño entre 12 y 24 píxeles.'),
            Select::make('peso_texto')
                ->label('Grosor del texto')
                ->options([300 => 'Ligero', 400 => 'Normal', 500 => 'Medio', 600 => 'Seminegrita'])
                ->default(400)->required(),
            Select::make('peso_titulos')
                ->label('Grosor de títulos')
                ->options([500 => 'Medio', 600 => 'Seminegrita', 700 => 'Negrita', 800 => 'Extra negrita', 900 => 'Máxima negrita'])
                ->default(800)->required(),
            Select::make('peso_botones')
                ->label('Grosor de botones y enlaces')
                ->options([400 => 'Normal', 500 => 'Medio', 600 => 'Seminegrita', 700 => 'Negrita', 800 => 'Extra negrita'])
                ->default(700)->required(),
            Select::make('espaciado_titulos')
                ->label('Espaciado entre letras de títulos')
                ->options(['-0.03' => 'Cerrado', '0' => 'Normal', '0.02' => 'Amplio', '0.05' => 'Muy amplio'])
                ->default('0')->required()->columnSpanFull(),
        ]);
    }

    private static function fontOptions(): array
    {
        return [
            'Inter' => 'Inter — moderna y limpia',
            'Montserrat' => 'Montserrat — institucional y fuerte',
            'Poppins' => 'Poppins — geométrica y amigable',
            'Roboto' => 'Roboto — clara y versátil',
            'Nunito' => 'Nunito — redondeada y cercana',
            'Open Sans' => 'Open Sans — muy legible',
            'Lora' => 'Lora — elegante con serifas',
            'Playfair Display' => 'Playfair Display — editorial y turística',
            'Raleway' => 'Raleway — fina y contemporánea',
            'Merriweather' => 'Merriweather — clásica y legible',
            'Oswald' => 'Oswald — alta y contundente',
            'Quicksand' => 'Quicksand — suave y moderna',
            'Rubik' => 'Rubik — dinámica y urbana',
            'Ubuntu' => 'Ubuntu — tecnológica y humana',
            'Bebas Neue' => 'Bebas Neue — títulos de impacto',
            'Dancing Script' => 'Dancing Script — manuscrita',
            'Pacifico' => 'Pacifico — artesanal y expresiva',
        ];
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('logo')->label('Logo')->disk('public')->height(56)->width(56),
                TextColumn::make('nombre')->searchable(),
                TextColumn::make('clave'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSiteIdentities::route('/'),
            'edit' => Pages\EditSiteIdentity::route('/{record}/edit'),
        ];
    }
}
