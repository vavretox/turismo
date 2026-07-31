<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\HasSectionPermission;
use App\Filament\Resources\EventoResource\Pages;
use App\Models\Evento;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
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
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class EventoResource extends Resource
{
    use HasSectionPermission;
    protected static ?string $model = Evento::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;
    protected static ?string $recordTitleAttribute = 'titulo';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
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
                ->helperText('Permite mostrar y buscar el evento dentro del municipio correcto.'),
            Select::make('destino_id')->relationship('destino', 'nombre')->searchable()->preload(),
            TextInput::make('titulo')->required()->maxLength(255),
            TextInput::make('slug')->maxLength(255),
            TextInput::make('lugar')->maxLength(255),
            FileUpload::make('imagen')
                ->label('Imagen')
                ->image()
                ->disk('public')
                ->directory('eventos')
                ->visibility('public')
                ->imageEditor()
                ->imagePreviewHeight('220')
                ->maxSize(4096)
                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                ->columnSpanFull(),
            DateTimePicker::make('fecha_inicio')->seconds(false),
            DateTimePicker::make('fecha_fin')->seconds(false),
            Textarea::make('descripcion')->rows(6)->columnSpanFull(),
            Toggle::make('activo')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('imagen')->label('Imagen')->disk('public')->square(),
                TextColumn::make('titulo')->searchable()->sortable(),
                TextColumn::make('destino.nombre')->label('Destino')->sortable(),
                TextColumn::make('municipio.nombre')->label('Municipio')->searchable()->sortable(),
                TextColumn::make('fecha_inicio')->dateTime('d/m/Y H:i')->sortable(),
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
            'index' => Pages\ListEventos::route('/'),
            'create' => Pages\CreateEvento::route('/create'),
            'edit' => Pages\EditEvento::route('/{record}/edit'),
        ];
    }
}
