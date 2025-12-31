<!DOCTYPE html>
<html>

<head>
    <title>Booking Voucher</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
            color: #333;
        }

        .header {
            width: 100%;
            border-bottom: 2px solid #0891b2;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #0891b2;
        }

        .title {
            text-align: right;
            float: right;
            font-size: 18px;
            font-weight: bold;
        }

        .section {
            margin-bottom: 20px;
        }

        .label {
            font-weight: bold;
            color: #555;
            width: 120px;
            display: inline-block;
        }

        .value {
            color: #000;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .table th,
        .table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .table th {
            background-color: #f3f4f6;
        }

        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 10px;
            color: #888;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>

<body>
    <div class="header">
        <span class="logo">Travel Agency</span>
        <span class="title">BOOKING VOUCHER</span>
        <div style="clear: both;"></div>
    </div>

    <div class="section">
        <h3>Booking Reference: {{ $item->reservation->booking_ref }}</h3>
        <div><span class="label">Date:</span> {{ $item->created_at->format('d M Y') }}</div>
        <div><span class="label">Status:</span> Confirmed</div>
    </div>

    <div class="section">
        <h3>Client Details</h3>
        <div><span class="label">Name:</span> {{ $item->reservation->contact_name }}
            {{ $item->reservation->contact_surname }}</div>
        <div><span class="label">Pax:</span> {{ $item->pax }} Passengers</div>
    </div>

    <div class="section">
        <h3>Service Details</h3>
        <table class="table">
            <tr>
                <th>Service Name</th>
                <td>{{ $item->service_name }}</td>
            </tr>
            <tr>
                <th>Zone/Route</th>
                <td>{{ $item->zone_name }}</td>
            </tr>
            <tr>
                <th>Date</th>
                <td>{{ $item->date->format('d M Y') }} @if($item->time) at
                {{ \Carbon\Carbon::parse($item->time)->format('H:i') }} @endif</td>
            </tr>
            @if($item->airline || $item->arrival_flight_number)
                <tr>
                    <th>Flight Info</th>
                    <td>
                        Arrival: {{ $item->airline }} {{ $item->arrival_flight_number }}
                        @if($item->arrival_time) @ {{ $item->arrival_time }} @endif
                    </td>
                </tr>
            @endif
            @if($item->pickup_time)
                <tr>
                    <th>Pickup Time</th>
                    <td><strong>{{ \Carbon\Carbon::parse($item->pickup_time)->format('H:i') }}</strong> (Please be ready at
                        lobby)</td>
                </tr>
            @endif
        </table>
    </div>

    <div class="section">
        <h3>Instructions</h3>
        <p>Please present this voucher to the driver/operator. If you have any issues, contact us via WhatsApp:
            +529981234567.</p>
    </div>

    <div class="footer">
        &copy; {{ date('Y') }} Travel Agency. All rights reserved.
    </div>
</body>

</html>