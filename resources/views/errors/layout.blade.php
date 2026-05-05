<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Error' }} — OPD Training</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Manrope', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background:
                radial-gradient(circle at 20% 20%, rgba(37, 99, 235, 0.10) 0%, transparent 40%),
                radial-gradient(circle at 80% 80%, rgba(99, 102, 241, 0.08) 0%, transparent 40%),
                linear-gradient(180deg, #f0f4ff 0%, #e8eeff 100%);
            color: #0f172a;
            padding: 24px;
        }

        .card {
            width: 100%;
            max-width: 520px;
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid rgba(148, 163, 184, 0.20);
            border-radius: 28px;
            box-shadow: 0 24px 56px rgba(15, 23, 42, 0.10);
            overflow: hidden;
        }

        .card-top {
            padding: 40px 40px 32px;
            background: linear-gradient(135deg, #17357a 0%, #122a63 50%, #0d1c42 100%);
            text-align: center;
        }

        .code {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.12);
            font-size: 2rem;
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 16px;
        }

        .card-top h1 {
            font-size: 1.5rem;
            font-weight: 800;
            color: #ffffff;
            line-height: 1.2;
        }

        .card-top p {
            margin-top: 8px;
            font-size: 0.875rem;
            color: rgba(255, 255, 255, 0.72);
            line-height: 1.6;
        }

        .card-body {
            padding: 32px 40px 40px;
            text-align: center;
        }

        .detail {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 14px 18px;
            font-size: 0.8125rem;
            color: #475569;
            line-height: 1.6;
            text-align: left;
            margin-bottom: 24px;
        }

        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 10px 22px;
            border-radius: 12px;
            font: inherit;
            font-size: 0.875rem;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            border: none;
            transition: opacity 0.15s;
        }

        .btn:hover { opacity: 0.88; }

        .btn-primary {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: #ffffff;
            box-shadow: 0 8px 20px rgba(37, 99, 235, 0.22);
        }

        .btn-light {
            background: #f1f5f9;
            color: #334155;
        }

        .brand {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 24px;
            font-size: 0.75rem;
            color: #94a3b8;
        }

        .brand-mark {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 22px;
            height: 22px;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            border-radius: 6px;
            font-size: 10px;
            font-weight: 800;
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="card-top">
            <div class="code">{{ $code ?? '?' }}</div>
            <h1>{{ $title ?? 'Something went wrong' }}</h1>
            <p>{{ $subtitle ?? 'An unexpected error occurred. Please try again.' }}</p>
        </div>
        <div class="card-body">
            @if (isset($detail))
            <div class="detail">{{ $detail }}</div>
            @endif
            <div class="actions">
                @if (isset($backUrl))
                    <a href="{{ $backUrl }}" class="btn btn-light">← Go Back</a>
                @else
                    <a href="javascript:history.back()" class="btn btn-light">← Go Back</a>
                @endif
                <a href="{{ route('dashboard') }}" class="btn btn-primary">Dashboard</a>
            </div>
            <div class="brand">
                <span class="brand-mark">OP</span>
                Organization and People Development
            </div>
        </div>
    </div>
</body>
</html>
