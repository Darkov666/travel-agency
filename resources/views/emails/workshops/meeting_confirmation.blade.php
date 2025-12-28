<!DOCTYPE html>
<html>

<head>
    <title>Siguiente pasos para tu Taller</title>
</head>

<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-w: 600px; margin: 0 auto; padding: 20px;">
        <h1 style="color: #4A5568;">¡Solicitud Recibida!</h1>
        <p>Hola {{ $data['name'] }},</p>
        <p>Gracias por tu interés en nuestro taller: <strong>{{ $service->title }}</strong>.</p>

        <p>Hemos recibido tus datos y la fecha preferida: <strong>{{ $data['preferred_date'] }} a las
                {{ $data['preferred_time'] }}</strong>.</p>

        <p>Tu solicitud ha sido pre-aprobada. A continuación encontrarás el enlace para la reunión virtual donde
            finalizaremos los detalles y confirmaremos la logística:</p>

        <div style="background-color: #F7FAFC; padding: 15px; border-radius: 8px; margin: 20px 0; text-align: center;">
            <p style="margin-bottom: 10px; font-weight: bold;">Enlace de Google Meet:</p>
            <a href="{{ $meetLink }}"
                style="background-color: #48BB78; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-weight: bold;">Unirse
                a la Reunión</a>
            <p style="font-size: 12px; color: #718096; margin-top: 10px;">{{ $meetLink }}</p>
        </div>

        <p>Si tienes alguna duda antes de la reunión, puedes responder a este correo.</p>

        <p>Saludos,<br>El equipo de Conecta Contigo</p>
    </div>
</body>

</html>