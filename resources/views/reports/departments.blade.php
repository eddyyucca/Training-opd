@extends('layouts.app', [
    'title' => 'Department Reports',
    'header' => 'Department Reports',
    'subtitle' => 'Training summary and department activity'
])

@section('page_actions')
    <form method="GET" class="toolbar-form w-full lg:w-auto">
        <select name="year" class="form-control no-enhance">
            <option value="">All Years</option>
            @foreach ($availableYears as $year)
                <option value="{{ $year }}" @selected((int) $selectedYear === (int) $year)>{{ $year }}</option>
            @endforeach
        </select>
        <select name="department" class="form-control">
            <option value="">All Departments</option>
            @foreach ($departmentOptions as $department)
                <option value="{{ $department }}" @selected($selectedDepartment === $department)>{{ $department }}</option>
            @endforeach
        </select>
        <button class="btn btn-primary">Apply</button>
        <a href="{{ route('reports.departments') }}" class="btn btn-light">Reset</a>
    </form>
@endsection

@section('content')
    @php
        $topDepartment = $departmentSummary->first();
        $totalHours = (float) $departmentSummary->sum('total_hours');
        $totalEmployees = (int) $departmentSummary->sum('employee_count');
        $totalParticipation = (int) $departmentSummary->sum('participation_count');
    @endphp

    <div class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="detail-item">
            <span class="detail-item-label">Departments</span>
            <div class="detail-item-value">{{ number_format($departmentSummary->count(), 0, ',', '.') }}</div>
        </div>
        <div class="detail-item">
            <span class="detail-item-label">Training Hours</span>
            <div class="detail-item-value">{{ number_format($totalHours, 0, ',', '.') }}</div>
        </div>
        <div class="detail-item">
            <span class="detail-item-label">Employees Covered</span>
            <div class="detail-item-value">{{ number_format($totalEmployees, 0, ',', '.') }}</div>
        </div>
        <div class="detail-item">
            <span class="detail-item-label">Participation Records</span>
            <div class="detail-item-value">{{ number_format($totalParticipation, 0, ',', '.') }}</div>
        </div>
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-[minmax(0,1.1fr)_minmax(320px,0.9fr)]">
        <div class="card">
            <div class="card-header">
                <div class="content-card-label">Overview</div>
                <h3 class="card-title">Training hours by department</h3>
            </div>
            <div class="card-body">
                <div class="chart-panel h-[320px] sm:h-[360px]">
                    <canvas id="report-department-chart"></canvas>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <div class="content-card-label">Highlights</div>
                <h3 class="card-title">Best current summary</h3>
            </div>
            <div class="card-body">
                @if ($topDepartment)
                    <div class="detail-item mb-4">
                        <span class="detail-item-label">Top Department</span>
                        <div class="detail-item-value">{{ $topDepartment['label'] }}</div>
                        <div class="mt-2 text-sm text-slate-500">{{ number_format($topDepartment['total_hours'], 0, ',', '.') }} hours delivered</div>
                    </div>
                @endif

                @forelse ($departmentSummary->take(4) as $item)
                    <div class="metric-inline">
                        <div>
                            <strong>{{ $item['label'] }}</strong>
                            <small>{{ number_format($item['employee_count'], 0, ',', '.') }} employees</small>
                        </div>
                        <div class="text-right">
                            <strong>{{ number_format($item['total_hours'], 0, ',', '.') }}</strong>
                            <small>hours</small>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">No department summary is available for this filter.</div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="card mt-6">
        <div class="card-header">
            <div class="content-card-header">
                <div>
                    <div class="content-card-label">Summary Table</div>
                    <h3 class="card-title">Department summary</h3>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                    <tr>
                        <th>Department</th>
                        <th>Training Hours</th>
                        <th>Employees</th>
                        <th>Participation</th>
                        <th>Average Hours</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($departmentSummary as $item)
                        <tr>
                            <td><span class="table-main">{{ $item['label'] }}</span></td>
                            <td>{{ number_format($item['total_hours'], 0, ',', '.') }}</td>
                            <td>{{ number_format($item['employee_count'], 0, ',', '.') }}</td>
                            <td>{{ number_format($item['participation_count'], 0, ',', '.') }}</td>
                            <td>{{ number_format($item['average_hours'], 2, '.', ',') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="empty-state">No department summary is available yet.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-6 grid gap-4">
        @forelse ($departmentDetails as $department => $items)
            <details class="card">
                <summary class="cursor-pointer list-none card-header">
                    <div class="content-card-header">
                        <div>
                            <div class="content-card-label">Department Detail</div>
                            <h3 class="card-title">{{ $department }}</h3>
                        </div>
                        <span class="table-count"><i class="fas fa-list"></i> {{ number_format($items->count(), 0, ',', '.') }} trainings</span>
                    </div>
                </summary>
                <div class="card-body p-0">
                    <div class="hidden lg:block">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                <tr>
                                    <th>Training</th>
                                    <th>Classification</th>
                                    <th>Provider</th>
                                    <th>Date</th>
                                    <th>Days</th>
                                    <th>Hours</th>
                                    <th>Employees</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($items as $item)
                                    <tr class="cursor-pointer" onclick="window.location='{{ route('trainings.show', $item->id) }}'">
                                        <td>
                                            <div class="table-stack">
                                                <a href="{{ route('trainings.show', $item->id) }}" class="table-main hover:underline" onclick="event.stopPropagation()">{{ $item->name }}</a>
                                                <span class="table-sub">{{ $item->category ?: 'Category not set' }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="table-stack">
                                                <span class="table-main">{{ $item->training_classification ?: '-' }}</span>
                                                <span class="table-sub">{{ $item->training_sub_classification ?: 'Sub-classification not set' }}</span>
                                            </div>
                                        </td>
                                        <td>{{ $item->provider ?: '-' }}</td>
                                        <td>{{ optional($item->start_date)->format('d M Y') ?? '-' }}</td>
                                        <td>{{ $item->days ?: 0 }}</td>
                                        <td>{{ number_format((float) $item->hours, 2, '.', ',') }}</td>
                                        <td>{{ number_format((int) $item->employee_count, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="grid gap-4 p-4 lg:hidden">
                        @foreach ($items as $item)
                            <a href="{{ route('trainings.show', $item->id) }}" class="block rounded-[22px] border border-slate-100 bg-slate-50/70 p-4">
                                <h4 class="text-sm font-extrabold text-slate-900">{{ $item->name }}</h4>
                                <p class="mt-1 text-xs text-slate-500">{{ $item->provider ?: 'Provider not set' }}</p>
                                <div class="mt-4 grid gap-3 sm:grid-cols-2">
                                    <div class="detail-item">
                                        <span class="detail-item-label">Classification</span>
                                        <div class="detail-item-value text-sm">{{ $item->training_classification ?: '-' }}</div>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-item-label">Date</span>
                                        <div class="detail-item-value text-sm">{{ optional($item->start_date)->format('d M Y') ?? '-' }}</div>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-item-label">Hours</span>
                                        <div class="detail-item-value text-sm">{{ number_format((float) $item->hours, 2, '.', ',') }}</div>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-item-label">Employees</span>
                                        <div class="detail-item-value text-sm">{{ number_format((int) $item->employee_count, 0, ',', '.') }}</div>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </details>
        @empty
            <div class="card">
                <div class="card-body empty-state">No training detail is available for the selected filter.</div>
            </div>
        @endforelse
    </div>
@endsection

@push('scripts')
    <script>
        (() => {
            const canvas = document.getElementById('report-department-chart');

            if (!canvas || typeof Chart === 'undefined') {
                return;
            }

            const existing = Chart.getChart(canvas);
            if (existing) {
                existing.destroy();
            }

            new Chart(canvas, {
                type: 'bar',
                data: {
                    labels: @json($departmentSummary->take(8)->pluck('label')),
                    datasets: [{
                        label: 'Hours',
                        data: @json($departmentSummary->take(8)->pluck('total_hours')),
                        backgroundColor: ['#1d4ed8', '#2563eb', '#3b82f6', '#60a5fa', '#93c5fd', '#1e40af', '#1d4ed8', '#2563eb'],
                        borderRadius: 14,
                        borderSkipped: false,
                        maxBarThickness: 36
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#0f172a',
                            titleColor: '#ffffff',
                            bodyColor: '#ffffff',
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            grid: { color: 'rgba(148, 163, 184, 0.18)' },
                            ticks: { color: '#475569' }
                        },
                        y: {
                            grid: { display: false },
                            ticks: { color: '#334155' }
                        }
                    }
                }
            });
        })();
    </script>
@endpush
