<!DOCTYPE html>
<html>

<head>
    <title>Service Cancelled</title>
</head>

<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2 style="color: #dc2626;">Service Assignment Cancelled</h2>
        <p>Dear Partner,</p>
        <p>The following service assignment has been <strong>CANCELLED</strong> by the administration.</p>

        <div style="background-color: #fca5a5; padding: 15px; border-radius: 8px; margin: 20px 0; color: #7f1d1d;">
            <p><strong>Ref:</strong> {{ $item->reservation->booking_ref }}</p>
            <p><strong>Service:</strong> {{ $item->service_name }}</p>
            <p><strong>Date:</strong> {{ $item->date->format('d M Y') }}</p>
        </div>

        <p>You are no longer required to perform this service. If you have any questions, please contact us.</p>
    </div>
</body>

</html>