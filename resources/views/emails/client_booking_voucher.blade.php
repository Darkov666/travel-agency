<!DOCTYPE html>
<html>

<head>
    <title>Your Booking Voucher</title>
</head>

<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2 style="color: #0891b2;">Booking Confirmed!</h2>
        <p>Dear {{ $item->reservation->contact_name }},</p>
        <p>We are pleased to confirm your booking. Please find your Booking Voucher attached to this email.</p>

        <p><strong>Ref:</strong> {{ $item->reservation->booking_ref }}</p>

        <p>Please present this voucher to your service provider.</p>

        <p>Thank you for choosing us!</p>
    </div>
</body>

</html>