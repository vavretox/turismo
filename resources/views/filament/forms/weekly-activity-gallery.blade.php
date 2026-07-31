@php($record = $getRecord())

<div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
    @if($record?->galeria_urls?->isNotEmpty())
        <p class="mb-4 text-sm font-bold text-gray-950">Fotografías publicadas</p>
        <div class="mb-6 grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
            @foreach($record->galeria_urls as $foto)
                <img class="aspect-[4/3] w-full rounded-xl object-cover" src="{{ $foto }}" alt="Fotografía publicada">
            @endforeach
        </div>
    @endif

    <label class="block">
        <span class="mb-2 block text-sm font-bold text-gray-950">Agregar fotografías</span>
        <input type="file" wire:model="weeklyGalleryUploads" accept="image/jpeg,image/png,image/webp" multiple class="weekly-activity-image-upload">
    </label>
    <p class="mt-3 text-xs leading-5 text-gray-500">Selecciona varias imágenes. Se agregarán a la galería al guardar.</p>
    <div wire:loading wire:target="weeklyGalleryUploads" class="mt-4 rounded-xl bg-blue-50 px-4 py-3 text-sm font-bold text-blue-800">Subiendo fotografías…</div>
    @error('weeklyGalleryUploads.*')
        <p class="mt-4 rounded-xl bg-red-50 px-4 py-3 text-sm font-bold text-red-700">{{ $message }}</p>
    @enderror

    <div class="mt-7 border-t border-gray-200 pt-6">
        @if($record?->video_url)
            <p class="mb-3 text-sm font-bold text-gray-950">Video publicado</p>
            <video class="mb-5 aspect-video w-full max-w-xl rounded-xl bg-black object-contain" src="{{ $record->video_url }}" controls preload="metadata"></video>
        @endif

        <div
            x-data="{ videoReady: false, videoName: '' }"
            x-on:livewire-upload-start="videoReady = false"
            x-on:livewire-upload-finish="videoReady = true"
            x-on:livewire-upload-error="videoReady = false"
        >
            <label class="block">
                <span class="mb-2 block text-sm font-bold text-gray-950">{{ $record?->video ? 'Reemplazar video' : 'Agregar video opcional' }}</span>
                <input
                    type="file"
                    wire:model="weeklyVideoUpload"
                    accept="video/mp4,video/webm,video/quicktime"
                    class="weekly-activity-image-upload"
                    x-on:change="videoName = $event.target.files[0]?.name || ''; videoReady = false"
                >
            </label>
            <p class="mt-3 text-xs leading-5 text-gray-500">Formatos MP4, WebM o MOV. Tamaño máximo: 50 MB.</p>
            <div wire:loading wire:target="weeklyVideoUpload" class="mt-4 rounded-xl bg-blue-50 px-4 py-3 text-sm font-bold text-blue-800">Subiendo video… No guardes hasta que termine.</div>
            <div x-show="videoReady" x-cloak class="mt-4 rounded-xl bg-green-50 px-4 py-3 text-sm font-bold text-green-800">
                Video <span x-text="videoName"></span> listo. Presiona “Guardar cambios” para publicarlo.
            </div>
            @error('weeklyVideoUpload')
                <p class="mt-4 rounded-xl bg-red-50 px-4 py-3 text-sm font-bold text-red-700">{{ $message }}</p>
            @enderror
        </div>

        @php($temporaryVideo = is_array($this->weeklyVideoUpload ?? null) ? collect($this->weeklyVideoUpload)->first() : ($this->weeklyVideoUpload ?? null))
        @if($temporaryVideo instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile)
            <div class="mt-5 rounded-2xl border border-green-200 bg-green-50 p-4">
                <p class="mb-3 text-sm font-bold text-green-900">Vista previa del nuevo video — pendiente de guardar</p>
                <video class="aspect-video w-full max-w-xl rounded-xl bg-black object-contain" src="{{ $temporaryVideo->temporaryUrl() }}" controls preload="metadata"></video>
            </div>
        @endif
    </div>
</div>
