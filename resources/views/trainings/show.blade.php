@extends('layouts.app', [
    'title' => 'Training Detail',
    'header' => 'Training Detail',
    'subtitle' => 'Schedule, access links, and participant status'
])

@section('page_actions')
    <div class="toolbar-actions w-full lg:w-auto">
        <a href="{{ route('trainings.index') }}" class="btn btn-light">Back to Training</a>
        <a href="{{ route('trainings.edit', $training) }}" class="btn btn-primary">Edit Training</a>
    </div>
@endsection

@section('content')
    <div class="glass-panel mt-6">
        <div class="card-body p-5">
            <div class="grid gap-5 lg:grid-cols-[minmax(0,1.45fr)_minmax(280px,0.75fr)] lg:items-center">
                <div class="space-y-4">
                    <span class="info-chip">Training Overview</span>
                    <div>
                        <h2 class="text-3xl font-extrabold tracking-tight text-white sm:text-4xl">{{ $training->name }}</h2>
                        <p class="mt-2 text-sm text-white/76 sm:text-base">
                            {{ collect([$training->provider ?: 'Provider not set', $training->training_classification, $training->training_sub_classification])->filter()->implode(' • ') }}
                        </p>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <span class="info-chip"><i class="fas fa-calendar-alt"></i> {{ $training->start_date?->format('d M Y') ?: 'Date not set' }}</span>
                        <span class="info-chip"><i class="fas fa-building"></i> {{ $training->department?->name ?: 'Department not set' }}</span>
                    </div>
                </div>

                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-1">
                    <div class="detail-item bg-white/12 border-white/10">
                        <span class="detail-item-label text-white/65">Hours</span>
                        <div class="detail-item-value text-white">{{ number_format((float) $training->hours, 2, '.', ',') }}</div>
                    </div>
                    <div class="detail-item bg-white/12 border-white/10">
                        <span class="detail-item-label text-white/65">Days</span>
                        <div class="detail-item-value text-white">{{ $training->days ?: 0 }}</div>
                    </div>
                    <div class="detail-item bg-white/12 border-white/10">
                        <span class="detail-item-label text-white/65">Time</span>
                        <div class="detail-item-value text-white">{{ $training->start_time?->format('H:i') ?: '-' }} - {{ $training->end_time?->format('H:i') ?: '-' }}</div>
                    </div>
                    <div class="detail-item bg-white/12 border-white/10">
                        <span class="detail-item-label text-white/65">Quota</span>
                        <div class="detail-item-value text-white">
                            {{ $training->quota ? number_format($training->quota, 0, ',', '.') : 'Unlimited' }}
                        </div>
                    </div>
                    <div class="detail-item bg-white/12 border-white/10">
                        <span class="detail-item-label text-white/65">Cost / Person</span>
                        <div class="detail-item-value text-white">
                            {{ $training->cost_per_person ? 'Rp ' . number_format((float) $training->cost_per_person, 0, ',', '.') : '-' }}
                        </div>
                    </div>
                    <div class="detail-item bg-white/12 border-white/10">
                        <span class="detail-item-label text-white/65">PR Number</span>
                        <div class="detail-item-value text-white">{{ $training->pr_number ?: '-' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if ($training->cost_per_person)
    <div class="mt-6 grid gap-4 sm:grid-cols-3">
        <div class="card p-5">
            <div class="content-card-label">Cost per Person</div>
            <div class="mt-1 text-2xl font-extrabold text-slate-900">Rp {{ number_format((float) $training->cost_per_person, 0, ',', '.') }}</div>
        </div>
        <div class="card p-5">
            <div class="content-card-label">Total Cost (Attended)</div>
            <div class="mt-1 text-2xl font-extrabold text-slate-900">
                Rp {{ number_format((float) $training->cost_per_person * $training->attendedEmployees->count(), 0, ',', '.') }}
            </div>
            <p class="mt-1 text-xs text-slate-500">{{ $training->attendedEmployees->count() }} attended × cost per person</p>
        </div>
        <div class="card p-5">
            <div class="content-card-label">Total Cost (Registered)</div>
            <div class="mt-1 text-2xl font-extrabold text-slate-900">
                Rp {{ number_format((float) $training->cost_per_person * $training->registeredEmployees->count(), 0, ',', '.') }}
            </div>
            <p class="mt-1 text-xs text-slate-500">{{ $training->registeredEmployees->count() }} registered × cost per person</p>
        </div>
    </div>
    @endif

    <div class="mt-6 grid gap-6 xl:grid-cols-2">
        <div class="card">
            <div class="card-header">
                <div class="content-card-label">Public Access</div>
                <h3 class="card-title">Registration access</h3>
            </div>
            <div class="card-body">
                <p class="content-card-subtitle mb-4">Open a dedicated QR page for participant registration.</p>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('trainings.registration.qr', $training) }}" class="btn btn-primary">Open Registration QR</a>
                    @if ($training->registrationIsOpen())
                        <a href="{{ route('trainings.registration.show', $training->registration_token) }}" class="btn btn-light" target="_blank" rel="noopener">Open Public Link</a>
                    @endif
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <div class="content-card-label">Public Access</div>
                <h3 class="card-title">Attendance access</h3>
            </div>
            <div class="card-body">
                <p class="content-card-subtitle mb-4">Open a dedicated QR page for training attendance.</p>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('trainings.attendance.qr', $training) }}" class="btn btn-primary">Open Attendance QR</a>
                    @if ($training->attendanceIsOpen())
                        <a href="{{ route('trainings.attendance.show', $training->attendance_token) }}" class="btn btn-light" target="_blank" rel="noopener">Open Public Link</a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-2">
        <div class="card">
            <button type="button" class="card-header w-full text-left js-toggle-panel" data-target="registered-participants-panel" aria-expanded="false">
                <div class="content-card-header">
                    <h3 class="card-title">Registered Participants</h3>
                    <span class="table-count"><i class="fas fa-users"></i> {{ number_format($training->registeredEmployees->count(), 0, ',', '.') }}</span>
                </div>
            </button>
            <div class="card-body hidden" id="registered-participants-panel">
                @forelse ($training->registeredEmployees as $employee)
                    <div class="metric-inline">
                        <div>
                            <strong>{{ $employee->name }}</strong>
                            <small>{{ $employee->nik }}</small>
                        </div>
                        <span class="badge badge-light">{{ optional($employee->pivot->registered_at)->format('d M Y H:i') ?: '-' }}</span>
                    </div>
                @empty
                    <div class="empty-state">No registrations yet.</div>
                @endforelse
            </div>
        </div>
        <div class="card">
            <button type="button" class="card-header w-full text-left js-toggle-panel" data-target="attended-participants-panel" aria-expanded="false">
                <div class="content-card-header">
                    <h3 class="card-title">Attended Participants</h3>
                    <span class="table-count"><i class="fas fa-user-check"></i> {{ number_format($training->attendedEmployees->count(), 0, ',', '.') }}</span>
                </div>
            </button>
            <div class="card-body hidden" id="attended-participants-panel">
                @forelse ($training->attendedEmployees as $employee)
                    <div class="metric-inline">
                        <div>
                            <strong>{{ $employee->name }}</strong>
                            <small>{{ $employee->nik }}</small>
                        </div>
                        <span class="badge badge-success">{{ optional($employee->pivot->attended_at)->format('d M Y H:i') ?: '-' }}</span>
                    </div>
                @empty
                    <div class="empty-state">No attendance records yet.</div>
                @endforelse
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (() => {
            document.querySelectorAll('.js-toggle-panel').forEach((button) => {
                button.addEventListener('click', () => {
                    const targetId = button.dataset.target;
                    const panel = targetId ? document.getElementById(targetId) : null;

                    if (!panel) {
                        return;
                    }

                    const isHidden = panel.classList.contains('hidden');
                    panel.classList.toggle('hidden');
                    button.setAttribute('aria-expanded', String(isHidden));
                });
            });
        })();
    </script>
@endpush
