<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>{{ $tour->name }} | Tour 360°</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@photo-sphere-viewer/core@5/index.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@photo-sphere-viewer/markers-plugin@5/index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        *{box-sizing:border-box}html,body,#viewer{width:100%;height:100%;margin:0;background:#16080b;font-family:Inter,system-ui,sans-serif}.topbar{position:fixed;z-index:20;top:0;left:0;right:0;display:flex;align-items:center;justify-content:space-between;gap:1rem;padding:1rem;background:linear-gradient(to bottom,rgba(0,0,0,.78),transparent);color:#fff;pointer-events:none}.topbar>*{pointer-events:auto}.back{display:inline-flex;align-items:center;gap:.55rem;border-radius:999px;background:rgba(45,11,18,.85);padding:.75rem 1rem;color:#fff;text-decoration:none;font-weight:800;backdrop-filter:blur(12px)}h1{margin:0;font-size:clamp(1rem,3vw,1.45rem);text-shadow:0 2px 10px #000}.scenes{position:fixed;z-index:20;bottom:max(1rem,env(safe-area-inset-bottom));left:50%;display:flex;max-width:calc(100vw - 2rem);transform:translateX(-50%);gap:.5rem;overflow-x:auto;border-radius:1.1rem;background:rgba(0,0,0,.66);padding:.6rem;backdrop-filter:blur(12px)}.scene{flex:none;border:1px solid rgba(255,255,255,.25);border-radius:.8rem;background:rgba(255,255,255,.12);padding:.7rem 1rem;color:#fff;font-weight:700;cursor:pointer}.scene.active{border-color:#fff;background:#6f1d2c}.empty{display:grid;height:100%;place-items:center;color:#fff;text-align:center}.empty a{color:#fff}.entrance-hotspot{display:grid;width:58px;height:58px;place-items:center;border:3px solid #fff;border-radius:50%;background:rgba(111,29,44,.92);color:#fff;box-shadow:0 0 0 0 rgba(255,255,255,.85),0 12px 30px rgba(0,0,0,.5);cursor:pointer;animation:entrance-pulse 1.7s infinite,entrance-bounce 1.2s ease-in-out infinite}.entrance-hotspot i{font-size:1.7rem}.entrance-hotspot::after{content:'';position:absolute;left:50%;top:100%;transform:translateX(-50%);border-left:9px solid transparent;border-right:9px solid transparent;border-top:12px solid #fff}@keyframes entrance-pulse{0%{box-shadow:0 0 0 0 rgba(255,255,255,.75),0 12px 30px rgba(0,0,0,.5)}70%{box-shadow:0 0 0 18px rgba(255,255,255,0),0 12px 30px rgba(0,0,0,.5)}100%{box-shadow:0 0 0 0 rgba(255,255,255,0),0 12px 30px rgba(0,0,0,.5)}}@keyframes entrance-bounce{0%,100%{transform:translateY(0)}50%{transform:translateY(-9px)}}@media(max-width:640px){.topbar{align-items:flex-start}.topbar h1{max-width:55%;text-align:right}.back span{display:none}}
    </style>
</head>
<body>
    <header class="topbar"><a class="back" href="{{ route('tours-360.index') }}"><i class="fa-solid fa-arrow-left"></i><span>Volver a tours</span></a><h1>{{ $tour->name }}</h1></header>
    @if($tour->scenes->isEmpty())
        <div class="empty"><div><i class="fa-solid fa-panorama fa-3x"></i><p>Este recorrido todavía no tiene fotografías.</p><a href="{{ route('tours-360.index') }}">Volver</a></div></div>
    @else
        <div id="viewer" aria-label="Visor panorámico de {{ $tour->name }}"></div>
        <nav class="scenes" aria-label="Escenas del recorrido">
            @foreach($tour->scenes->take(1) as $scene)<button class="scene active" type="button" data-index="0">San Roque</button>@endforeach
        </nav>
        <script type="importmap">{"imports":{"three":"https://cdn.jsdelivr.net/npm/three@0.170.0/build/three.module.js","@photo-sphere-viewer/core":"https://cdn.jsdelivr.net/npm/@photo-sphere-viewer/core@5/index.module.js","@photo-sphere-viewer/markers-plugin":"https://cdn.jsdelivr.net/npm/@photo-sphere-viewer/markers-plugin@5/index.module.js"}}</script>
        <script type="module">
            import { Viewer } from '@photo-sphere-viewer/core';
            import { MarkersPlugin } from '@photo-sphere-viewer/markers-plugin';
            const scenes = @json($tour->scenes->map(fn ($scene) => ['name' => $scene->name, 'url' => Storage::disk('public')->url($scene->panorama_image)])->values());
            const viewer = new Viewer({container: document.querySelector('#viewer'), panorama: scenes[0].url, navbar: ['autorotate','zoom','move','fullscreen'], plugins: [[MarkersPlugin, {markers: [{id: 'church-entrance', position: {yaw: 0, pitch: -0.39}, html: '<div class="entrance-hotspot" title="Entrar a la iglesia" aria-label="Entrar a la iglesia"><i class="fa-solid fa-arrow-down"></i></div>', anchor: 'bottom center'}]}]]});
            const markers = viewer.getPlugin(MarkersPlugin);
            const buttons = [...document.querySelectorAll('.scene')];
            buttons.forEach((button) => button.addEventListener('click', async () => {
                const index = Number(button.dataset.index);
                buttons.forEach(item => item.classList.toggle('active', item === button));
                await viewer.setPanorama(scenes[index].url, {transition: 900, showLoader: true});
                if (index === 0) markers.showMarker('church-entrance'); else markers.hideMarker('church-entrance');
            }));
            markers.addEventListener('select-marker', async ({marker}) => {
                if (marker.id !== 'church-entrance') return;
                if (scenes.length < 2) {
                    window.alert('Sube la fotografía 360° del interior como segunda escena para ingresar.');
                    return;
                }
                await viewer.setPanorama(scenes[1].url, {transition: 900, showLoader: true});
                markers.hideMarker('church-entrance');
                buttons[0]?.classList.remove('active');
            });
        </script>
    @endif
</body>
</html>
