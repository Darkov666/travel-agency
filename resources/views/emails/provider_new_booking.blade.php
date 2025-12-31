<!DOCTYPE html>
<html>

<head>
    <title>New Service Request</title>
</head>

<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2 style="color: #0891b2;">New Service Request</h2>
        <p>Dear Partner,</p>
        <p>You have a new service request. Please review the attached Work Order for details.</p>

        <div style="background-color: #f3f4f6; padding: 15px; border-radius: 8px; margin: 20px 0;">
            <p><strong>Ref:</strong> {{ $item->reservation->booking_ref }}</p>
            <p><strong>Service:</strong> {{ $item->service_name }}</p>
            <p><strong>Date:</strong> {{ $item->date->format('d M Y') }}</p>
            <p><strong>Pickup:</strong> {{ \Carbon\Carbon::parse($item->pickup_time)->format('H:i') }}</p>
        </div>

        <p>Please confirm if you can accept this service by clicking the button below:</p>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ $url }}"
                style="background-color: #059669; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; font-weight: bold;">ACCEPT
                SERVICE</a>
        </div>

        <p>If you cannot accept this service, please contact the administration immediately.</p>
    </div>
</body>

</html>