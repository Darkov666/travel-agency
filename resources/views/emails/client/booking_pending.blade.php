<!DOCTYPE html>
<html>

<head>
    <title>Reserva Pendiente</title>
</head>

<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-w-600px; margin: 0 auto; padding: 20px;">
        <h2 style="color: #2c3e50;">Tu Reserva está Pendiente</h2>

        <p>Hola {{ $appointment->customer_name }},</p>
        @if(in_array($appointment->service->type, ['workshop', 'conference', 'talk', 'training']))
            <p>Hemos recibido tu solicitud de reserva para el taller/conferencia. Esta cita requiere aprobación del
                psicólogo.</p>
        @else
            <p>Hemos recibido tu solicitud de reserva. Para confirmarla, por favor realiza el pago mediante transferencia
                bancaria.</p>

            <div style="background: #f9f9f9; padding: 15px; border-radius: 5px; margin: 20px 0;">
                <h3 style="margin-top: 0;">Datos Bancarios:</h3>
                <p><strong>Banco:</strong> BBVA</p>
                <p><strong>Cuenta:</strong> 1234567890</p>
                <p><strong>CLABE:</strong> 012345678901234567</p>
                <p><strong>Titular:</strong> Yuliana Andrea Sanchez Navarrete</p>
                <p><strong>Monto:</strong> ${{ $appointment->service->price_mxn }} MXN</p>
            </div>

            <div
                style="background: #fff3cd; color: #856404; padding: 15px; border-radius: 5px; margin: 20px 0; border: 1px solid #ffeeba;">
                <strong>IMPORTANTE:</strong> Tienes 24 horas para enviar tu comprobante de pago, de lo contrario la reserva
                se cancelará automáticamente.
            </div>
        @endif

        @if(in_array($appointment->service->type, ['workshop', 'conference', 'talk', 'training']))
            <p>Si tienes alguna duda o comentario puedes contactarnos via Whatsapp dando clic en el siguiente botón.</p>
            <div style="margin-top: 30px; text-align: center;">
                <a href="https://wa.me/{{ env('WHATSAPP_NUMBER') }}?text=Hola,%20tengo%20dudas%20sobre%20mi%20solicitud%20de%20{{ urlencode($appointment->service->title) }}"
                    style="background-color: #25D366; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-weight: bold;">
                    Contactar por WhatsApp
                </a>
            </div>
        @else
            <p>Por favor, envía tu comprobante vía WhatsApp haciendo clic en el siguiente botón:</p>

            <div style="margin-top: 30px; text-align: center;">
                <a href="https://wa.me/{{ env('WHATSAPP_NUMBER') }}?text=Hola,%20envío%20comprobante%20para%20la%20reserva%20de%20{{ urlencode($appointment->service->title) }}%20a%20nombre%20de%20{{ urlencode($appointment->customer_name) }}"
                    style="background-color: #25D366; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-weight: bold;">
                    Enviar Comprobante por WhatsApp
                </a>
            </div>
        @endif
    </div>
</body>

</html>