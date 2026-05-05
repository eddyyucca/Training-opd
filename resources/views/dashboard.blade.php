@extends('layouts.app', [
    'title' => 'Dashboard',
    'header' => 'Training Dashboard',
    'subtitle' => 'Organization and People Development'
])

@section('page_actions')
    <form method="GET" class="dashboard-filter-bar" id="dashboard-filter-form">
        <div class="dashboard-filter-card">
            <label for="dashboard-year" class="dashboard-filter-label">Year</label>
            <select name="year" id="dashboard-year" class="form-control dashboard-filter no-enhance">
                @foreach ($availableYears as $year)
                    <option value="{{ $year }}" @selected((int) $selectedYear === (int) $year)>{{ $year }}</option>
                @endforeach
            </select>
        </div>
        <div class="dashboard-filter-card">
            <label for="dashboard-month" class="dashboard-filter-label">Month</label>
            <select name="month" id="dashboard-month" class="form-control dashboard-filter no-enhance">
                <option value="">All Months</option>
                @foreach ($availableMonths as $month)
                    <option value="{{ $month['value'] }}" @selected((int) $selectedMonth === (int) $month['value'])>{{ $month['label'] }}</option>
                @endforeach
            </select>
        </div>
        <div class="dashboard-filter-card">
            <label for="dashboard-week" class="dashboard-filter-label">Week</label>
            <select name="week" id="dashboard-week" class="form-control dashboard-filter no-enhance">
                <option value="">All Weeks</option>
                @foreach ($availableWeeks as $week)
                    <option value="{{ $week['value'] }}" @selected((int) $selectedWeek === (int) $week['value'])>{{ $week['label'] }}</option>
                @endforeach
            </select>
        </div>
    </form>
@endsection

