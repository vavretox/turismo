<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\HasSectionPermission;
use App\Filament\Resources\ProvinciaTuristicaResource\Pages;
use App\Models\ProvinciaTuristica;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
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

class ProvinciaTuristicaResource extends Resource
{
    use HasSectionPermission;
    protected static ?string $model = ProvinciaTuristica::class;
    protected static ?string $slug = 'municipios-turisticos';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMap;
    protected static ?string $navigationLabel = 'Municipios turísticos';
    protected static ?string $modelLabel = 'municipio turístico';
    protected static ?string $pluralModelLabel = 'municipios turísticos';
    protected static ?string $recordTitleAttribute = 'nombre';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('nombre')->required()->maxLength(255),
            TextInput::make('slug')->maxLength(255),
            TextInput::make('provincia')->required()->maxLength(255),
            TextInput::make('subtitulo')->maxLength(255)->columnSpanFull(),
            FileUpload::make('imagen')
                ->label('Imagen principal')
                ->image()
                ->disk('public')
                ->directory('municipios')
                ->visibility('public')
                ->imageEditor()
                ->imagePreviewHeight('220')
                ->columnSpanFull(),
            FileUpload::make('imagenes_secundarias')
                ->label('Imágenes secundarias')
                ->helperText('Puedes subir varias imágenes sin límite de tamaño. Arrástralas para cambiar el orden en que aparecerán.')
                ->image()
                ->multiple()
                ->reorderable()
                ->appendFiles()
                ->disk('public')
                ->directory('municipios/galeria')
                ->visibility('public')
                ->imageEditor()
                ->imagePreviewHeight('160')
                ->columnSpanFull(),
            Textarea::make('resumen')->rows(3)->columnSpanFull(),
            Textarea::make('descripcion')->rows(7)->columnSpanFull(),
            Textarea::make('atractivos')
                ->helperText('Escribe un atractivo por linea.')
                ->rows(6),
            Textarea::make('fiestas')
                ->helperText('Escribe una fiesta o experiencia por linea.')
                ->rows(5),
            Textarea::make('recomendaciones')
                ->helperText('Escribe una recomendacion por linea.')
                ->rows(5),
            TextInput::make('orden')->numeric()->default(0),
            Toggle::make('activo')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('imagen')->label('Imagen')->disk('public')->square(),
                TextColumn::make('nombre')->searchable()->sortable(),
                TextColumn::make('provincia')->searchable()->sortable(),
                TextColumn::make('slug')->searchable(),
                TextColumn::make('orden')->sortable(),
                IconColumn::make('activo')->boolean(),
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
            'index' => Pages\ListProvinciaTuristicas::route('/'),
            'create' => Pages\CreateProvinciaTuristica::route('/create'),
            'edit' => Pages\EditProvinciaTuristica::route('/{record}/edit'),
        ];
    }
}
