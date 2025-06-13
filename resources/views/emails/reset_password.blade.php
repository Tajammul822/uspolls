<p>Hello,</p>
<p>You requested a password reset for your account. Click the link below to choose a new password:</p>

<p><a href="{{ url('password/reset/' . $token . '?email=' . urlencode($email)) }}">
    Reset Password
</a></p>

<p>This link will expire in 60 minutes.</p>

<p>If you did not request a password reset, no further action is required.</p>

<p>Regards,<br>Your Application Team</p>