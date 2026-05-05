<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $mode === 'attendance' ? 'Training Attendance' : 'Training Registration' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --brand-50: #eff6ff;
            --brand-100: #dbeafe;
            --brand-600: #2563eb;
            --brand-700: #1d4ed8;
            --slate-50: #f8fafc;
            --slate-100: #f1f5f9;
            --slate-200: #e2e8f0;
            --slate-300: #cbd5e1;
            --slate-500: #64748b;
            --slate-700: #334155;
            --slate-900: #0f172a;
            --success-50: #ecfdf5;
            --success-700: #047857;
            --danger-50: #fef2f2;
            --danger-700: #b91c1c;
            --amber-50: #fffbeb;
            --amber-700: #b45309;
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

        .page { max-width: 760px; margin: 0 auto; padding: 12px; }

        .shell {
            overflow: hidden;
            border: 1px solid rgba(148, 163, 184, 0.18);
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.96);
            box-shadow: 0 16px 36px rgba(15, 23, 42, 0.07);
        }

        .hero {
            padding: 16px;
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

        .hero h1 { margin: 10px 0 4px; font-size: clamp(1.2rem, 4vw, 1.7rem); line-height: 1.15; }
        .hero p { margin: 0; font-size: 13px; color: rgba(255, 255, 255, 0.78); }

        .hero-meta { display: grid; gap: 8px; margin-top: 10px; }

        .hero-chip {
            display: inline-flex;
            align-items: center;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.12);
            padding: 8px 10px;
            font-size: 12px;
            font-weight: 700;
            color: #fff;
        }

        .content { padding: 12px; }

        .panel {
            border: 1px solid var(--slate-200);
            border-radius: 16px;
            background: #fff;
            padding: 12px;
        }

        .panel + .panel { margin-top: 12px; }
        .panel h2 { margin: 0 0 4px; font-size: 14px; }
        .panel p { margin: 0; font-size: 12px; line-height: 1.45; color: var(--slate-500); }

        .quota-grid { display: grid; gap: 8px; margin-top: 10px; }

        .quota-chip {
            display: inline-flex;
            align-items: center;
            border-radius: 12px;
            padding: 8px 10px;
            font-size: 12px;
            font-weight: 700;
        }

        .quota-chip.primary { background: var(--brand-50); color: var(--brand-700); }
        .quota-chip.secondary { background: var(--slate-50); color: var(--slate-700); }

        .alert {
            border-radius: 12px;
            padding: 10px 12px;
            margin-bottom: 10px;
            font-size: 12px;
            font-weight: 700;
        }

        .alert-success { background: var(--success-50); color: var(--success-700); }
        .alert-error { background: var(--danger-50); color: var(--danger-700); }

        /* Tab switcher */
        .tab-bar {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 6px;
            margin-bottom: 14px;
            background: var(--slate-100);
            border-radius: 14px;
            padding: 4px;
        }

        .tab-btn {
            border: 0;
            border-radius: 10px;
            padding: 9px 10px;
            font: inherit;
            font-size: 12px;
            font-weight: 800;
            cursor: pointer;
            background: transparent;
            color: var(--slate-500);
            transition: background 0.15s, color 0.15s;
        }

        .tab-btn.active {
            background: #fff;
            color: var(--brand-700);
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.08);
        }

        .tab-panel { display: none; }
        .tab-panel.active { display: block; }

        .field + .field { margin-top: 10px; }
        .compact-row .field + .field { margin-top: 0; }
        .contact-row { margin-top: 14px; }

        .field label {
            display: block;
            margin-bottom: 6px;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: var(--brand-700);
        }

        .field input,
        .field select {
            width: 100%;
            min-height: 42px;
            border: 1px solid var(--slate-200);
            border-radius: 12px;
            background: #fff;
            padding: 0 12px;
            color: var(--slate-900);
            font: inherit;
            font-size: 14px;
            outline: none;
            appearance: none;
        }

        .field input:focus,
        .field select:focus {
            border-color: #93c5fd;
            box-shadow: 0 0 0 4px rgba(191, 219, 254, 0.55);
        }

        .employee-list {
            display: grid;
            gap: 8px;
            max-height: 240px;
            overflow-y: auto;
            padding-right: 2px;
            margin-top: 8px;
        }

        .employee-item {
            width: 100%;
            border: 1px solid var(--slate-200);
            border-radius: 14px;
            background: linear-gradient(180deg, rgba(255,255,255,0.98), rgba(248,250,252,0.92));
            padding: 10px;
            text-align: left;
            cursor: pointer;
            transition: border-color 0.15s ease, background 0.15s ease, box-shadow 0.15s ease;
        }

        .employee-item:hover { border-color: var(--slate-300); }

        .employee-item.active {
            border-color: #93c5fd;
            background: linear-gradient(180deg, rgba(239,246,255,0.96), rgba(219,234,254,0.85));
            box-shadow: 0 12px 24px rgba(37, 99, 235, 0.10);
        }

        .employee-name { display: block; font-size: 13px; font-weight: 800; color: var(--slate-900); }
        .employee-id { display: block; margin-top: 2px; font-size: 11px; color: var(--slate-500); }

        .employee-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 4px 8px;
            margin-top: 6px;
            font-size: 11px;
            color: var(--slate-700);
        }

        .selected-panel {
            margin-top: 12px;
            border: 1px dashed var(--slate-300);
            border-radius: 12px;
            padding: 10px;
            background: var(--slate-50);
            font-size: 12px;
            color: var(--slate-700);
        }

        .selected-panel strong { display: block; margin-bottom: 2px; color: var(--slate-900); }

        .empty {
            border: 1px dashed var(--slate-200);
            border-radius: 14px;
            padding: 12px;
            text-align: center;
            color: var(--slate-500);
            font-size: 12px;
        }

        .external-notice {
            border-radius: 12px;
            padding: 10px 12px;
            margin-bottom: 12px;
            font-size: 12px;
            font-weight: 600;
            background: var(--amber-50);
            color: var(--amber-700);
        }

        .submit-bar {
            margin-top: 12px;
            padding-top: 12px;
            border-top: 1px solid var(--slate-200);
        }

        .submit-bar button {
            width: 100%;
            min-height: 42px;
            border: 0;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--brand-600), var(--brand-700));
            color: #fff;
            font: inherit;
            font-size: 14px;
            font-weight: 800;
            cursor: pointer;
            box-shadow: 0 14px 26px rgba(37, 99, 235, 0.18);
        }

        .submit-bar button:disabled {
            opacity: 0.55;
            cursor: not-allowed;
            box-shadow: none;
        }

        @media (min-width: 640px) {
            .hero-meta,
            .compact-row {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .hero-meta,
            .quota-grid,
            .compact-row {
                display: grid;
                gap: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="shell">
            <div class="hero">
                <span class="eyebrow">{{ $mode === 'attendance' ? 'Attendance' : 'Registration' }}</span>
                <h1>{{ $training->name }}</h1>
                <p>{{ $training->provider ?: 'Provider not set' }}</p>
                <div class="hero-meta">
                    <span class="hero-chip">{{ $training->start_date?->format('d M Y') ?: 'Date not set' }}</span>
                    <span class="hero-chip">{{ $training->start_time?->format('H:i') ?: '-' }} - {{ $training->end_time?->format('H:i') ?: '-' }}</span>
                </div>
            </div>

            <div class="content">
                <div class="panel">
                    <h2>{{ $mode === 'attendance' ? 'Attendance Access' : 'Registration Access' }}</h2>
                    <p>Choose your participant type below to continue.</p>
                    @if ($mode === 'registration')
                        <div class="quota-grid">
                            <div class="quota-chip primary">Quota: {{ $training->quota ? number_format($training->quota, 0, ',', '.') : 'Unlimited' }}</div>
                            <div class="quota-chip secondary">Remaining seats: {{ $training->quota ? max($training->quota - $training->registered_employees_count, 0) : 'Unlimited' }}</div>
                        </div>
                    @endif
                </div>

                <div class="panel">
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-error">{{ $errors->first() }}</div>
                    @endif

                    {{-- Tab bar --}}
                    <div class="tab-bar" id="tab-bar">
                        <button type="button" class="tab-btn active" data-tab="internal">
                            Internal Karyawan
                        </button>
                        <button type="button" class="tab-btn" data-tab="external">
                            Peserta Eksternal
                        </button>
                    </div>

                    {{-- Tab: Internal --}}
                    <div class="tab-panel active" id="tab-internal">
                        <form method="POST" action="{{ $mode === 'attendance' ? route('trainings.attendance.submit', $training->attendance_token) : route('trainings.registration.submit', $training->registration_token) }}">
                            @csrf
                            <input type="hidden" name="register_mode" value="existing">
                            <input type="hidden" name="employee_id" id="employee_id" value="{{ old('employee_id') }}">

                            <div class="field">
                                <label for="employee_search">Search Employee</label>
                                <input type="text" id="employee_search" placeholder="Search by employee ID or name">
                            </div>

                            <div class="field">
                                <label>Select Employee</label>
                                <div id="employee_list" class="employee-list">
                                    @forelse ($employees as $employee)
                                        @php
                                            $searchText = strtolower(implode(' ', array_filter([
                                                $employee->nik,
                                                $employee->name,
                                            ])));
                                        @endphp
                                        <button
                                            type="button"
                                            class="employee-item"
                                            data-employee-id="{{ $employee->id }}"
                                            data-employee-name="{{ $employee->name }}"
                                            data-employee-nik="{{ $employee->nik }}"
                                            data-search="{{ $searchText }}"
                                        >
                                            <span class="employee-name">{{ $employee->name }}</span>
                                            <span class="employee-id">{{ $employee->nik }}</span>
                                            <span class="employee-meta">
                                                <span>{{ $employee->department ?: 'Department not set' }}</span>
                                                <span>{{ $employee->division ?: 'Division not set' }}</span>
                                                <span>{{ $employee->position_title ?: 'Position not set' }}</span>
                                            </span>
                                        </button>
                                    @empty
                                        <div class="empty">No active employee is available for this training.</div>
                                    @endforelse
                                </div>
                                <div class="selected-panel" id="selected_employee_panel">
                                    <strong>No employee selected</strong>
                                    Choose one employee from the list above.
                                </div>
                            </div>

                            @if ($mode === 'registration')
                                <div class="compact-row contact-row">
                                    <div class="field">
                                        <label for="whatsapp_number">WhatsApp Number</label>
                                        <input
                                            type="text"
                                            id="whatsapp_number"
                                            name="whatsapp_number"
                                            value="{{ old('whatsapp_number') }}"
                                            placeholder="Enter WhatsApp number"
                                            required
                                        >
                                    </div>
                                    <div class="field">
                                        <label for="email">Email Address</label>
                                        <input
                                            type="email"
                                            id="email"
                                            name="email"
                                            value="{{ old('email') }}"
                                            placeholder="Enter email address"
                                        >
                                    </div>
                                </div>
                            @endif

                            <div class="submit-bar">
                                <button type="submit" id="submit_button" @disabled(! old('employee_id') && $employees->isNotEmpty())>
                                    {{ $mode === 'attendance' ? 'Save Attendance' : 'Save Registration' }}
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- Tab: External --}}
                    <div class="tab-panel" id="tab-external">
                        <form method="POST" action="{{ $mode === 'attendance' ? route('trainings.attendance.submit', $training->attendance_token) : route('trainings.registration.submit', $training->registration_token) }}">
                            @csrf
                            <input type="hidden" name="register_mode" value="external">

                            <div class="external-notice">
                                Peserta eksternal tidak terdaftar di sistem karyawan. Data akan disimpan sebagai peserta baru.
                            </div>

                            <div class="compact-row">
                                <div class="field">
                                    <label for="ext_name">Nama Lengkap *</label>
                                    <input
                                        type="text"
                                        id="ext_name"
                                        name="ext_name"
                                        value="{{ old('ext_name') }}"
                                        placeholder="Masukkan nama lengkap"
                                        required
                                    >
                                </div>
                                <div class="field">
                                    <label for="ext_gender">Jenis Kelamin</label>
                                    <select id="ext_gender" name="ext_gender">
                                        <option value="">Pilih jenis kelamin</option>
                                        <option value="Male" @selected(old('ext_gender') === 'Male')>Laki-laki</option>
                                        <option value="Female" @selected(old('ext_gender') === 'Female')>Perempuan</option>
                                    </select>
                                </div>
                            </div>

                            <div class="compact-row" style="margin-top:10px">
                                <div class="field">
                                    <label for="ext_company">Perusahaan / Instansi</label>
                                    <input
                                        type="text"
                                        id="ext_company"
                                        name="ext_company"
                                        value="{{ old('ext_company') }}"
                                        placeholder="Nama perusahaan"
                                    >
                                </div>
                                <div class="field">
                                    <label for="ext_position">Jabatan</label>
                                    <input
                                        type="text"
                                        id="ext_position"
                                        name="ext_position"
                                        value="{{ old('ext_position') }}"
                                        placeholder="Jabatan / posisi"
                                    >
                                </div>
                            </div>

                            <div class="compact-row contact-row">
                                <div class="field">
                                    <label for="ext_whatsapp">WhatsApp Number *</label>
                                    <input
                                        type="text"
                                        id="ext_whatsapp"
                                        name="whatsapp_number"
                                        value="{{ old('whatsapp_number') }}"
                                        placeholder="Nomor WhatsApp"
                                        required
                                    >
                                </div>
                                <div class="field">
                                    <label for="ext_email">Email Address</label>
                                    <input
                                        type="email"
                                        id="ext_email"
                                        name="email"
                                        value="{{ old('email') }}"
                                        placeholder="Alamat email"
                                    >
                                </div>
                            </div>

                            <div class="submit-bar">
                                <button type="submit">
                                    {{ $mode === 'attendance' ? 'Simpan Kehadiran Eksternal' : 'Daftarkan Peserta Eksternal' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        (() => {
            // Tab switching
            const tabBtns = document.querySelectorAll('.tab-btn');
            const tabPanels = document.querySelectorAll('.tab-panel');

            tabBtns.forEach((btn) => {
                btn.addEventListener('click', () => {
                    const target = btn.dataset.tab;
                    tabBtns.forEach((b) => b.classList.remove('active'));
                    tabPanels.forEach((p) => p.classList.remove('active'));
                    btn.classList.add('active');
                    const panel = document.getElementById('tab-' + target);
                    if (panel) panel.classList.add('active');
                });
            });

            // Internal employee list logic
            const searchInput = document.getElementById('employee_search');
            const employeeIdInput = document.getElementById('employee_id');
            const selectedPanel = document.getElementById('selected_employee_panel');
            const submitButton = document.getElementById('submit_button');
            const list = document.getElementById('employee_list');
            const items = Array.from(document.querySelectorAll('.employee-item'));
            const initialId = employeeIdInput?.value || '';

            const normalize = (value) => value
                .toLowerCase()
                .normalize('NFD')
                .replace(/[̀-ͯ]/g, '')
                .trim();

            const setSelected = (button) => {
                items.forEach((item) => item.classList.remove('active'));

                if (!button) {
                    if (employeeIdInput) employeeIdInput.value = '';
                    if (selectedPanel) selectedPanel.innerHTML = '<strong>No employee selected</strong>Choose one employee from the list above.';
                    if (submitButton) submitButton.disabled = items.length > 0;
                    return;
                }

                button.classList.add('active');
                if (employeeIdInput) employeeIdInput.value = button.dataset.employeeId || '';
                if (selectedPanel) selectedPanel.innerHTML = `<strong>${button.dataset.employeeName || ''}</strong>${button.dataset.employeeNik || ''}`;
                if (submitButton) submitButton.disabled = false;
            };

            const filterItems = () => {
                const keyword = normalize(searchInput?.value || '');
                let visibleCount = 0;

                items.forEach((item) => {
                    const visible = keyword === '' || normalize(item.dataset.search || '').includes(keyword);
                    item.hidden = !visible;
                    if (visible) visibleCount += 1;
                });

                const existingEmpty = document.getElementById('employee-empty-search');

                if (visibleCount === 0 && !existingEmpty && items.length > 0) {
                    const empty = document.createElement('div');
                    empty.className = 'empty';
                    empty.id = 'employee-empty-search';
                    empty.textContent = 'No employee matches your search.';
                    list?.appendChild(empty);
                }

                if (visibleCount > 0 && existingEmpty) existingEmpty.remove();
            };

            items.forEach((item) => item.addEventListener('click', () => setSelected(item)));

            if (initialId) {
                const initialButton = items.find((item) => item.dataset.employeeId === initialId);
                if (initialButton) setSelected(initialButton);
            } else if (submitButton && items.length > 0) {
                submitButton.disabled = true;
            }

            searchInput?.addEventListener('input', filterItems);
            filterItems();

            // If old input was external, switch to external tab
            @if (old('register_mode') === 'external')
                const extBtn = document.querySelector('[data-tab="external"]');
                if (extBtn) extBtn.click();
            @endif
        })();
    </script>
</body>
</html>
