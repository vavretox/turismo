<!DOCTYPE html>
<html lang="es">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Nuevo mensaje de contacto</title></head>
<body style="margin:0;background:#f5f1ec;color:#292524;font-family:Arial,sans-serif">
<div style="max-width:640px;margin:0 auto;padding:32px 16px">
    <div style="overflow:hidden;border-radius:18px;background:#fff;box-shadow:0 10px 30px rgba(69,10,10,.1)">
        <div style="background:#6f1d2c;padding:24px 28px;color:#fff">
            <p style="margin:0 0 6px;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.12em">Portal turístico de Tarija</p>
            <h1 style="margin:0;font-size:24px">Nuevo mensaje de contacto</h1>
        </div>
        <div style="padding:28px">
            <p style="margin:0 0 20px;line-height:1.6">Se recibió una consulta desde el formulario público del portal.</p>
            <table role="presentation" style="width:100%;border-collapse:collapse">
                <tr><td style="padding:8px 0;color:#78716c;width:110px">Nombre</td><td style="padding:8px 0;font-weight:700">{{ $contact['nombre'] }}</td></tr>
                <tr><td style="padding:8px 0;color:#78716c">Correo</td><td style="padding:8px 0"><a href="mailto:{{ $contact['email'] }}" style="color:#6f1d2c">{{ $contact['email'] }}</a></td></tr>
                <tr><td style="padding:8px 0;color:#78716c">Teléfono</td><td style="padding:8px 0">{{ $contact['telefono'] ?: 'No indicado' }}</td></tr>
                <tr><td style="padding:8px 0;color:#78716c">Motivo</td><td style="padding:8px 0">{{ ucfirst($contact['motivo']) }}</td></tr>
            </table>
            <div style="margin-top:22px;border-radius:12px;background:#faf7f4;padding:18px;line-height:1.7;white-space:pre-wrap">{{ $contact['mensaje'] }}</div>
            <p style="margin:22px 0 0;color:#78716c;font-size:13px">Puedes responder directamente a este correo; la respuesta se dirigirá al visitante.</p>
        </div>
    </div>
</div>
</body>
</html>
