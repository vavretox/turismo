@php
    $documentLabels = [
        'nit' => 'Copia del NIT',
        'licencia' => 'Licencia turística',
        'identidad' => 'Documento de identidad',
        'fotografia' => 'Fotografía',
        'logo' => 'Logo institucional',
        'seprec' => 'Registro SEPREC',
        'comprobante' => 'Comprobante de pago',
    ];
    $documents = $provider->documents ?? [];
@endphp

<div
    class="provider-documents"
    x-data="{ previewOpen: false, previewUrl: '', previewTitle: '', openPreview(url, title) { this.previewUrl = url; this.previewTitle = title; this.previewOpen = true }, closePreview() { this.previewOpen = false; this.previewUrl = ''; this.previewTitle = '' } }"
    @keydown.escape.window="if (previewOpen) closePreview()"
>
    <section class="provider-summary">
        <div class="provider-avatar"><i class="fa-solid fa-building"></i></div>
        <div class="provider-summary-main">
            <small>Expediente digital</small>
            <strong>{{ $provider->commercial_name }}</strong>
            <span>{{ count($documents) }} documento(s) adjunto(s)</span>
        </div>
        <div class="provider-summary-data">
            <span><small>Representante</small><strong>{{ $provider->legal_representative }}</strong></span>
            <span><small>C.I.</small><strong>{{ $provider->identity_document }}</strong></span>
        </div>
    </section>

    <div class="provider-help">
        <i class="fa-solid fa-circle-info"></i>
        <p><strong>Revisa sin descargar.</strong> Pulsa “Ver documento” para abrir la vista previa aquí mismo. Usa “Descargar” solo cuando necesites guardar una copia.</p>
    </div>

    <div class="provider-document-list">
        @forelse($documents as $index => $document)
            @php
                $extension = strtolower(pathinfo($document['name'] ?? $document['path'] ?? '', PATHINFO_EXTENSION));
                $isPdf = $extension === 'pdf';
                $type = $document['type'] ?? 'archivo';
                $typeLabel = $documentLabels[$type] ?? ucfirst(str_replace('_', ' ', $type));
            @endphp
            <article class="provider-document-card">
                <div class="provider-document-number">{{ $index + 1 }}</div>
                <div class="provider-document-icon {{ $isPdf ? 'is-pdf' : 'is-image' }}">
                    <i class="fa-solid {{ $isPdf ? 'fa-file-pdf' : 'fa-file-image' }}"></i>
                </div>
                <div class="provider-document-copy">
                    <span class="provider-document-type">{{ $typeLabel }}</span>
                    <strong title="{{ $document['name'] ?? 'Documento' }}">{{ $document['name'] ?? 'Documento' }}</strong>
                    <small>{{ strtoupper($extension ?: 'archivo') }} · Archivo adjunto</small>
                </div>
                <div class="provider-document-actions">
                    <button
                        class="provider-preview-button"
                        type="button"
                        @click="openPreview(@js(route('prestadores.documents.preview', ['provider' => $provider, 'index' => $index])), @js($document['name'] ?? 'Documento'))"
                    >
                        <i class="fa-solid fa-eye"></i><span>Ver documento</span>
                    </button>
                    <a class="provider-download" href="{{ route('prestadores.documents.download', ['provider' => $provider, 'index' => $index]) }}">
                        <i class="fa-solid fa-download"></i><span>Descargar</span>
                    </a>
                </div>
            </article>
        @empty
            <div class="provider-empty">
                <i class="fa-regular fa-folder-open"></i>
                <strong>Sin documentos adjuntos</strong>
                <p>Este prestador todavía no cargó archivos.</p>
            </div>
        @endforelse
    </div>

    <div class="document-viewer-backdrop" x-cloak x-show="previewOpen" x-transition.opacity @click.self="closePreview()">
        <section class="document-viewer" role="dialog" aria-modal="true" aria-labelledby="document-viewer-title" x-show="previewOpen" x-transition>
            <header class="document-viewer-header">
                <div>
                    <small>Vista previa del documento</small>
                    <strong id="document-viewer-title" x-text="previewTitle"></strong>
                </div>
                <div class="document-viewer-controls">
                    <a :href="previewUrl" target="_blank" rel="noopener" title="Abrir en pantalla completa"><i class="fa-solid fa-expand"></i><span>Pantalla completa</span></a>
                    <button type="button" @click="closePreview()" aria-label="Cerrar vista previa"><i class="fa-solid fa-xmark"></i></button>
                </div>
            </header>
            <div class="document-viewer-body">
                <iframe :src="previewOpen ? previewUrl : 'about:blank'" :title="'Vista previa de ' + previewTitle"></iframe>
            </div>
            <footer class="document-viewer-footer">
                <span><i class="fa-solid fa-shield-halved"></i> Documento privado · acceso administrativo</span>
                <button type="button" @click="closePreview()">Cerrar vista</button>
            </footer>
        </section>
    </div>
