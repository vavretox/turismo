@php
    $fonts = ['Inter', 'Montserrat', 'Poppins', 'Roboto', 'Nunito', 'Open Sans', 'Lora', 'Playfair Display', 'Raleway', 'Merriweather', 'Oswald', 'Quicksand', 'Rubik', 'Ubuntu', 'Bebas Neue', 'Dancing Script', 'Pacifico'];
@endphp
<div class="typography-editor">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Dancing+Script:wght@400;700&family=Inter:wght@400;700;900&family=Lora:wght@400;700&family=Merriweather:wght@400;700;900&family=Montserrat:wght@400;700;900&family=Nunito:wght@400;700;900&family=Open+Sans:wght@400;700&family=Oswald:wght@400;700&family=Pacifico&family=Playfair+Display:wght@400;700;900&family=Poppins:wght@400;700;900&family=Quicksand:wght@400;700&family=Raleway:wght@400;700;900&family=Roboto:wght@400;700;900&family=Rubik:wght@400;700;900&family=Ubuntu:wght@400;700&display=swap" rel="stylesheet">
    <div class="typography-help">
        <strong>Editor de tipografía</strong>
        <span>Observa cada fuente y después selecciónala en los controles inferiores. Los cambios se aplican al guardar.</span>
    </div>
    <div class="font-catalog">
        @foreach($fonts as $font)
            <button type="button" class="font-sample" data-preview-font="{{ $font }}" style="font-family: '{{ $font }}', sans-serif">
                <small>{{ $font }}</small>
                <span>Turismo Tarija</span>
                <em>Rutas, cultura y experiencias</em>
            </button>
        @endforeach
    </div>
    <div class="size-catalog">
        <span style="font-size:12px">12 px</span><span style="font-size:14px">14 px</span><span style="font-size:16px">16 px</span><span style="font-size:18px">18 px</span><span style="font-size:20px">20 px</span><span style="font-size:24px">24 px</span>
    </div>
    <div class="weight-catalog">
        <span style="font-weight:300">Ligero 300</span><span style="font-weight:400">Normal 400</span><span style="font-weight:600">Seminegrita 600</span><span style="font-weight:700">Negrita 700</span><span style="font-weight:900">Máxima 900</span>
    </div>
</div>

<style>
.typography-editor{border:1px solid #fecaca;border-radius:20px;background:#fff;padding:18px;color:#1f2937}.typography-help{display:flex;flex-direction:column;gap:4px;margin-bottom:16px}.typography-help strong{font-size:18px;color:#7f1d1d}.typography-help span{font-size:13px;color:#64748b}.font-catalog{display:grid;grid-template-columns:repeat(auto-fit,minmax(190px,1fr));gap:10px;max-height:430px;overflow:auto;padding:3px}.font-sample{display:flex;min-height:112px;flex-direction:column;align-items:flex-start;justify-content:center;border:1px solid #e5e7eb;border-radius:14px;background:#fff;padding:13px;text-align:left;transition:.2s}.font-sample:hover{border-color:#dc2626;background:#fff7f7;transform:translateY(-2px);box-shadow:0 8px 20px rgba(127,29,29,.12)}.font-sample small{font-family:Inter,sans-serif;font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.12em;color:#b91c1c}.font-sample span{font-size:22px;font-weight:700;line-height:1.25}.font-sample em{font-size:13px;font-style:normal;color:#64748b}.size-catalog,.weight-catalog{display:flex;flex-wrap:wrap;align-items:center;gap:8px;margin-top:12px;border-radius:12px;background:#f8fafc;padding:12px}.size-catalog span,.weight-catalog span{border-radius:8px;background:white;padding:7px 10px;box-shadow:0 1px 3px rgba(0,0,0,.06)}
</style>
