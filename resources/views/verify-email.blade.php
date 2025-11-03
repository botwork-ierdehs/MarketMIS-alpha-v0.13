@component('mail::message')
# Welcome to {{ config('app.name') }}!

Please click the button below to verify your email address and activate your account.

@component('mail::button', ['url' => $actionUrl])
Verify Email
@endcomponent

If you did not create an account, no further action is required.

Thanks,  
The {{ config('app.name') }} Team
@endcomponent
