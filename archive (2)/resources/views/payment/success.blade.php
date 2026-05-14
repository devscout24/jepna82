<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful | {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4f46e5;
            --success: #10b981;
            --bg: #f8fafc;
            --text: #1e293b;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Outfit', sans-serif;
        }

        body {
            background-color: var(--bg);
            color: var(--text);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            overflow: hidden;
        }

        .container {
            width: 100%;
            max-width: 480px;
            padding: 2rem;
            text-align: center;
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            border-radius: 2rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.3);
            animation: slideUp 0.8s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(40px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .icon-box {
            width: 80px;
            height: 80px;
            background: var(--success);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 2.5rem;
            box-shadow: 0 10px 20px rgba(16, 185, 129, 0.3);
            animation: scaleIn 0.6s 0.2s cubic-bezier(0.34, 1.56, 0.64, 1) both;
        }

        @keyframes scaleIn {
            from { transform: scale(0); }
            to { transform: scale(1); }
        }

        h1 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: #0f172a;
        }

        p {
            color: #64748b;
            margin-bottom: 2rem;
            line-height: 1.6;
        }

        .details {
            background: rgba(0, 0, 0, 0.03);
            border-radius: 1.5rem;
            padding: 1.5rem;
            margin-bottom: 2rem;
            text-align: left;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.75rem;
            font-size: 0.9rem;
        }

        .detail-row:last-child { margin-bottom: 0; }

        .detail-label { color: #94a3b8; }
        .detail-value { font-weight: 600; color: #334155; }

        .btn {
            display: inline-block;
            width: 100%;
            padding: 1rem 2rem;
            background: var(--primary);
            color: white;
            text-decoration: none;
            border-radius: 1rem;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.2);
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(79, 70, 229, 0.3);
            background: #4338ca;
        }

        /* Abstract background shapes */
        .shape {
            position: absolute;
            z-index: -1;
            filter: blur(80px);
            border-radius: 50%;
        }

        .shape-1 { width: 300px; height: 300px; background: rgba(79, 70, 229, 0.15); top: -100px; left: -100px; }
        .shape-2 { width: 400px; height: 400px; background: rgba(16, 185, 129, 0.15); bottom: -150px; right: -150px; }

    </style>
</head>
<body>
    <div class="shape shape-1"></div>
    <div class="shape shape-2"></div>

    <div class="container">
        <div class="icon-box">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="20 6 9 17 4 12"></polyline>
            </svg>
        </div>
        <h1>Payment Success!</h1>
        <p>Thank you for your purchase. Your account has been credited successfully.</p>

        @if($session)
        <div class="details">
            <div class="detail-row">
                <span class="detail-label">Amount Paid</span>
                <span class="detail-value">{{ strtoupper($session->currency) }} {{ number_format($session->amount_total / 100, 2) }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Order ID</span>
                <span class="detail-value">#{{ substr($session->id, -8) }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Customer</span>
                <span class="detail-value">{{ $session->customer_details->email }}</span>
            </div>
        </div>
        @endif

        <a href="{{ url('/dashboard') }}" class="btn">Go to Dashboard</a>
    </div>
</body>
</html>
