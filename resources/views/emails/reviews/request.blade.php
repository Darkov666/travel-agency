# Share Your Experience

Hi {{ $review->reservation->contact_name }},

Thank you for choosing {{ config('app.name') }}. We hope you enjoyed your trip.

Please take a moment to rate your experience.

<x-mail::button :url="route('reviews.create', ['token' => $review->token])">
    Write a Review
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>