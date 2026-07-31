<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\HasSectionPermission;
use App\Filament\Resources\NoticiaResource\Pages;
use App\Mail\TourismNewsletter;
use App\Models\NewsletterSubscriber;
use App\Models\Noticia;
use BackedEnum;
use Filament\Actions\Action;
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
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class NoticiaResource extends Resource
{
    use HasSectionPermission;

    protected static ?string $model = Noticia::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedNewspaper;
    protected static ?string $recordTitleAttribute = 'titulo';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Contenido de la noticia')->schema([
                Select::make('destino_id')->label('Destino relacionado')->relationship('destino', 'nombre')->searchable()->preload(),
                TextInput::make('titulo')->required()->maxLength(255),
                TextInput::make('slug')->label('Dirección web')->maxLength(255)->helperText('Puede dejarse vacío; se genera desde el título.'),
                DateTimePicker::make('publicado_en')->label('Fecha de publicación')->seconds(false)->default(now()),
                FileUpload::make('imagen')
                    ->label('Imagen principal')
                    ->image()
                    ->disk('public')
                    ->directory('noticias')
                    ->visibility('public')
                    ->imageEditor()
                    ->imagePreviewHeight('220')
                    ->maxSize(4096)
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->columnSpanFull(),
                Textarea::make('resumen')->rows(3)->required()->maxLength(1000)->columnSpanFull(),
                Textarea::make('contenido')->rows(12)->required()->columnSpanFull(),
                TextInput::make('fuente_nombre')
                    ->label('Nombre de la fuente')
                    ->placeholder('Ej.: Gobernación de Tarija')
                    ->maxLength(255),
                TextInput::make('fuente_url')
                    ->label('Enlace de la publicación original')
                    ->placeholder('https://www.facebook.com/...')
                    ->url()
                    ->helperText('Pegue aquí el enlace de Facebook u otra fuente oficial.')
                    ->columnSpanFull(),
                Toggle::make('activo')->label('Publicada y disponible para enviar')->default(true),
            ])->columns(2)->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('imagen')->label('Imagen')->disk('public')->square(),
                TextColumn::make('titulo')->searchable()->sortable(),
                TextColumn::make('destino.nombre')->label('Destino')->sortable(),
                TextColumn::make('fuente_nombre')->label('Fuente')->placeholder('Propia')->toggleable(),
                TextColumn::make('publicado_en')->label('Publicación')->dateTime('d/m/Y H:i')->sortable(),
                IconColumn::make('activo')->label('Publicada')->boolean(),
                TextColumn::make('newsletter_enviado_en')
                    ->label('Newsletter')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('Sin enviar')
                    ->description(fn (Noticia $record): ?string => $record->newsletter_enviado_en
                        ? "{$record->newsletter_destinatarios} enviados · {$record->newsletter_fallidos} fallidos"
                        : null)
                    ->sortable(),
            ])
            ->recordActions([
                Action::make('enviarPrueba')
                    ->label('Enviar prueba')
                    ->icon(Heroicon::OutlinedEnvelope)
                    ->color('gray')
                    ->requiresConfirmation()
                    ->modalDescription('Se enviará una vista previa únicamente a turismo@tarija.gob.bo.')
                    ->action(function (Noticia $record): void {
                        $testSubscriber = new NewsletterSubscriber([
                            'nombre' => 'Equipo de Turismo Tarija',
                            'email' => config('contact.recipient'),
                            'activo' => true,
                        ]);

                        try {
                            Mail::to(config('contact.recipient'))->send(new TourismNewsletter($record, $testSubscriber, true));
                            Notification::make()->title('Prueba enviada al correo institucional')->success()->send();
                        } catch (Throwable $exception) {
                            Log::error('Falló la prueba del newsletter turístico.', [
                                'noticia_id' => $record->id,
                                'error' => $exception->getMessage(),
                            ]);
                            Notification::make()->title('No se pudo enviar la prueba')->body('Revisa la configuración SMTP y el registro de errores.')->danger()->send();
                        }
                    }),
                Action::make('enviarNewsletter')
                    ->label(fn (Noticia $record): string => $record->newsletter_enviado_en ? 'Reenviar newsletter' : 'Enviar newsletter')
                    ->icon(Heroicon::OutlinedPaperAirplane)
                    ->color(fn (Noticia $record): string => $record->newsletter_enviado_en ? 'warning' : 'success')
                    ->disabled(fn (Noticia $record): bool => ! $record->activo || NewsletterSubscriber::query()->where('activo', true)->doesntExist())
                    ->requiresConfirmation()
                    ->modalHeading(fn (Noticia $record): string => $record->newsletter_enviado_en ? '¿Reenviar esta noticia?' : '¿Enviar esta noticia?')
                    ->modalDescription(function (Noticia $record): string {
                        $total = NewsletterSubscriber::query()->where('activo', true)->count();
                        $prefix = $record->newsletter_enviado_en ? 'Esta noticia ya fue enviada anteriormente. ' : '';

                        return $prefix."Se enviará desde turismo@tarija.gob.bo a {$total} suscriptores activos.";
                    })
                    ->action(function (Noticia $record): void {
                        $sent = 0;
                        $failed = 0;

                        NewsletterSubscriber::query()
                            ->where('activo', true)
                            ->orderBy('id')
                            ->each(function (NewsletterSubscriber $subscriber) use ($record, &$sent, &$failed): void {
                                try {
                                    Mail::to($subscriber->email)->send(new TourismNewsletter($record, $subscriber));
                                    $sent++;
                                } catch (Throwable $exception) {
                                    $failed++;
                                    Log::error('Falló un envío del newsletter turístico.', [
                                        'noticia_id' => $record->id,
                                        'subscriber_id' => $subscriber->id,
                                        'error' => $exception->getMessage(),
                                    ]);
                                }
                            });

                        $record->update([
                            'newsletter_enviado_en' => now(),
                            'newsletter_destinatarios' => $sent,
                            'newsletter_fallidos' => $failed,
                        ]);

                        $notification = Notification::make()
                            ->title($failed ? 'Envío completado con observaciones' : 'Newsletter enviado')
                            ->body("Enviados: {$sent}. Fallidos: {$failed}.");

                        ($failed ? $notification->warning() : $notification->success())->send();
                    }),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNoticias::route('/'),
            'create' => Pages\CreateNoticia::route('/create'),
            'edit' => Pages\EditNoticia::route('/{record}/edit'),
        ];
    }
}
