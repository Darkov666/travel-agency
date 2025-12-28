<!DOCTYPE html>
<html>

<head>
    <title>Nueva Solicitud de Taller</title>
</head>

<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-w: 600px; margin: 0 auto; padding: 20px;">
        <h1 style="color: #2B6CB0;">Nueva Solicitud de Taller</h1>
        <p>Se ha recibido una nueva solicitud de información/taller.</p>

        <h3>Detalles del Cliente:</h3>
        <ul>
            <li><strong>Nombre:</strong> {{ $data['name'] }}</li>
            <li><strong>Empresa/Institución:</strong> {{ $data['company'] ?? 'N/A' }}</li>
            <li><strong>Email:</strong> {{ $data['email'] }}</li>
            <li><strong>Teléfono:</strong> {{ $data['phone'] }}</li>
            <li><strong>Género:</strong> {{ $data['gender'] }}</li>
        </ul>

        <h3>Detalles del Servicio:</h3>
        <ul>
            <li><strong>Servicio:</strong> {{ $service->title }}</li>
            <li><strong>Fecha Preferida:</strong> {{ $data['preferred_date'] }}</li>
            <li><strong>Hora Preferida:</strong> {{ $data['preferred_time'] }}</li>
        </ul>

        <p>El sistema ha enviado automáticamente el enlace de Meet al usuario.</p>
    </div>
</body>

</html>