@section('content')
    <div class="glass-panel mt-6">
        <div class="card-body p-5">
            <div class="grid gap-5 lg:grid-cols-[minmax(0,1.5fr)_minmax(300px,0.9fr)] lg:items-center">
                <div class="space-y-4">
                    <span class="info-chip">Organization and People Development</span>
                    <div>
                        <h2 class="text-3xl font-extrabold tracking-tight text-white sm:text-4xl">Training insight across the selected period</h2>
                        <p class="mt-2 max-w-2xl text-sm text-white/72 sm:text-base">Track yearly, monthly, and weekly performance in one responsive view.</p>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <span class="info-chip"><i class="fas fa-calendar-alt"></i> {{ $periodLabel }}</span>
                        <span class="info-chip"><i class="fas fa-users"></i> {{ number_format($employeeMasterCount, 0, ',', '.') }} employees</span>
                        <span class="info-chip"><i class="fas fa-user-check"></i> {{ number_format($uniqueEmployees, 0, ',', '.') }} active trainees</span>
                    </div>
                </div>

                <div class="grid gap-3 sm:grid-cols-3 lg:grid-cols-1">
                    <div class="detail-item bg-white/12 border-white/10">
                        <span class="detail-item-label text-white/65">Attendance Based Hours</span>
                        <div class="detail-item-value text-white">{{ number_format($totalHours, 0, ',', '.') }} hours</div>
                    </div>
                    <div class="detail-item bg-white/12 border-white/10">
                        <span class="detail-item-label text-white/65">Average Hours per Employee</span>
                        <div class="detail-item-value text-white">{{ number_format($averageHours, 1, ',', '.') }} hours</div>
                    </div>
                    <div class="detail-item bg-white/12 border-white/10">
                        <span class="detail-item-label text-white/65">Attendance Records</span>
                        <div class="detail-item-value text-white">{{ number_format($totalParticipants, 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="section-stat stat-blue">
            <h3>{{ number_format($totalTrainings, 0, ',', '.') }}</h3>
            <p>Total trainings</p>
            <div class="stat-icon"><i class="fas fa-book-reader"></i></div>
        </div>
        <div class="section-stat stat-green">
            <h3>{{ number_format($totalHours, 0, ',', '.') }}</h3>
            <p>Total attended hours</p>
            <div class="stat-icon"><i class="fas fa-clock"></i></div>
        </div>
        <div class="section-stat stat-amber">
            <h3>{{ number_format($runningTrainings, 0, ',', '.') }}</h3>
            <p>Running trainings</p>
            <div class="stat-icon"><i class="fas fa-play-circle"></i></div>
        </div>
        <div class="section-stat stat-rose">
            <h3>{{ number_format($upcomingTrainings, 0, ',', '.') }}</h3>
            <p>Upcoming trainings</p>
            <div class="stat-icon"><i class="fas fa-calendar-plus"></i></div>
        </div>
    </div>

    {{-- Internal vs External Summary --}}
    <div class="mt-6 grid gap-6 sm:grid-cols-2">
        <div class="card">
            <div class="card-header">
                <div class="content-card-label">Internal</div>
                <h3 class="card-title">Karyawan Internal</h3>
                <p class="content-card-subtitle">Peserta dari daftar karyawan</p>
            </div>
            <div class="card-body space-y-3">
                <div class="detail-item">
                    <span class="detail-item-label">Attendance Records</span>
                    <div class="detail-item-value">{{ number_format($internalStats['participants'], 0, ',', '.') }}</div>
                </div>
                <div class="detail-item">
                    <span class="detail-item-label">Unique Employees</span>
                    <div class="detail-item-value">{{ number_format($internalStats['unique_employees'], 0, ',', '.') }}</div>
                </div>
                <div class="detail-item">
                    <span class="detail-item-label">Total Hours</span>
                    <div class="detail-item-value">{{ number_format($internalStats['total_hours'], 0, ',', '.') }} jam</div>
                </div>
                <div class="detail-item">
                    <span class="detail-item-label">Avg Hours / Employee</span>
                    <div class="detail-item-value">
                        {{ $internalStats['unique_employees'] > 0 ? number_format($internalStats['total_hours'] / $internalStats['unique_employees'], 1, ',', '.') : '0' }} jam
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <div class="content-card-label">Eksternal</div>
                <h3 class="card-title">Peserta Eksternal</h3>
                <p class="content-card-subtitle">Peserta bukan karyawan</p>
            </div>
            <div class="card-body space-y-3">
                <div class="detail-item">
                    <span class="detail-item-label">Attendance Records</span>
                    <div class="detail-item-value">{{ number_format($externalStats['participants'], 0, ',', '.') }}</div>
                </div>
                <div class="detail-item">
                    <span class="detail-item-label">Unique Participants</span>
                    <div class="detail-item-value">{{ number_format($externalStats['unique_employees'], 0, ',', '.') }}</div>
                </div>
                <div class="detail-item">
                    <span class="detail-item-label">Total Hours</span>
                    <div class="detail-item-value">{{ number_format($externalStats['total_hours'], 0, ',', '.') }} jam</div>
                </div>
                <div class="detail-item">
                    <span class="detail-item-label">Avg Hours / Participant</span>
                    <div class="detail-item-value">
                        {{ $externalStats['unique_employees'] > 0 ? number_format($externalStats['total_hours'] / $externalStats['unique_employees'], 1, ',', '.') : '0' }} jam
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-[minmax(0,1.4fr)_minmax(320px,0.8fr)]">
        <div class="card">
            <div class="card-header">
                <div class="content-card-header">
                    <div>
                        <div class="content-card-label">Trend</div>
                        <h3 class="card-title">{{ $trendSeries['title'] }}</h3>
                        <p class="content-card-subtitle">{{ $periodLabel }}</p>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-panel h-[300px] sm:h-[360px]">
                    <canvas id="trend-chart"></canvas>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <div class="content-card-label">Live Status</div>
                <h3 class="card-title">Running trainings right now</h3>
            </div>
            <div class="card-body space-y-4">
                @forelse ($runningTrainingList as $training)
                    <article class="rounded-[22px] border border-slate-100 bg-slate-50/80 p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <h4 class="truncate text-sm font-extrabold text-slate-900">{{ $training->name }}</h4>
                                <p class="mt-1 text-xs text-slate-500">{{ $training->department?->name ?: 'All Departments' }}</p>
                            </div>
                            <span class="badge badge-light whitespace-nowrap">{{ number_format($training->hours, 1, ',', '.') }} hrs</span>
                        </div>
                        <div class="mt-4 grid gap-3 sm:grid-cols-3">
                            <div class="detail-item">
                                <span class="detail-item-label">Schedule</span>
                                <div class="detail-item-value text-sm">{{ $training->start_date?->format('d M Y') ?: '-' }}</div>
                                <div class="text-xs text-slate-500">{{ $training->start_time?->format('H:i') ?: '-' }} - {{ $training->end_time?->format('H:i') ?: '-' }}</div>
                            </div>
                            <div class="detail-item">
                                <span class="detail-item-label">Registered</span>
                                <div class="detail-item-value">{{ number_format($training->registered_employees_count, 0, ',', '.') }}</div>
                            </div>
                            <div class="detail-item">
                                <span class="detail-item-label">Attended</span>
                                <div class="detail-item-value">{{ number_format($training->attended_employees_count, 0, ',', '.') }}</div>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="empty-state">No training is currently running for this period.</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Cost Summary --}}
    <div class="mt-6 grid gap-6 xl:grid-cols-[minmax(0,0.9fr)_minmax(0,1.1fr)]">
        <div class="card">
            <div class="card-header">
                <div class="content-card-label">Budget</div>
                <h3 class="card-title">Training cost summary</h3>
                <p class="content-card-subtitle">{{ $periodLabel }}</p>
            </div>
            <div class="card-body space-y-3">
                <div class="detail-item">
                    <span class="detail-item-label">Total Cost (attended)</span>
                    <div class="detail-item-value text-lg font-extrabold text-slate-900">
                        Rp {{ number_format($totalCost, 0, ',', '.') }}
                    </div>
                </div>
                <div class="detail-item">
                    <span class="detail-item-label">Attendance Records</span>
                    <div class="detail-item-value">{{ number_format($totalParticipants, 0, ',', '.') }} orang</div>
                </div>
                @if ($totalParticipants > 0 && $totalCost > 0)
                <div class="detail-item">
                    <span class="detail-item-label">Avg Cost / Attendance</span>
                    <div class="detail-item-value">Rp {{ number_format($totalCost / $totalParticipants, 0, ',', '.') }}</div>
                </div>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <div class="content-card-label">Budget</div>
                <h3 class="card-title">Cost per training</h3>
            </div>
            <div class="card-body">
                @if ($trainingCostSummary->isNotEmpty())
                    <div class="chart-panel" style="height: {{ min(60 + $trainingCostSummary->count() * 42, 340) }}px">
                        <canvas id="cost-chart"></canvas>
                    </div>
                @else
                    <div class="empty-state">Belum ada data cost training.</div>
                @endif
            </div>
        </div>
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-2 xl:grid-cols-4">
        <div class="card">
            <div class="card-header">
                <div class="content-card-label">Status</div>
                <h3 class="card-title">Training lifecycle</h3>
            </div>
            <div class="card-body">
                <div class="chart-panel h-[260px]">
                    <canvas id="status-chart"></canvas>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <div class="content-card-label">People</div>
                <h3 class="card-title">Gender distribution</h3>
            </div>
            <div class="card-body">
                <div class="chart-panel h-[260px]">
                    <canvas id="gender-chart"></canvas>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <div class="content-card-label">People</div>
                <h3 class="card-title">Job level group</h3>
            </div>
            <div class="card-body">
                <div class="chart-panel h-[260px]">
                    <canvas id="position-chart"></canvas>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <div class="content-card-label">Training</div>
                <h3 class="card-title">Classification mix</h3>
            </div>
            <div class="card-body">
                <div class="chart-panel h-[260px]">
                    <canvas id="classification-chart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-2">
        <div class="card">
            <div class="card-header">
                <div class="content-card-label">Department</div>
                <h3 class="card-title">Hours by department</h3>
            </div>
            <div class="card-body">
                <div class="chart-panel h-[320px] sm:h-[360px]">
                    <canvas id="department-hours-chart"></canvas>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <div class="content-card-label">Provider</div>
                <h3 class="card-title">Top providers</h3>
            </div>
            <div class="card-body">
                <div class="chart-panel h-[320px] sm:h-[360px]">
                    <canvas id="provider-chart"></canvas>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (() => {
            const filterForm = document.getElementById('dashboard-filter-form');

            document.querySelectorAll('.dashboard-filter').forEach((field) => {
                field.addEventListener('change', () => {
                    filterForm?.submit();
                });
            });

            const canvases = {
                trend: document.getElementById('trend-chart'),
                status: document.getElementById('status-chart'),
                gender: document.getElementById('gender-chart'),
                position: document.getElementById('position-chart'),
                classification: document.getElementById('classification-chart'),
                department: document.getElementById('department-hours-chart'),
                provider: document.getElementById('provider-chart'),
                cost: document.getElementById('cost-chart'),
            };

            const requiredCanvases = ['trend', 'status', 'gender', 'position', 'classification', 'department', 'provider'];
            if (requiredCanvases.some((key) => !canvases[key]) || typeof Chart === 'undefined') {
                return;
            }

            const labelPlugin = {
                id: 'dashboardValueLabels',
                afterDatasetsDraw(chart) {
                    const { ctx } = chart;
                    const dataset = chart.data.datasets[0];

                    if (!dataset) {
                        return;
                    }

                    ctx.save();
                    ctx.font = '600 11px Manrope, sans-serif';
                    ctx.fillStyle = '#0f172a';
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';

                    chart.getDatasetMeta(0).data.forEach((element, index) => {
                        const rawValue = dataset.data[index];
                        const value = Number(rawValue);

                        if (!value) {
                            return;
                        }

                        const formatted = Number.isInteger(value) ? value.toLocaleString('en-US') : value.toLocaleString('en-US', {
                            minimumFractionDigits: 1,
                            maximumFractionDigits: 1,
                        });

                        const position = element.tooltipPosition();
                        let x = position.x;
                        let y = position.y - 14;

                        if (chart.config.type === 'doughnut' || chart.config.type === 'pie') {
                            y = position.y;
                        }

                        if (chart.config.type === 'bar' && chart.options.indexAxis === 'y') {
                            x = position.x + 24;
                            y = position.y;
                            ctx.textAlign = 'left';
                        }

                        ctx.fillStyle = 'rgba(255, 255, 255, 0.92)';
                        const width = ctx.measureText(formatted).width + 12;
                        const boxX = x - (chart.options.indexAxis === 'y' ? 6 : width / 2);
                        const boxY = y - 10;
                        ctx.beginPath();
                        if (typeof ctx.roundRect === 'function') {
                            ctx.roundRect(boxX, boxY, width, 20, 10);
                        } else {
                            ctx.rect(boxX, boxY, width, 20);
                        }
                        ctx.fill();

                        ctx.fillStyle = '#0f172a';
                        ctx.fillText(formatted, chart.options.indexAxis === 'y' ? x : x, y);
                        ctx.textAlign = 'center';
                    });

                    ctx.restore();
                }
            };

            Chart.register(labelPlugin);

            const chartTextColor = '#334155';
            const chartGridColor = 'rgba(148, 163, 184, 0.18)';
            const palette = ['#1d4ed8', '#2563eb', '#3b82f6', '#60a5fa', '#93c5fd', '#bfdbfe'];

            const commonOptions = {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        labels: {
                            color: chartTextColor,
                            boxWidth: 12,
                            usePointStyle: true,
                            padding: 14,
                        }
                    },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        titleColor: '#ffffff',
                        bodyColor: '#ffffff',
                        padding: 12,
                    }
                }
            };

            const buildChart = (canvas, config, key) => {
                const existing = Chart.getChart(canvas);
                if (existing) {
                    existing.destroy();
                }

                window[key] = new Chart(canvas, config);
            };

            const trendContext = canvases.trend.getContext('2d');
            const trendGradient = trendContext.createLinearGradient(0, 0, 0, 340);
            trendGradient.addColorStop(0, 'rgba(37, 99, 235, 0.95)');
            trendGradient.addColorStop(1, 'rgba(147, 197, 253, 0.45)');

            buildChart(canvases.trend, {
                type: 'bar',
                data: {
                    labels: @json($trendSeries['labels']),
                    datasets: [{
                        label: 'Hours',
                        data: @json($trendSeries['values']),
                        backgroundColor: trendGradient,
                        borderRadius: 16,
                        borderSkipped: false,
                        maxBarThickness: 44
                    }]
                },
                options: {
                    ...commonOptions,
                    plugins: {
                        ...commonOptions.plugins,
                        legend: { display: false }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { color: chartTextColor, maxRotation: 0, autoSkipPadding: 16 }
                        },
                        y: {
                            beginAtZero: true,
                            grid: { color: chartGridColor },
                            ticks: { color: chartTextColor }
                        }
                    }
                }
            }, '__dashboardTrendChart');

            buildChart(canvases.status, {
                type: 'doughnut',
                data: {
                    labels: ['Upcoming', 'Running', 'Completed'],
                    datasets: [{
                        data: [{{ $upcomingTrainings }}, {{ $runningTrainings }}, {{ $completedTrainings }}],
                        backgroundColor: ['#93c5fd', '#2563eb', '#1e3a8a'],
                        borderWidth: 0
                    }]
                },
                options: {
                    ...commonOptions,
                    cutout: '62%',
                    plugins: {
                        ...commonOptions.plugins,
                        legend: { position: 'bottom', labels: commonOptions.plugins.legend.labels }
                    }
                }
            }, '__dashboardStatusChart');

            buildChart(canvases.gender, {
                type: 'doughnut',
                data: {
                    labels: @json($genderSummary->pluck('label')),
                    datasets: [{
                        data: @json($genderSummary->pluck('employee_count')),
                        backgroundColor: ['#1d4ed8', '#60a5fa', '#cbd5e1'],
                        borderWidth: 0
                    }]
                },
                options: {
                    ...commonOptions,
                    cutout: '62%',
                    plugins: {
                        ...commonOptions.plugins,
                        legend: { position: 'bottom', labels: commonOptions.plugins.legend.labels }
                    }
                }
            }, '__dashboardGenderChart');

            buildChart(canvases.position, {
                type: 'pie',
                data: {
                    labels: @json($positionSummary->pluck('label')),
                    datasets: [{
                        data: @json($positionSummary->pluck('employee_count')),
                        backgroundColor: ['#1d4ed8', '#3b82f6', '#93c5fd'],
                        borderWidth: 0
                    }]
                },
                options: {
                    ...commonOptions,
                    plugins: {
                        ...commonOptions.plugins,
                        legend: { position: 'bottom', labels: commonOptions.plugins.legend.labels }
                    }
                }
            }, '__dashboardPositionChart');

            buildChart(canvases.classification, {
                type: 'doughnut',
                data: {
                    labels: @json($classificationSummary->pluck('training_classification')->map(fn ($value) => $value ?: 'Unclassified')),
                    datasets: [{
                        data: @json($classificationSummary->pluck('total')),
                        backgroundColor: palette,
                        borderWidth: 0
                    }]
                },
                options: {
                    ...commonOptions,
                    cutout: '62%',
                    plugins: {
                        ...commonOptions.plugins,
                        legend: { position: 'bottom', labels: commonOptions.plugins.legend.labels }
                    }
                }
            }, '__dashboardClassificationChart');

            buildChart(canvases.department, {
                type: 'bar',
                data: {
                    labels: @json($departmentSummary->take(8)->pluck('label')),
                    datasets: [{
                        label: 'Hours',
                        data: @json($departmentSummary->take(8)->pluck('total_hours')),
                        backgroundColor: @json($departmentSummary->take(8)->values()->map(fn ($item, $index) => $index % 2 === 0 ? '#2563eb' : '#93c5fd')),
                        borderRadius: 14,
                        borderSkipped: false,
                        maxBarThickness: 28
                    }]
                },
                options: {
                    ...commonOptions,
                    indexAxis: 'y',
                    plugins: {
                        ...commonOptions.plugins,
                        legend: { display: false }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            grid: { color: chartGridColor },
                            ticks: { color: chartTextColor }
                        },
                        y: {
                            grid: { display: false },
                            ticks: { color: chartTextColor }
                        }
                    }
                }
            }, '__dashboardDepartmentChart');

            buildChart(canvases.provider, {
                type: 'line',
                data: {
                    labels: @json($providerSummary->pluck('provider_label')),
                    datasets: [{
                        label: 'Trainings',
                        data: @json($providerSummary->pluck('total')),
                        borderColor: '#1d4ed8',
                        backgroundColor: 'rgba(37, 99, 235, 0.16)',
                        fill: true,
                        tension: 0.35,
                        pointBackgroundColor: '#1d4ed8',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 5
                    }]
                },
                options: {
                    ...commonOptions,
                    plugins: {
                        ...commonOptions.plugins,
                        legend: { display: false }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { color: chartTextColor }
                        },
                        y: {
                            beginAtZero: true,
                            grid: { color: chartGridColor },
                            ticks: { color: chartTextColor, precision: 0 }
                        }
                    }
                }
            }, '__dashboardProviderChart');

            if (canvases.cost) {
                const costData = @json($trainingCostSummary);
                const costLabels = costData.map((item) => item.name.length > 30 ? item.name.substring(0, 30) + '…' : item.name);
                const costValues = costData.map((item) => item.total_cost);
                const costColors = costData.map((_, i) => i % 2 === 0 ? '#16a34a' : '#4ade80');

                buildChart(canvases.cost, {
                    type: 'bar',
                    data: {
                        labels: costLabels,
                        datasets: [{
                            label: 'Total Cost (Rp)',
                            data: costValues,
                            backgroundColor: costColors,
                            borderRadius: 14,
                            borderSkipped: false,
                            maxBarThickness: 28
                        }]
                    },
                    options: {
                        ...commonOptions,
                        indexAxis: 'y',
                        plugins: {
                            ...commonOptions.plugins,
                            legend: { display: false },
                            tooltip: {
                                ...commonOptions.plugins.tooltip,
                                callbacks: {
                                    label: (ctx) => ' Rp ' + ctx.parsed.x.toLocaleString('id-ID'),
                                    afterLabel: (ctx) => {
                                        const item = costData[ctx.dataIndex];
                                        return item ? ` ${item.attended_count} hadir × Rp ${item.cost_per_person.toLocaleString('id-ID')}` : '';
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                beginAtZero: true,
                                grid: { color: chartGridColor },
                                ticks: {
                                    color: chartTextColor,
                                    callback: (value) => 'Rp ' + (value / 1000000).toFixed(1) + ' jt'
                                }
                            },
                            y: {
                                grid: { display: false },
                                ticks: { color: chartTextColor }
                            }
                        }
                    }
                }, '__dashboardCostChart');
            }
        })();
    </script>
@endpush
