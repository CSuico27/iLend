@component('mail::message')
# New OTP Request

Hi,

Your new One-Time Password (OTP) is:

# **{{ $otp }}**

Please enter this within 10 minutes to verify your account.

Thanks,<br>
**iLend Team**
@endcomponent
