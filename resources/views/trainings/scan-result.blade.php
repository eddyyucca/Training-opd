<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $mode === 'attendance' ? 'Attendance Result' : 'Registration Result' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --brand-50: #eff6ff;
            --brand-600: #2563eb;
            --brand-700: #1d4ed8;
            --slate-50: #f8fafc;
            --slate-200: #e2e8f0;
            --slate-500: #64748b;
            --slate-700: #334155;
            --slate-900: #0f172a;
            --success-50: #ecfdf5;
            --success-700: #047857;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: 'Manrope', sans-serif;
            background:
                radial-gradient(circle at top left, rgba(37, 99, 235, 0.12), transparent 24%),
                linear-gradient(180deg, #f6f9ff 0%, #eef4ff 100%);
            color: var(--slate-900);
        }

        .page {
            max-width: 720px;
            margin: 0 auto;
            padding: 16px 12px 28px;
        }

        .shell {
            overflow: hidden;
            border: 1px solid rgba(148, 163, 184, 0.18);
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.96);
            box-shadow: 0 16px 36px rgba(15, 23, 42, 0.07);
        }

        .hero {
            padding: 18px;
            background: linear-gradient(135deg, #17357a 0%, #122a63 45%, #0d1c42 100%);
            color: #fff;
        }

        .eyebrow {
            display: inline-flex;
            padding: 6px 10px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.12);
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .hero h1 {
            margin: 12px 0 4px;
            font-size: clamp(1.25rem, 4vw, 1.8rem);
            line-height: 1.15;
        }

        .hero p {
            margin: 0;
            font-size: 13px;
            color: rgba(255, 255, 255, 0.78);
        }

        .content {
            padding: 14px;
        }

        .success-box {
            border: 1px solid #bbf7d0;
            border-radius: 16px;
            background: var(--success-50);
            padding: 14px;
            color: var(--success-700);
        }

        .success-box strong {
            display: block;
            margin-bottom: 4px;
            font-size: 14px;
        }

        .success-box span {
            font-size: 12px;
        }

        .grid {
            display: grid;
            gap: 12px;
            margin-top: 12px;
        }

        .card {
            border: 1px solid var(--slate-200);
            border-radius: 16px;
            background: #fff;
            padding: 12px;
        }

        .card h2 {
            margin: 0 0 8px;
            font-size: 14px;
        }

        .row {
            display: flex;
            align-items: start;
            justify-content: space-between;
            gap: 12px;
            padding: 10px 0;
            border-bottom: 1px solid var(--slate-200);
        }

        .row:last-child {
            border-bottom: 0;
            padding-bottom: 0;
        }

        .row strong {
            display: block;
            font-size: 13px;
            color: var(--slate-900);
        }

        .row small,
        .row span {
            font-size: 12px;
            color: var(--slate-500);
        }

        .actions {
            display: grid;
            gap: 10px;
            margin-top: 14px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 42px;
            border-radius: 12px;
            border: 0;
            padding: 0 14px;
            font: inherit;
            font-size: 14px;
            font-weight: 800;
            text-decoration: none;
            cursor: pointer;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--brand-600), var(--brand-700));
            color: #fff;
            box-shadow: 0 14px 26px rgba(37, 99, 235, 0.18);
        }

        .btn-light {
            background: var(--slate-50);
            color: var(--slate-700);
            border: 1px solid var(--slate-200);
        }

        @media (min-width: 640px) {
            .grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .actions {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="shell">
            <div class="hero">
                <span class="eyebrow">{{ $mode === 'attendance' ? 'Attendance Completed' : 'Registration Completed' }}</span>
                <h1>{{ $training->name }}</h1>
                <p>{{ $training->department?->name ?: 'Department not set' }}</p>
            </div>

            <div class="content">
                <div class="success-box">
                    <strong>
                        @if ($mode === 'attendance')
                            {{ $status === 'already_attended' ? 'Attendance was already recorded.' : 'Attendance saved successfully.' }}
                        @else
                            {{ $status === 'already_registered' ? 'Registration was already recorded.' : 'Registration saved successfully.' }}
                        @endif
                    </strong>
                    <span>Your submission has been stored in the system.</span>
                </div>

                <div class="grid">
                    <div class="card">
                        <h2>Employee Detail</h2>
                        <div class="row">
                            <div>
                                <strong>Name</strong>
                                <small>Selected employee</small>
                            </div>
                            <span>{{ $employee->name }}</span>
                        </div>
                        <div class="row">
                            <div>
                                <strong>Employee ID</strong>
                                <small>Registered employee code</small>
                            </div>
                            <span>{{ $employee->nik }}</span>
                        </div>
                        <div class="row">
                            <div>
                                <strong>WhatsApp Number</strong>
                                <small>Saved contact detail</small>
                            </div>
                            <span>{{ $employee->whatsapp_number ?: '-' }}</span>
                        </div>
                        <div class="row">
                            <div>
                                <strong>Email Address</strong>
                                <small>Saved email detail</small>
                            </div>
                            <span>{{ $employee->email ?: '-' }}</span>
                        </div>
                    </div>

                    <div class="card">
                        <h2>{{ $mode === 'attendance' ? 'Attendance Detail' : 'Registration Detail' }}</h2>
                        <div class="row">
                            <div>
                                <strong>Training Date</strong>
                                <small>Scheduled date</small>
                            </div>
                            <span>{{ $training->start_date?->format('d M Y') ?: '-' }}</span>
                        </div>
                        <div class="row">
                            <div>
                                <strong>Training Time</strong>
                                <small>Session time</small>
                            </div>
                            <span>{{ $training->start_time?->format('H:i') ?: '-' }} - {{ $training->end_time?->format('H:i') ?: '-' }}</span>
                        </div>
                        <div class="row">
                            <div>
                                <strong>Registered At</strong>
                                <small>Stored in database</small>
                            </div>
                            <span>{{ optional($registeredAt)->format('d M Y H:i') ?: '-' }}</span>
                        </div>
                        @if ($mode === 'attendance')
                            <div class="row">
                                <div>
                                    <strong>Attended At</strong>
                                    <small>Stored in database</small>
                                </div>
                                <span>{{ optional($attendedAt)->format('d M Y H:i') ?: '-' }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="actions">
                    <a href="{{ $mode === 'attendance' ? route('trainings.attendance.show', $training->attendance_token) : route('trainings.registration.show', $training->registration_token) }}" class="btn btn-light">Back to Form</a>
                    <a href="{{ $mode === 'attendance' ? route('trainings.attendance.show', $training->attendance_token) : route('trainings.registration.show', $training->registration_token) }}" class="btn btn-primary">Submit Another</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
