<!doctype html>
<html lang="es">
<body style="margin:0;background:#f5f1ed;font-family:Arial,sans-serif;color:#292524">
<div style="max-width:640px;margin:0 auto;padding:28px">
    <div style="border-radius:18px 18px 0 0;background:#6f1d2c;padding:24px;color:white"><h1 style="margin:0;font-size:24px">Registro de prestador turístico</h1></div>
    <div style="border-radius:0 0 18px 18px;background:white;padding:26px">
        <p>Hola, <strong>{{ $provider->legal_representative }}</strong>.</p>
        <p>El registro de <strong>{{ $provider->commercial_name }}</strong> fue actualizado al estado:</p>
        <p style="display:inline-block;border-radius:999px;background:#f3e8ea;padding:10px 16px;font-weight:bold;color:#6f1d2c">{{ ['approved'=>'Dado de alta','reviewing'=>'En revisión','rejected'=>'No aprobado','suspended'=>'Dado de baja','pending'=>'Pendiente'][$provider->status] ?? ucfirst($provider->status) }}</p>
        @if($provider->admin_notes)
            <div style="margin-top:20px;border-left:4px solid #6f1d2c;background:#faf7f4;padding:15px"><strong>Observaciones:</strong><br>{{ $provider->admin_notes }}</div>
        @endif
        @if($temporaryPassword)
            <div style="margin-top:22px;border-radius:14px;background:#edf7f1;padding:18px">
                <strong>Su acceso fue habilitado</strong>
                <p>Usuario: {{ $provider->email }}<br>Contraseña temporal: <strong>{{ $temporaryPassword }}</strong></p>
                <a href="{{ route('prestador.login') }}" style="display:inline-block;border-radius:10px;background:#6f1d2c;padding:12px 18px;color:white;text-decoration:none;font-weight:bold">Ingresar al portal</a>
                <p style="font-size:12px;color:#57534e">Recomendamos cambiar esta contraseña después del primer ingreso.</p>
            </div>
        @endif
    </div>
</div>
</body>
</html>
