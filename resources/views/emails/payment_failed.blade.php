<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { width: 80%; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 10px; }
        .header { background: #FF9800; color: white; padding: 10px; text-align: center; border-radius: 10px 10px 0 0; }
        .content { padding: 20px; }
        .footer { text-align: center; font-size: 0.8em; color: #777; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Payment Failed</h1>
        </div>
        <div class="content">
            <p>Hi {{ $user->name }},</p>
            <p>We're sorry, but the recurring payment for your subscription has failed.</p>
            <p>Please update your payment method or ensure your account has sufficient funds to avoid any service interruption.</p>
            <p>Our system will attempt to process the payment again soon.</p>
            <p><a href="{{ env('APP_URL') }}/billing" style="display:inline-block; padding:10px 20px; background:#f44336; color:white; text-decoration:none; border-radius:5px;">Update Billing Info</a></p>
            <br>
            <p>Best Regards,<br>The {{ config('app.name') }} Team</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
