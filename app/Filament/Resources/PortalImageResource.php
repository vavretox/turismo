<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\HasSectionPermission;
use App\Filament\Resources\PortalImageResource\Pages;
use App\Models\PortalImage;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
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
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PortalImageResource extends Resource
{
    use HasSectionPermission;
    protected static ?string $model = PortalImage::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;
    protected static ?string $navigationLabel = 'Imagenes del portal';
    protected static ?string $modelLabel = 'imagen de portada';
    protected static ?string $pluralModelLabel = 'imagen de portada';
    protected static ?string $recordTitleAttribute = 'nombre';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('clave', 'like', 'home_hero_%');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('nombre')->label('Título de la portada')->required()->maxLength(255)->default('Explora Tarija'),
            Select::make('clave')
                ->label('Posición en el recorrido')
                ->options([
                    'home_hero_1' => 'Portada 1',
                    'home_hero_2' => 'Portada 2',
                    'home_hero_3' => 'Portada 3',
                    'home_hero_4' => 'Portada 4',
                    'home_hero_5' => 'Portada 5',
                ])
                ->required()
                ->unique(ignoreRecord: true)
                ->helperText('Puedes configurar hasta cinco fondos, uno por cada posición.'),
            FileUpload::make('imagen')
                ->label('Imagen de portada')
                ->required()
                ->image()
                ->disk('public')
                ->directory('portal')
                ->visibility('public')
                ->imageEditor()
                ->imagePreviewHeight('260')
                ->maxSize(4096)
                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                ->columnSpanFull(),
            Textarea::make('descripcion')->label('Texto de la portada')->required()->rows(3)->columnSpanFull(),
            Toggle::make('activo')->default(true),
            TextInput::make('orden')->numeric()->default(1),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('imagen')->label('Imagen')->disk('public')->height(64)->width(120),
                TextColumn::make('nombre')->searchable()->sortable(),
                TextColumn::make('clave')->searchable()->sortable(),
                TextColumn::make('orden')->sortable(),
                IconColumn::make('activo')->boolean(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPortalImages::route('/'),
            'create' => Pages\CreatePortalImage::route('/create'),
            'edit' => Pages\EditPortalImage::route('/{record}/edit'),
        ];
    }
}