</div>

<style>
    .provider-documents { color:#292524; font-family:Inter,ui-sans-serif,system-ui,sans-serif; }
    .provider-summary { display:grid; grid-template-columns:auto minmax(0,1fr) auto; gap:14px; align-items:center; padding:18px; border:1px solid #eadfd9; border-radius:18px; background:linear-gradient(135deg,#fffaf7,#f6eee9); }
    .provider-avatar { display:grid; width:48px; height:48px; place-items:center; border-radius:14px; color:#fff; background:linear-gradient(135deg,#8b2436,#571624); box-shadow:0 8px 18px rgba(87,22,36,.2); }
    .provider-summary-main { min-width:0; }
    .provider-summary-main small { display:block; color:#8b5e65; font-size:10px; font-weight:800; letter-spacing:.12em; text-transform:uppercase; }
    .provider-summary-main strong { display:block; margin-top:2px; overflow:hidden; color:#3f0710; font-size:17px; text-overflow:ellipsis; white-space:nowrap; }
    .provider-summary-main span { display:block; margin-top:3px; color:#78716c; font-size:12px; }
    .provider-summary-data { display:flex; gap:24px; padding-left:18px; border-left:1px solid #dfd1ca; }
    .provider-summary-data span { min-width:110px; }
    .provider-summary-data small { display:block; color:#8b817c; font-size:10px; }
    .provider-summary-data strong { display:block; margin-top:2px; color:#41383a; font-size:12px; }
    .provider-help { display:flex; gap:10px; align-items:flex-start; margin:14px 0; padding:12px 14px; border-radius:12px; color:#5d4a4e; background:#f3eeee; font-size:12px; line-height:1.5; }
    .provider-help > i { margin-top:2px; color:#8b2436; }
    .provider-help p { margin:0; }
    .provider-document-list { display:grid; gap:10px; }
    .provider-document-card { position:relative; display:grid; grid-template-columns:auto auto minmax(0,1fr) auto; gap:12px; align-items:center; padding:13px; border:1px solid #e5e0dd; border-radius:15px; background:#fff; transition:border-color .2s,box-shadow .2s,transform .2s; }
    .provider-document-card:hover { border-color:#c99ba4; box-shadow:0 8px 22px rgba(69,10,10,.07); transform:translateY(-1px); }
    .provider-document-number { display:grid; width:24px; height:24px; place-items:center; border-radius:50%; color:#756b68; background:#f2efed; font-size:10px; font-weight:900; }
    .provider-document-icon { display:grid; width:42px; height:42px; place-items:center; border-radius:12px; font-size:18px; }
    .provider-document-icon.is-pdf { color:#b42332; background:#fcebed; }
    .provider-document-icon.is-image { color:#176b87; background:#e8f5f8; }
    .provider-document-copy { min-width:0; }
    .provider-document-type { display:block; color:#8b2436; font-size:10px; font-weight:900; letter-spacing:.06em; text-transform:uppercase; }
    .provider-document-copy strong { display:block; margin-top:2px; overflow:hidden; color:#292524; font-size:13px; text-overflow:ellipsis; white-space:nowrap; }
    .provider-document-copy small { display:block; margin-top:3px; color:#8b817c; font-size:10px; }
    .provider-document-actions { display:flex; gap:7px; align-items:center; }
    .provider-preview-button,.provider-download { display:inline-flex; min-height:38px; box-sizing:border-box; align-items:center; justify-content:center; gap:7px; border-radius:10px; padding:8px 12px; font-size:11px; font-weight:800; text-decoration:none; cursor:pointer; transition:.2s; }
    .provider-preview-button { border:0; color:#fff; background:#762033; box-shadow:0 5px 12px rgba(118,32,51,.16); }
    .provider-preview-button:hover { background:#971d32; }
    .provider-download { border:1px solid #ddd4d0; color:#514749; background:#fff; }
    .provider-download:hover { border-color:#9f5966; color:#762033; background:#fff8f7; }
    .document-viewer-backdrop { position:fixed; z-index:9999; inset:0; display:grid; place-items:center; padding:20px; background:rgba(24,18,20,.78); backdrop-filter:blur(5px); }
    .document-viewer { display:flex; width:min(1100px,96vw); height:min(820px,92vh); overflow:hidden; flex-direction:column; border:1px solid rgba(255,255,255,.25); border-radius:20px; background:#fff; box-shadow:0 30px 90px rgba(0,0,0,.45); }
    .document-viewer-header { display:flex; min-height:68px; align-items:center; justify-content:space-between; gap:16px; padding:12px 18px; color:#fff; background:linear-gradient(135deg,#571624,#762033); }
    .document-viewer-header > div:first-child { min-width:0; }
    .document-viewer-header small { display:block; color:#e8cfd4; font-size:9px; font-weight:900; letter-spacing:.12em; text-transform:uppercase; }
    .document-viewer-header strong { display:block; margin-top:3px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; font-size:13px; }
    .document-viewer-controls { display:flex; flex:0 0 auto; gap:8px; }
    .document-viewer-controls a,.document-viewer-controls button { display:inline-flex; min-height:38px; align-items:center; justify-content:center; gap:7px; border:1px solid rgba(255,255,255,.2); border-radius:10px; color:#fff; background:rgba(255,255,255,.1); padding:8px 11px; font-size:10px; font-weight:800; text-decoration:none; cursor:pointer; }
    .document-viewer-controls button { width:38px; padding:0; font-size:15px; }
    .document-viewer-controls a:hover,.document-viewer-controls button:hover { background:rgba(255,255,255,.2); }
    .document-viewer-body { min-height:0; flex:1; padding:10px; background:#393436; }
    .document-viewer-body iframe { display:block; width:100%; height:100%; border:0; border-radius:8px; background:#fff; }
    .document-viewer-footer { display:flex; min-height:52px; align-items:center; justify-content:space-between; gap:16px; padding:9px 16px; border-top:1px solid #e5e0dd; color:#756b68; background:#fff; font-size:10px; }
    .document-viewer-footer button { border:0; border-radius:9px; color:#fff; background:#762033; padding:9px 16px; font-size:10px; font-weight:800; cursor:pointer; }
    .provider-empty { padding:36px; border:2px dashed #ddd4d0; border-radius:16px; text-align:center; color:#857a77; }
    .provider-empty > i { font-size:30px; }
    .provider-empty strong { display:block; margin-top:10px; color:#514749; }
    .provider-empty p { margin:4px 0 0; font-size:12px; }
    @media(max-width:720px) {
        .provider-summary { grid-template-columns:auto minmax(0,1fr); }
        .provider-summary-data { grid-column:1/-1; padding:12px 0 0; border-top:1px solid #dfd1ca; border-left:0; }
        .provider-document-card { grid-template-columns:auto minmax(0,1fr); }
        .provider-document-number { display:none; }
        .provider-document-actions { grid-column:1/-1; }
        .provider-preview-button,.provider-download { flex:1; }
        .document-viewer-backdrop { padding:8px; }
        .document-viewer { width:100%; height:94vh; border-radius:14px; }
        .document-viewer-controls a span { display:none; }
        .document-viewer-footer span { display:none; }
        .document-viewer-footer { justify-content:flex-end; }
    }
</style>
