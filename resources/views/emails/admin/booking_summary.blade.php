<!DOCTYPE html>
<html>

<head>
    <title>Resumen de Nuevas Reservas</title>
    <style>
        body {
            font-family: sans-serif;
            line-height: 1.6;
            color: #333;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .appointment {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            background-color: #f9f9f9;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }

        .header {
            background-color: #eee;
            padding: 10px;
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h2>Nuevas Reservas Recibidas</h2>
            <p>Se han realizado múltiples reservas en una sola orden.</p>
        </div>

        @foreach($appointments as $appointment)
            <div class="appointment">
                <h3>{{ $appointment->service->title }}</h3>
                <p><strong>Cliente:</strong> {{ $appointment->customer_name }}</p>
                <p><strong>Email:</strong> {{ $appointment->customer_email }}</p>
                <p><strong>Teléfono:</strong> {{ $appointment->customer_phone }}</p>

                @if($appointment->scheduled_at)
                    <p><strong>Fecha Solicitada:</strong>
                        {{ \Carbon\Carbon::parse($appointment->scheduled_at)->format('d/m/Y H:i') }}</p>
                    <p><strong>Tipo:</strong> {{ ucfirst($appointment->session_type) }}</p>

                    @if($appointment->participants_data)
                        <p><strong>Participantes Adicionales:</strong></p>
                        <ul>
                            @foreach(json_decode($appointment->participants_data) as $p)
                                <li>{{ $p->name }} {{ $p->surname }} ({{ $p->gender }})</li>
                            @endforeach
                        </ul>
                    @endif

                    <div style="margin-top: 15px;">
                        <a href="{{ route('booking.accept.signed', $appointment->id) }}" class="btn">Aceptar Reserva</a>
                    </div>
                @else
                    <p><strong>Estado:</strong> Fecha Pendiente (Open Date)</p>
                    <p>El cliente seleccionará la fecha desde su panel.</p>
                @endif
            </div>
        @endforeach
    </div>
</body>

</html>