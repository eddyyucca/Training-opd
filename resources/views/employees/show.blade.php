@extends('layouts.app', [
    'title' => 'Employee Detail',
    'header' => 'Employee Detail',
    'subtitle' => 'Profile, performance, and training history'
])

@section('page_actions')
    <div class="toolbar-actions w-full lg:w-auto">
        <a href="{{ route('employees.index') }}" class="btn btn-light">Back to Employees</a>
        <a href="{{ route('employees.edit', $employee) }}" class="btn btn-primary">Edit Employee</a>
    </div>
@endsection

@section('content')
    <div class="glass-panel mt-6">
        <div class="card-body p-5">
            <div class="grid gap-5 lg:grid-cols-[minmax(0,1.45fr)_minmax(280px,0.75fr)] lg:items-center">
                <div class="space-y-4">
                    <span class="info-chip">Employee Profile</span>
                    <div>
                        <h2 class="text-3xl font-extrabold tracking-tight text-white sm:text-4xl">{{ $employee->name }}</h2>
                        <p class="mt-2 text-sm text-white/76 sm:text-base">
                            {{ collect([$employee->position_title, $employee->department, $employee->division])->filter()->implode(' • ') ?: 'Position and organization details are not set yet.' }}
                        </p>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <span class="info-chip"><i class="fas fa-id-badge"></i> {{ $employee->nik }}</span>
                        <span class="info-chip"><i class="fas fa-building"></i> {{ $employee->company ?: 'Company not set' }}</span>
                        <span class="info-chip"><i class="fas fa-layer-group"></i> {{ $employee->job_level_group }}</span>
                    </div>
                </div>

                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-1">
                    <div class="detail-item bg-white/12 border-white/10">
                        <span class="detail-item-label text-white/65">Status</span>
                        <div class="detail-item-value text-white">{{ $employee->is_active ? 'Active' : 'Inactive' }}</div>
                    </div>
                    <div class="detail-item bg-white/12 border-white/10">
                        <span class="detail-item-label text-white/65">Gender</span>
                        <div class="detail-item-value text-white">{{ $employee->gender ?: '-' }}</div>
                    </div>
                    <div class="detail-item bg-white/12 border-white/10">
                        <span class="detail-item-label text-white/65">Last Training</span>
                        <div class="detail-item-value text-white">{{ $latestTrainingDate?->format('d M Y') ?: '-' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="section-stat stat-blue">
            <h3>{{ number_format($totalTraining, 0, ',', '.') }}</h3>
            <p>Total trainings attended</p>
            <div class="stat-icon"><i class="fas fa-book-reader"></i></div>
        </div>
        <div class="section-stat stat-green">
            <h3>{{ number_format($totalHours, 0, ',', '.') }}</h3>
            <p>Total training hours</p>
            <div class="stat-icon"><i class="fas fa-clock"></i></div>
        </div>
        <div class="section-stat stat-amber">
            <h3>{{ number_format($classificationSummary->count(), 0, ',', '.') }}</h3>
            <p>Top classifications</p>
            <div class="stat-icon"><i class="fas fa-layer-group"></i></div>
        </div>
        <div class="section-stat stat-rose">
            <h3>{{ number_format($yearSummary->count(), 0, ',', '.') }}</h3>
            <p>Active years in training</p>
            <div class="stat-icon"><i class="fas fa-chart-line"></i></div>
        </div>
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-[minmax(0,1.1fr)_minmax(300px,0.9fr)]">
        <div class="card">
            <div class="card-header">
                <div class="content-card-label">Performance Trend</div>
                <h3 class="card-title">Training hours over time</h3>
            </div>
            <div class="card-body">
                <div class="chart-panel h-[300px] sm:h-[360px]">
                    <canvas id="employee-performance-chart"></canvas>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <div class="content-card-label">Profile Summary</div>
                <h3 class="card-title">Employee information</h3>
            </div>
            <div class="card-body">
                <div class="metric-inline">
                    <div>
                        <strong>Department</strong>
                        <small>Current placement</small>
                    </div>
                    <div class="text-right">
                        <strong>{{ $employee->department ?: '-' }}</strong>
                    </div>
                </div>
                <div class="metric-inline">
                    <div>
                        <strong>Division</strong>
                        <small>Operational unit</small>
                    </div>
                    <div class="text-right">
                        <strong>{{ $employee->division ?: '-' }}</strong>
                    </div>
                </div>
                <div class="metric-inline">
                    <div>
                        <strong>Position</strong>
                        <small>Current role</small>
                    </div>
                    <div class="text-right">
                        <strong>{{ $employee->position_title ?: '-' }}</strong>
                    </div>
                </div>
                <div class="metric-inline">
                    <div>
                        <strong>Company</strong>
                        <small>Employer entity</small>
                    </div>
                    <div class="text-right">
                        <strong>{{ $employee->company ?: '-' }}</strong>
                    </div>
                </div>
                <div class="metric-inline">
                    <div>
                        <strong>WhatsApp</strong>
                        <small>Direct contact</small>
                    </div>
                    <div class="text-right">
                        <strong>{{ $employee->whatsapp_number ?: '-' }}</strong>
                    </div>
                </div>
                <div class="metric-inline">
                    <div>
                        <strong>Email</strong>
                        <small>Work email</small>
                    </div>
                    <div class="text-right">
                        <strong>{{ $employee->email ?: '-' }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-2">
        <div class="card">
            <div class="card-header">
                <div class="content-card-label">By Year</div>
                <h3 class="card-title">Training summary by year</h3>
            </div>
            <div class="card-body">
                @forelse ($yearSummary as $item)
                    <div class="metric-inline">
                        <div>
                            <strong>{{ $item['year'] }}</strong>
                            <small>{{ number_format($item['hours'], 1, '.', ',') }} hours</small>
                        </div>
                        <div class="text-right">
                            <strong>{{ number_format($item['total'], 0, ',', '.') }}</strong>
                            <small>trainings</small>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">No training history is available yet.</div>
                @endforelse
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <div class="content-card-label">Classification</div>
                <h3 class="card-title">Top classifications</h3>
            </div>
            <div class="card-body">
                @forelse ($classificationSummary as $item)
                    <div class="metric-inline">
                        <div>
                            <strong>{{ $item['label'] }}</strong>
                            <small>Training focus</small>
                        </div>
                        <div class="text-right">
                            <strong>{{ number_format($item['total'], 0, ',', '.') }}</strong>
                            <small>trainings</small>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">No classification data is available yet.</div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="card mt-6">
        <div class="card-header">
            <div class="content-card-header">
                <div>
                    <div class="content-card-label">Training History</div>
                    <h3 class="card-title">Completed training records</h3>
                </div>
                <a href="{{ route('trainings.index') }}" class="btn btn-light btn-sm">Open Training List</a>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                    <tr>
                        <th>Year</th>
                        <th>Training</th>
                        <th>Classification</th>
                        <th>Date</th>
                        <th>Hours</th>
                        <th>Days</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($trainingHistory as $training)
                        <tr>
                            <td>{{ $training->year ?: '-' }}</td>
                            <td>
                                <div class="table-stack">
                                    <span class="table-main">{{ $training->name }}</span>
                                    <span class="table-sub">{{ $training->provider ?: 'Provider not set' }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="table-stack">
                                    <span class="table-main">{{ $training->training_classification ?: '-' }}</span>
                                    <span class="table-sub">{{ $training->training_sub_classification ?: 'Sub-classification not set' }}</span>
                                </div>
                            </td>
                            <td>
                                {{ $training->start_date?->format('d M Y') ?: '-' }}
                                @if ($training->end_date && $training->end_date->ne($training->start_date))
                                    <br><small class="text-muted">to {{ $training->end_date->format('d M Y') }}</small>
                                @endif
                            </td>
                            <td>{{ number_format((float) $training->hours, 1, '.', ',') }}</td>
                            <td>{{ $training->days ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="empty-state">No training history is available for this employee.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (() => {
            const canvas = document.getElementById('employee-performance-chart');

            if (!canvas || typeof Chart === 'undefined') {
                return;
            }

            const existing = Chart.getChart(canvas);
            if (existing) {
                existing.destroy();
            }

            const context = canvas.getContext('2d');
            const gradient = context.createLinearGradient(0, 0, 0, 340);
            gradient.addColorStop(0, 'rgba(37, 99, 235, 0.35)');
            gradient.addColorStop(1, 'rgba(147, 197, 253, 0.04)');

            new Chart(canvas, {
                type: 'line',
                data: {
                    labels: @json($monthlyPerformance->pluck('label')),
                    datasets: [{
                        label: 'Hours',
                        data: @json($monthlyPerformance->pluck('hours')),
                        borderColor: '#1d4ed8',
                        backgroundColor: gradient,
                        fill: true,
                        tension: 0.35,
                        pointRadius: 5,
                        pointHoverRadius: 6,
                        pointBackgroundColor: '#1d4ed8',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#0f172a',
                            titleColor: '#ffffff',
                            bodyColor: '#ffffff',
                            callbacks: {
                                label(context) {
                                    return `${context.raw} hours`;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { color: '#475569' }
                        },
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(148, 163, 184, 0.18)' },
                            ticks: { color: '#475569' }
                        }
                    }
                }
            });
        })();
    </script>
@endpush
