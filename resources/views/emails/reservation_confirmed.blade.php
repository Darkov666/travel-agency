<!DOCTYPE html>
<html>

<head>
    <title>Reserva Confirmada</title>
</head>

<body>
    <h1>¡Tu reserva ha sido confirmada!</h1>
    <p>Hola {{ $appointment->customer_name }},</p>
    <p>Tu cita para <strong>{{ $appointment->service->title }}</strong> ha sido confirmada.</p>
    <p><strong>Folio:</strong> {{ $appointment->folio }}</p>
    <p><strong>Fecha:</strong> {{ $appointment->scheduled_at }}</p>
    <p><strong>Enlace a la sesión (si aplica):</strong> [Enlace Zoom/Meet]</p>
    <p>Gracias por confiar en nosotros.</p>
</body>

</html>