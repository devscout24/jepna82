<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Cancelled | {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4f46e5;
            --danger: #ef4444;
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
            background: var(--danger);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 2.5rem;
            box-shadow: 0 10px 20px rgba(239, 68, 68, 0.3);
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
        }

        .btn:hover {
            transform: translateY(-2px);
            background: #4338ca;
        }

        .btn-outline {
            background: transparent;
            color: var(--primary);
            border: 2px solid var(--primary);
            margin-top: 1rem;
        }

        .btn-outline:hover {
            background: rgba(79, 70, 229, 0.05);
            transform: translateY(-2px);
        }

        .shape {
            position: absolute;
            z-index: -1;
            filter: blur(80px);
            border-radius: 50%;
        }

        .shape-1 { width: 300px; height: 300px; background: rgba(239, 68, 68, 0.1); top: -100px; left: -100px; }
    </style>
</head>
<body>
    <div class="shape shape-1"></div>
    <div class="container">
        <div class="icon-box">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </div>
        <h1>Payment Cancelled</h1>
        <p>The payment process was cancelled. No charges were made to your account.</p>

        <a href="{{ url('/dashboard') }}" class="btn">Return to Dashboard</a>
        <a href="javascript:history.back()" class="btn btn-outline">Try Again</a>
    </div>
</body>
</html>
