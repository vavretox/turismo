<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\HasSectionPermission;
use App\Filament\Resources\VirtualTourResource\Pages;
use App\Models\VirtualTour;
use BackedEnum;
use Filament\Actions\{BulkActionGroup, DeleteAction, DeleteBulkAction, EditAction};
use Filament\Forms\Components\{FileUpload, Repeater, Textarea, TextInput, Toggle};
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\{IconColumn, ImageColumn, TextColumn};
use Filament\Tables\Table;

class VirtualTourResource extends Resource
{
    use HasSectionPermission;

    protected static ?string $model = VirtualTour::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;
    protected static ?string $navigationLabel = 'Fotografías 360°';
    protected static ?string $modelLabel = 'tour 360°';
    protected static ?string $pluralModelLabel = 'tours 360°';
    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Información del tour')->schema([
                TextInput::make('name')->label('Nombre')->required()->maxLength(255),
                Textarea::make('description')->label('Descripción')->rows(4)->columnSpanFull(),
                FileUpload::make('cover_image')->label('Imagen de portada')->image()->disk('public')
                    ->directory('tours-360/portadas')->visibility('public')->imageEditor()->maxSize(10240)->columnSpanFull(),
                TextInput::make('sort_order')->label('Orden')->numeric()->default(0)->minValue(0),
                Toggle::make('is_active')->label('Publicado')->default(true),
            ])->columns(2),
            Section::make('Fotografías panorámicas')->description('Sube imágenes equirectangulares 360° (relación recomendada 2:1).')->schema([
                Repeater::make('scenes')->label('Escenas')->relationship()->orderColumn('sort_order')->schema([
                    TextInput::make('name')->label('Nombre de la escena')->required()->maxLength(255),
                    FileUpload::make('panorama_image')->label('Fotografía 360°')->image()->required()->disk('public')
                        ->directory('tours-360/panoramas')->visibility('public')->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                        ->maxSize(30720)->helperText('JPG, PNG o WebP. Máximo 30 MB.')->columnSpanFull(),
                ])->columns(1)->reorderable()->collapsible()->itemLabel(fn (array $state): ?string => $state['name'] ?? 'Nueva escena')
                  ->addActionLabel('Agregar fotografía 360°')->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            ImageColumn::make('cover_image')->label('Portada')->disk('public')->square(),
            TextColumn::make('name')->label('Nombre')->searchable()->sortable(),
            TextColumn::make('scenes_count')->label('Fotografías')->counts('scenes')->badge(),
            IconColumn::make('is_active')->label('Publicado')->boolean(),
            TextColumn::make('sort_order')->label('Orden')->sortable(),
        ])->defaultSort('sort_order')->recordActions([EditAction::make(), DeleteAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVirtualTours::route('/'),
            'create' => Pages\CreateVirtualTour::route('/create'),
            'edit' => Pages\EditVirtualTour::route('/{record}/edit'),
        ];
    }
}
