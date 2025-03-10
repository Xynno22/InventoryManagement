<!DOCTYPE html>
<html lang="en">
<head>
    <title>Reset Password</title>
</head>
<body>
    <p>Hello {{ $email }},</p>
    <p>You recently requested to reset your password. Click the button below to continue:</p>
    <a href="{{ url('reset-password/' . $token . '?email=' . urlencode($email)) }}" style="display:inline-block;padding:10px 15px;background-color:#4F46E5;color:white;text-decoration:none;border-radius:5px;">Reset Password</a>
    <p>If you didn’t request this, you can safely ignore this email.</p>
    <p>Thanks,</p>
    <p>Your App Team</p>
</body>
</html>