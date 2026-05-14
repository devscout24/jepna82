<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { width: 80%; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 10px; }
        .header { background: #2196F3; color: white; padding: 10px; text-align: center; border-radius: 10px 10px 0 0; }
        .content { padding: 20px; }
        .footer { text-align: center; font-size: 0.8em; color: #777; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Credits Added!</h1>
        </div>
        <div class="content">
            <p>Hi {{ $user->name }},</p>
            <p>You have successfully purchased the <strong>{{ $package->title }}</strong> credit pack.</p>
            <p><strong>{{ $package->scan_credits }}</strong> credits have been added to your account.</p>
            <p>You can use these credits at any time to scan your contracts.</p>
            <br>
            <p>Best Regards,<br>The {{ config('app.name') }} Team</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
