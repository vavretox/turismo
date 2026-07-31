<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\HasSectionPermission;
use App\Filament\Resources\NewsletterSubscriberResource\Pages;
use App\Models\NewsletterSubscriber;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class NewsletterSubscriberResource extends Resource
{
    use HasSectionPermission;
    protected static ?string $model = NewsletterSubscriber::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEnvelope;
    protected static ?string $navigationLabel = 'Suscriptores';
    protected static ?string $modelLabel = 'suscriptor';
    protected static ?string $pluralModelLabel = 'suscriptores del newsletter';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('nombre')->maxLength(100),
            TextInput::make('email')->email()->required()->unique(ignoreRecord: true),
            Toggle::make('activo')->label('Suscripción activa')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('email')->searchable()->sortable(),
            TextColumn::make('nombre')->searchable(),
            TextColumn::make('suscrito_en')->label('Suscrito')->dateTime('d/m/Y H:i')->sortable(),
            TextColumn::make('cancelado_en')->label('Cancelado')->dateTime('d/m/Y H:i')->placeholder('—')->sortable()->toggleable(),
            IconColumn::make('activo')->label('Activo')->boolean()->sortable(),
        ])->filters([
            TernaryFilter::make('activo')
                ->label('Estado de suscripción')
                ->trueLabel('Solo activos')
                ->falseLabel('Solo inactivos')
                ->placeholder('Todos'),
        ])->recordActions([
            EditAction::make(),
            DeleteAction::make(),
        ])->toolbarActions([
            BulkActionGroup::make([DeleteBulkAction::make()]),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNewsletterSubscribers::route('/'),
            'create' => Pages\CreateNewsletterSubscriber::route('/create'),
            'edit' => Pages\EditNewsletterSubscriber::route('/{record}/edit'),
        ];
    }
}
