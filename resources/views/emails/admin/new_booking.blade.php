<!DOCTYPE html>
<html>

<head>
    <title>Nueva Reserva</title>
</head>

<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2 style="color: #2c3e50;">Nueva Reserva Recibida</h2>

        <p>Se ha registrado una nueva cita.</p>

        <div style="background: #f9f9f9; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <p><strong>Cliente:</strong> {{ $appointment->customer_name }}</p>
            <p><strong>Email:</strong> {{ $appointment->customer_email }}</p>
            <p><strong>Teléfono:</strong> {{ $appointment->customer_phone }}</p>
            <p><strong>Tipo de Sesión:</strong>
                {{ $appointment->session_type === 'online' ? 'Online (Google Meet)' : 'Presencial' }}</p>
            <p><strong>Servicio:</strong> {{ $appointment->service->title }}</p>
            <p><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($appointment->scheduled_at)->format('d/m/Y H:i') }}</p>
            @if(!in_array($appointment->service->type, ['workshop', 'conference', 'talk', 'training']))
                <p><strong>Método de Pago:</strong> {{ $appointment->payment_method }}</p>
            @endif
            <p><strong>Estado:</strong> {{ $appointment->status }}</p>
        </div>

        @if($appointment->status === 'pending')
            <div style="margin-top: 30px; text-align: center;">
                @if(!in_array($appointment->service->type, ['workshop', 'conference', 'talk', 'training']))
                    <p>Si recibiste la transferencia, por favor confirma la cita:</p>
                @else
                    <p>Por favor confirma la cita si no hay inconveniente:</p>
                @endif

                <!-- Signed Route for one-click confirmation -->
                <a href="{{ \Illuminate\Support\Facades\URL::signedRoute('booking.accept.signed', ['appointment' => $appointment->id]) }}"
                    style="background-color: #3490dc; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-weight: bold;">
                    Confirmar Cita
                </a>
                <br><br>
                <br><br>
                <small>Nota: Al confirmar esta cita, el sistema enviará automáticamente el correo de confirmación al
                    cliente. Si la reserva incluye productos digitales, los enlaces de descarga se enviarán en ese mismo
                    correo.</small>
            </div>
        @endif
    </div>
</body>

</html>