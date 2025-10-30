<x-mail::message>
# Your Verification Code

Welcome to Fintich Hub!

Your One-Time Password (OTP) is:

<x-mail::panel>
{{ $otp }}
</x-mail::panel>

This code will expire in 10 minutes.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
