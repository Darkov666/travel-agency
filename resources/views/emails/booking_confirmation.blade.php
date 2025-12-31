<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            font-family: sans-serif;
            color: #333;
            line-height: 1.6;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background: #0891b2;
            color: white;
            padding: 15px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }

        .content {
            padding: 20px;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
        }

        .footer {
            text-align: center;
            font-size: 12px;
            color: #6b7280;
            margin-top: 20px;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #25D366;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .table th,
        .table td {
            text-align: left;
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }

        .item-details {
            background-color: #fff;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 4px;
            border: 1px solid #eee;
        }

        .pickup-note {
            background-color: #fff3cd;
            color: #856404;
            padding: 5px;
            border-radius: 3px;
            font-size: 0.9em;
            margin-top: 5px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            @if($isAdminCopy)
                <h1 style="background-color: #f59e0b; padding: 5px;">ADMIN COPY</h1>
            @endif
            <h1>Booking Confirmation</h1>
            <p>Ref: {{ $reservation->booking_ref }}</p>
        </div>

        <div class="content">
            <p>Dear {{ $reservation->contact_name }},</p>
            <p>Thank you for booking with us! Your reservation has been successfully confirmed.</p>

            <h3>Booking Details</h3>
            <p>
                <strong>Status:</strong> {{ ucfirst($reservation->status) }}<br>
                <strong>Payment:</strong> {{ ucfirst($reservation->payment_method) }}
                ({{ ucfirst($reservation->payment_choice) }})<br>
                <strong>Amount Paid:</strong> ${{ number_format($reservation->amount_paid, 2) }}<br>
                <strong>Balance Due:</strong> ${{ number_format($reservation->balance_due, 2) }}
            </p>

            @if($reservation->status === 'pending' || $reservation->payment_method === 'transfer')
                <div
                    style="background-color: #fff3cd; color: #856404; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #ffeeba;">
                    <h3 style="margin-top: 0; color: #856404;">Pending Payment Instructions</h3>
                    <p>Your reservation is pending confirmation. Please transfer the <strong>Deposit
                            (${{ number_format($reservation->total_amount * 0.20, 2) }})</strong> or Full Amount to:</p>
                    <p>
                        <strong>Bank:</strong> BBVA<br>
                        <strong>Account Holder:</strong> Travel Agency SA<br>
                        <strong>CLABE:</strong> 012345678901234567
                    </p>
                    <p>Once transferred, please send the proof via WhatsApp using the button below to confirm your booking.
                    </p>
                </div>
            @endif

            <h3>Services</h3>
            @foreach($reservation->items as $item)
                <div class="item-details">
                    <strong>{{ $item->service_name }}</strong><br>
                    Zone: {{ $item->zone_name }}<br>
                    Date: {{ $item->date->format('d M Y') }}
                    @if($item->time) at {{ \Carbon\Carbon::parse($item->time)->format('H:i') }} @endif
                    <br>
                    Pax: {{ $item->pax }} (Units: {{ $item->units }})<br>

                    @if($item->airline || $item->arrival_flight_number)
                        <div style="margin-top: 5px; font-size: 0.9em; color: #555;">
                            <strong>Arrival Flight:</strong> {{ $item->airline }} {{ $item->arrival_flight_number }}
                            @if($item->arrival_time) @ {{ $item->arrival_time }} @endif
                            (Term: {{ $item->arrival_terminal }})
                        </div>
                    @endif

                    @if($item->return_date)
                        <br>
                        <strong>Return Service:</strong> {{ $item->return_date->format('d M Y') }}<br>
                        @if($item->departure_airline || $item->departure_flight_number)
                            <div style="margin-top: 5px; font-size: 0.9em; color: #555;">
                                <strong>Departure Flight:</strong> {{ $item->departure_airline }}
                                {{ $item->departure_flight_number }}
                                @if($item->departure_time) @ {{ $item->departure_time }} @endif
                                (Term: {{ $item->departure_terminal }})
                            </div>
                            @if($item->pickup_time)
                                <div class="pickup-note">
                                    <strong>Calculated Pickup Time:</strong>
                                    {{ \Carbon\Carbon::parse($item->pickup_time)->format('H:i') }}
                                    <br><i>(Based on {{ $item->flight_type }} flight departure)</i>
                                </div>
                            @endif
                        @endif
                    @endif
                </div>
            @endforeach

            @if($isAdminCopy && ($reservation->status === 'pending' || $reservation->status === 'draft'))
                <div
                    style="text-align: center; margin: 30px 0; padding: 20px; background-color: #f3f4f6; border-radius: 8px;">
                    <h3 style="margin-top: 0;">Admin Action</h3>
                    <p>Confirm this reservation if payment has been verified.</p>
                    <a href="{{ route('admin.reservations.confirm', $reservation->booking_ref) }}"
                        style="display: inline-block; padding: 12px 24px; background-color: #0891b2; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; font-size: 16px;">
                        CONFIRM RESERVATION
                    </a>
                </div>
            @endif

            <p>If you have any questions or need to make changes, please contact us immediately via WhatsApp:</p>

            <div style="text-align: center; margin: 20px 0;">
                <a href="https://wa.me/529981234567?text=Hello, I have a question about my booking {{ $reservation->booking_ref }}"
                    class="btn">
                    Chat on WhatsApp
                </a>
            </div>
        </div>

        <div class="footer">
            &copy; {{ date('Y') }} Travel Agency. All rights reserved.
        </div>
    </div>
</body>

</html>