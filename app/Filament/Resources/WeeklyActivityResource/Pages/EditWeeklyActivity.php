<?php
namespace App\Filament\Resources\WeeklyActivityResource\Pages;
use App\Filament\Resources\WeeklyActivityResource;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class EditWeeklyActivity extends EditRecord
{
    protected static string $resource = WeeklyActivityResource::class;

    public $weeklyImageUpload;
    public array $weeklyGalleryUploads = [];
    public $weeklyVideoUpload;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (! $this->weeklyImageUpload instanceof TemporaryUploadedFile) {
            $data['galeria'] = $this->storeGalleryUploads($this->record->galeria ?? []);
            $data['video'] = $this->storeVideoUpload($this->record->video);
            return $data;
        }

        $this->validate([
            'weeklyImageUpload' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ], [
            'weeklyImageUpload.image' => 'Selecciona un archivo de imagen válido.',
            'weeklyImageUpload.mimes' => 'La imagen debe ser JPG, PNG o WebP.',
            'weeklyImageUpload.max' => 'La imagen no puede superar los 5 MB.',
        ]);

        $extension = strtolower($this->weeklyImageUpload->getClientOriginalExtension());
        $path = $this->weeklyImageUpload->storeAs(
            'actividades-semana',
            Str::ulid().'.'.$extension,
            'public',
        );

        if (filled($this->record->imagen)) {
            Storage::disk('public')->delete($this->record->imagen);
        }

        $data['imagen'] = $path;
        $data['galeria'] = $this->storeGalleryUploads($this->record->galeria ?? []);
        $data['video'] = $this->storeVideoUpload($this->record->video);
        $this->weeklyImageUpload = null;

        return $data;
    }

    private function storeGalleryUploads(array $gallery): array
    {
        $this->validate([
            'weeklyGalleryUploads.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        foreach ($this->weeklyGalleryUploads as $upload) {
            if ($upload instanceof TemporaryUploadedFile) {
                $extension = strtolower($upload->getClientOriginalExtension());
                $gallery[] = $upload->storeAs('actividades-semana/galeria', Str::ulid().'.'.$extension, 'public');
            }
        }

        $this->weeklyGalleryUploads = [];

        return array_values(array_filter($gallery));
    }

    private function storeVideoUpload(?string $currentVideo): ?string
    {
        $upload = $this->weeklyVideoUpload;

        if (is_array($upload)) {
            $upload = collect($upload)->first(fn ($file) => $file instanceof TemporaryUploadedFile);
        }

        if (! $upload instanceof TemporaryUploadedFile) {
            return $currentVideo;
        }

        $this->validate([
            'weeklyVideoUpload' => ['nullable'],
        ]);

        $extension = strtolower($upload->getClientOriginalExtension());
        $path = $upload->storeAs('actividades-semana/videos', Str::ulid().'.'.$extension, 'public');

        if (filled($currentVideo)) {
            Storage::disk('public')->delete($currentVideo);
        }

        $this->weeklyVideoUpload = null;

        return $path;
    }
}
