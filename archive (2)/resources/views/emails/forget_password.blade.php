<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forget Password OTP</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            color: #333333;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .header {
            background-color: #007bff;
            color: #ffffff;
            text-align: center;
            padding: 20px 0;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 30px;
            line-height: 1.6;
        }
        .content h2 {
            color: #333333;
            margin-top: 0;
        }
        .otp-container {
            text-align: center;
            margin: 30px 0;
        }
        .otp-code {
            display: inline-block;
            font-size: 32px;
            font-weight: bold;
            color: #007bff;
            background-color: #f8f9fa;
            padding: 15px 30px;
            border-radius: 6px;
            letter-spacing: 4px;
            border: 1px dashed #007bff;
        }
        .footer {
            background-color: #f8f9fa;
            text-align: center;
            padding: 20px;
            font-size: 14px;
            color: #666666;
            border-top: 1px solid #eeeeee;
        }
        .footer p {
            margin: 5px 0 0 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Password Reset Request</h1>
        </div>
        <div class="content">
            <h2>Hello {{ $user->name ?? 'User' }},</h2>
            <p>We received a request to reset your password. Use the following One-Time Password (OTP) to proceed with your password reset:</p>
            
            <div class="otp-container">
                <div class="otp-code">{{ $user->otp }}</div>
            </div>
            
            <p>This OTP is valid for the next 2 minutes. If you did not request a password reset, please ignore this email or contact support if you have concerns.</p>
            <p>Best regards,<br>The Application Team</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} Our Application. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
