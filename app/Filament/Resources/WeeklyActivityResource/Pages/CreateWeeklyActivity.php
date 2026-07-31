<?php
namespace App\Filament\Resources\WeeklyActivityResource\Pages;
use App\Filament\Resources\WeeklyActivityResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class CreateWeeklyActivity extends CreateRecord
{
    protected static string $resource = WeeklyActivityResource::class;

    public $weeklyImageUpload;
    public array $weeklyGalleryUploads = [];
    public $weeklyVideoUpload;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if ($this->weeklyImageUpload instanceof TemporaryUploadedFile) {
            $this->validate(['weeklyImageUpload' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120']]);
            $extension = strtolower($this->weeklyImageUpload->getClientOriginalExtension());
            $data['imagen'] = $this->weeklyImageUpload->storeAs('actividades-semana', Str::ulid().'.'.$extension, 'public');
            $this->weeklyImageUpload = null;
        }

        $this->validate(['weeklyGalleryUploads.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120']]);
        $data['galeria'] = [];
        foreach ($this->weeklyGalleryUploads as $upload) {
            if ($upload instanceof TemporaryUploadedFile) {
                $extension = strtolower($upload->getClientOriginalExtension());
                $data['galeria'][] = $upload->storeAs('actividades-semana/galeria', Str::ulid().'.'.$extension, 'public');
            }
        }
        $this->weeklyGalleryUploads = [];

        $videoUpload = $this->weeklyVideoUpload;
        if (is_array($videoUpload)) {
            $videoUpload = collect($videoUpload)->first(fn ($file) => $file instanceof TemporaryUploadedFile);
        }

        if ($videoUpload instanceof TemporaryUploadedFile) {
            $extension = strtolower($videoUpload->getClientOriginalExtension());
            $data['video'] = $videoUpload->storeAs('actividades-semana/videos', Str::ulid().'.'.$extension, 'public');
            $this->weeklyVideoUpload = null;
        }

        return $data;
    }
}
