<!-- resources/views/emails/reset-password.blade.php -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
</head>
<body>
    <h1>Reset Password Request</h1>
    <p>Hi,</p>
    <p>We received a request to reset your password. Please click the link below to reset your password:</p>
    <a href="{{ url('api/reset-password/'.$token.'?email='.$email) }}">Reset Password</a>
    <p>If you did not request a password reset, no further action is required.</p>
</body>
</html>
