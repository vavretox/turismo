@php
    $record = $getRecord();
    $imageUrl = $record?->imagen_url ?? asset('images/referencia/tarija.jpg');
    $hasUploadedImage = filled($record?->imagen);
@endphp

@once
    <style>
        .weekly-activity-image-upload {
            width: 100%;
            padding: .75rem;
            border: 1px dashed #c9b49e;
            border-radius: 1rem;
            background: #faf7f2;
            color: #6b7280;
        }

        .weekly-activity-image-upload::file-selector-button {
            margin-right: 1rem;
            padding: .75rem 1.25rem;
            border: 0;
            border-radius: .75rem;
            background: #7f1d1d;
            color: white;
            font-weight: 800;
            cursor: pointer;
            transition: background-color .2s, transform .2s;
        }

        .weekly-activity-image-upload::file-selector-button:hover {
            background: #991b1b;
            transform: translateY(-1px);
        }
    </style>
@endonce

<div
    x-data="{
        imageUrl: @js($imageUrl),
        originalUrl: @js($imageUrl),
        changed: false,
        fileName: '',
        init() {
            const updatePreview = (event) => {
                const input = event.target;

                if (
                    ! (input instanceof HTMLInputElement) ||
                    input.type !== 'file' ||
                    ! input.classList.contains('weekly-activity-image-upload')
                ) {
                    return;
                }

                const file = input.files?.[0];
                if (! file) return;

                if (this.imageUrl.startsWith('blob:')) {
                    URL.revokeObjectURL(this.imageUrl);
                }

                this.imageUrl = URL.createObjectURL(file);
                this.fileName = file.name;
                this.changed = true;
            };

            document.addEventListener('change', updatePreview);
        },
    }"
    class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm"
>
    <div class="grid gap-0 md:grid-cols-[minmax(280px,420px)_1fr]">
        <div class="relative min-h-64 overflow-hidden bg-gray-100">
            <img
                x-bind:src="imageUrl"
                alt="Vista previa de la actividad de la semana"
                class="h-64 w-full object-cover"
            >
            <span class="absolute bottom-4 left-4 inline-flex items-center gap-2 rounded-full bg-amber-400 px-4 py-2 text-xs font-black uppercase text-red-950 shadow-lg">
                <span aria-hidden="true">▣</span>
                Actividad de la semana
            </span>
        </div>

        <div class="flex flex-col justify-center gap-4 p-6">
            <div>
                <p class="text-sm font-bold text-gray-950" x-text="changed ? 'Nueva imagen seleccionada' : 'Imagen que se muestra actualmente'"></p>
                <p class="mt-1 text-sm text-gray-500">
                    <span x-show="! changed">
                        {{ $hasUploadedImage ? 'Esta imagen está guardada y publicada en el popup.' : 'Se está usando la imagen predeterminada. Selecciona una nueva imagen debajo.' }}
                    </span>
                    <span x-show="changed" x-cloak>
                        <span x-text="fileName"></span>. Presiona “Guardar cambios” para publicarla.
                    </span>
                </p>
            </div>

            <div
                class="inline-flex w-fit items-center gap-2 rounded-full px-3 py-2 text-xs font-bold"
                x-bind:class="changed ? 'bg-amber-100 text-amber-900' : '{{ $hasUploadedImage ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}'"
            >
                <span class="h-2.5 w-2.5 rounded-full" x-bind:class="changed ? 'bg-amber-500' : '{{ $hasUploadedImage ? 'bg-green-500' : 'bg-gray-400' }}'"></span>
                <span x-text="changed ? 'Cambio pendiente de guardar' : '{{ $hasUploadedImage ? 'Imagen publicada' : 'Imagen predeterminada' }}'"></span>
            </div>

            <label class="block">
                <span class="mb-2 block text-sm font-bold text-gray-950">Cambiar imagen</span>
                <input
                    type="file"
                    wire:model="weeklyImageUpload"
                    accept="image/jpeg,image/png,image/webp"
                    class="weekly-activity-image-upload"
                >
            </label>

            <p class="text-xs leading-5 text-gray-500">
                Formatos permitidos: JPG, PNG o WebP. Tamaño máximo: 5 MB. Después de seleccionarla, presiona “Guardar cambios”.
            </p>

            <div wire:loading wire:target="weeklyImageUpload" class="rounded-xl bg-blue-50 px-4 py-3 text-sm font-bold text-blue-800">
                Subiendo imagen, espera un momento…
            </div>

            @error('weeklyImageUpload')
                <p class="rounded-xl bg-red-50 px-4 py-3 text-sm font-bold text-red-700">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>
