<!DOCTYPE html>
<html lang="es">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>{{ $noticia->titulo }}</title></head>
<body style="margin:0;background:#f5f0e8;font-family:Arial,sans-serif;color:#1f2937">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="padding:32px 16px">
    <tr><td align="center">
        <table role="presentation" width="620" cellspacing="0" cellpadding="0" style="width:100%;max-width:620px;background:#fff;border-radius:22px;overflow:hidden;box-shadow:0 15px 45px rgba(69,10,10,.12)">
            @if($noticia->imagen_url)
                <tr><td><img src="{{ $noticia->imagen_url }}" alt="{{ $noticia->titulo }}" width="620" style="display:block;width:100%;height:auto;max-height:360px;object-fit:cover"></td></tr>
            @endif
            <tr><td style="padding:34px">
                @if($isTest)
                    <p style="margin:0 0 18px;border-radius:10px;background:#fef3c7;padding:10px 14px;color:#92400e;font-size:12px;font-weight:bold">VISTA DE PRUEBA · Aún no se envió a los suscriptores</p>
                @endif
                <p style="margin:0 0 10px;color:#991b1b;font-size:12px;font-weight:bold;letter-spacing:2px;text-transform:uppercase">Novedades de Turismo Tarija</p>
                <h1 style="margin:0 0 18px;font-size:30px;line-height:1.2;color:#3f0710">{{ $noticia->titulo }}</h1>
                <p style="font-size:16px;line-height:1.7;color:#4b5563">{{ $noticia->resumen ?: \Illuminate\Support\Str::limit(strip_tags($noticia->contenido), 260) }}</p>
                <p style="margin:28px 0"><a href="{{ route('noticias.show', $noticia) }}" style="display:inline-block;border-radius:999px;background:#7f1d1d;padding:14px 24px;color:#fff;text-decoration:none;font-weight:bold">Leer noticia completa</a></p>
                @if($isTest)
                    <p style="margin-top:30px;border-top:1px solid #e4d2cc;padding-top:20px;font-size:12px;line-height:1.6;color:#6b7280">Este correo solo fue enviado a la cuenta institucional para revisión.</p>
                @else
                    <p style="margin-top:30px;border-top:1px solid #e4d2cc;padding-top:20px;font-size:12px;line-height:1.6;color:#6b7280">Recibes este mensaje porque te suscribiste al boletín turístico. <a href="{{ route('newsletter.unsubscribe', $subscriber->token) }}" style="color:#6f1d2c">Cancelar suscripción</a>.</p>
                @endif
            </td></tr>
        </table>
    </td></tr>
</table>
</body>
</html>
