@component('mail::message')
# How was your trip?

Hi there,

We hope you enjoyed your service. Please take a moment to rate your driver and the service provided.
Your public review helps other travelers and helps us reward our best drivers.

@component('mail::button', ['url' => $url])
Rate Your Trip
@endcomponent

Thanks,<br>
The Team
@endcomponent