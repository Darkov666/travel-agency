@component('mail::message')
# Help us improve!

Hi there,

Thank you for choosing us. We would love to hear about your experience using our website.
Your feedback helps us provide a better experience for everyone.

@component('mail::button', ['url' => $url])
Rate Website Experience
@endcomponent

Thanks,<br>
The Team
@endcomponent