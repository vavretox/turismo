<div class="grid gap-3">
    @forelse(($getState() ?? []) as $index => $document)
        <div class="flex flex-col justify-between gap-3 rounded-xl border border-gray-200 p-3 dark:border-white/10 sm:flex-row sm:items-center">
            <span class="min-w-0 truncate text-sm font-semibold">{{ $document['name'] ?? 'Documento' }}</span>
            <span class="flex gap-2">
                <a class="inline-flex items-center gap-2 rounded-lg bg-primary-600 px-3 py-2 text-sm font-bold text-white" href="{{ route('prestadores.documents.preview', ['provider' => $record, 'index' => $index]) }}" target="_blank" rel="noopener">
                    <x-filament::icon icon="heroicon-o-eye" class="h-5 w-5" /> Ver
                </a>
                <a class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-3 py-2 text-sm font-bold text-gray-700 dark:border-white/20 dark:text-gray-200" href="{{ route('prestadores.documents.download', ['provider' => $record, 'index' => $index]) }}">
                    <x-filament::icon icon="heroicon-o-arrow-down-tray" class="h-5 w-5" /> Descargar
                </a>
            </span>
        </div>
    @empty
        <p class="text-sm text-gray-500">No se adjuntaron documentos.</p>
    @endforelse
</div>
