<!DOCTYPE html>
<html>

<head>
    <title>Provider Accepted Service</title>
</head>

<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2 style="color: #059669;">Provider Accepted Service</h2>
        <p>The provider has accepted the service request.</p>

        <div style="background-color: #f3f4f6; padding: 15px; border-radius: 8px; margin: 20px 0;">
            <p><strong>Provider:</strong> {{ $item->assignedProvider->name ?? 'N/A' }}</p>
            <p><strong>Ref:</strong> {{ $item->reservation->booking_ref }}</p>
            <p><strong>Service:</strong> {{ $item->service_name }}</p>
            <p><strong>Confirmed At:</strong>
                {{ \Carbon\Carbon::parse($item->vendor_confirmed_at)->format('d M Y H:i') }}</p>
        </div>

        <p>If you wish to CANCEL this assignment (and reassign to another provider), click below:</p>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ $cancelUrl }}"
                style="background-color: #dc2626; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; font-weight: bold;">CANCEL
                ASSIGNMENT</a>
        </div>
    </div>
</body>

</html>