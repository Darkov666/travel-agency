<!DOCTYPE html>
<html>

<head>
    <title>Service Reminder</title>
</head>

<body style="font-family: Arial, sans-serif; line-height: 1.6;">
    <h2>Upcoming Service Reminder</h2>
    <p>This is a reminder that you have a service scheduled in <strong>{{ $hours }} hours</strong>.</p>

    <h3>Service Details</h3>
    <ul>
        <li><strong>Service:</strong> {{ $item->service_name }}</li>
        <li><strong>Time:</strong> {{ $item->time }}</li>
        <li><strong>Date:</strong> {{ $item->date }}</li>
        <li><strong>Location:</strong> {{ $item->zone_name }}</li>
    </ul>

    @if($recipientType === 'provider')
        <p>Please ensure you are prepared for this service.</p>
        <a href="{{ url('/dashboard') }}"
            style="background: #3b82f6; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">View
            Work Order</a>
    @elseif($recipientType === 'client')
        <p>We look forward to serving you!</p>
    @endif

    <p style="font-size: 0.8em; color: #666;">Ref: {{ $item->reservation->booking_ref }}</p>
</body>

</html>