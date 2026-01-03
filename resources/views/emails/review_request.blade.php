<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            font-family: sans-serif;
        }

        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #3b82f6;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <h2>Hello {{ $reservation->contact_name }},</h2>

    <p>We hope you enjoyed your trip with us!</p>

    <p>Your feedback is incredibly valuable to us and helps us improve our services. Could you please take a moment to
        rate your experience?</p>

    <p style="text-align: center; margin: 30px 0;">
        <a href="{{ $reviewUrl }}" class="button">Rate Your Experience</a>
    </p>

    <p>If the button doesn't work, you can copy and paste this link into your browser:</p>
    <p>{{ $reviewUrl }}</p>

    <p>Thank you,<br>The Team</p>
</body>

</html>