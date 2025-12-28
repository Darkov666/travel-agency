<!DOCTYPE html>
<html>

<head>
    <title>Reserva Confirmada</title>
</head>

<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2 style="color: #2c3e50;">¡Tu Reserva está Confirmada!</h2>

        <p>Hola {{ $appointment->customer_name }},</p>
        <p>Nos complace informarte que tu cita ha sido confirmada exitosamente.</p>

        <div style="background: #e8f5e9; padding: 15px; border-radius: 5px; margin: 20px 0; border: 1px solid #c8e6c9;">
            <h3 style="margin-top: 0; color: #2e7d32;">Detalles de la Cita:</h3>
            <p><strong>Folio:</strong> {{ $appointment->folio }}</p>
            <p><strong>Servicio:</strong> {{ $appointment->service->title }}</p>
            <p><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($appointment->scheduled_at)->format('d/m/Y') }}</p>
            <p><strong>Hora de inicio:</strong> {{ \Carbon\Carbon::parse($appointment->scheduled_at)->format('H:i') }}
            </p>
            <p><strong>Hora de finalización:</strong> {{ \Carbon\Carbon::parse($appointment->end_time)->format('H:i') }}
            </p>
            <p><strong>Tipo de Sesión:</strong> {{ $appointment->session_type === 'online' ? 'Online' : 'Presencial' }}
            </p>

            @if($appointment->session_type === 'online')
                <p><strong>Link de Reunión:</strong> <a href="{{ $appointment->google_event_id ?? '#' }}">Unirse con Google
                        Meet</a></p>
            @else
                <p><strong>Ubicación:</strong> Consultorio Principal, Av. Reforma 123, CDMX. <a
                        href="https://maps.google.com/?q=Consultorio+Principal+Av.+Reforma+123+CDMX">Ver en Mapa</a></p>
            @endif
        </div>

        @if($appointment->service->downloadable)
            <div style="background: #e3f2fd; padding: 15px; border-radius: 5px; margin: 20px 0;">
                <h3 style="margin-top: 0; color: #1565c0;">Material de Descarga</h3>
                <p>Como parte de tu servicio, aquí tienes acceso a tus materiales:</p>

                <div style="text-align: center; margin-top: 15px;">
                    <a href="{{ route('downloads.index', ['token' => $appointment->token]) }}"
                        style="background-color: #1976D2; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-weight: bold;">
                        Descargar Archivos / Manuales
                    </a>
                </div>
            </div>
        @endif

        <div style="text-align: center; margin-top: 30px;">
            <p>Si tienes alguna duda, contáctanos:</p>
            <a href="https://wa.me/{{ env('WHATSAPP_NUMBER') }}"
                style="background-color: #25D366; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-flex; align-items: center;">
                Contáctanos por WhatsApp
            </a>
        </div>
    </div>
</body>

</html>