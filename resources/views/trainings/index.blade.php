@extends('layouts.app', [
    'title' => 'Training',
    'header' => 'Training',
    'subtitle' => 'Planning, monitoring, registration, and attendance'
])

@section('page_actions')
    <div class="toolbar-actions w-full lg:w-auto">
        <a href="{{ route('trainings.create') }}" class="btn btn-primary">Add Training</a>
    </div>
@endsection

@section('content')
    <div class="card mt-6">
        <div class="card-header">
            <div class="content-card-header">
                <div>
                    <div class="content-card-label">Training Filters</div>
                    <h3 class="card-title">Find training sessions quickly</h3>
                    <p class="content-card-subtitle">Search by training name, provider, classification, or year.</p>
                </div>
            </div>
        </div>
        <div class="card-body">
            <form method="GET" class="grid gap-4 lg:grid-cols-[minmax(0,1.5fr)_minmax(180px,0.6fr)_auto]">
                <div>
                    <label for="training-search">Search</label>
                    <input id="training-search" type="text" name="search" value="{{ $search }}" class="form-control" placeholder="Search by training, provider, or classification">
                </div>
                <div>
                    <label for="training-year">Year</label>
                    <select id="training-year" name="year" class="form-control no-enhance">
                        <option value="">All Years</option>
                        @foreach ($years as $itemYear)
                            <option value="{{ $itemYear }}" @selected((int) $year === (int) $itemYear)>{{ $itemYear }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end gap-3">
                    <button class="btn btn-primary w-full sm:w-auto">Apply</button>
                    <a href="{{ route('trainings.index') }}" class="btn btn-light w-full sm:w-auto">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        <div class="detail-item">
            <span class="detail-item-label">Total Trainings</span>
            <div class="detail-item-value">{{ number_format($trainings->total(), 0, ',', '.') }}</div>
        </div>
        <div class="detail-item">
            <span class="detail-item-label">Registered Participants</span>
            <div class="detail-item-value">{{ number_format($trainings->sum('registered_employees_count'), 0, ',', '.') }}</div>
        </div>
        <div class="detail-item">
            <span class="detail-item-label">Attended Participants</span>
            <div class="detail-item-value">{{ number_format($trainings->sum('attended_employees_count'), 0, ',', '.') }}</div>
        </div>
    </div>

    <div class="card mt-6">
        <div class="card-header">
            <div class="content-card-header">
                <div>
                    <div class="content-card-label">Training List</div>
                    <h3 class="card-title">Training records</h3>
                </div>
                <div class="text-sm text-slate-500">{{ number_format($trainings->total(), 0, ',', '.') }} results</div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="hidden xl:block">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                        <tr>
                            <th>Year</th>
                            <th>Training</th>
                            <th>Classification</th>
                            <th>Schedule</th>
                            <th>Department</th>
                            <th>Hours</th>
                            <th>Trainees</th>
                            <th class="text-right">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse ($trainings as $training)
                            <tr class="cursor-pointer" onclick="window.location='{{ route('trainings.show', $training) }}'">
                                <td>{{ $training->year ?: '-' }}</td>
                                <td>
                                    <div class="table-stack">
                                        <a href="{{ route('trainings.show', $training) }}" class="table-main hover:underline" onclick="event.stopPropagation()">{{ $training->name }}</a>
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
                                    <div class="table-stack">
                                        <span class="table-main">{{ optional($training->start_date)->format('d M Y') ?? '-' }}</span>
                                        <span class="table-sub">{{ $training->start_time?->format('H:i') ?: '-' }} - {{ $training->end_time?->format('H:i') ?: '-' }}</span>
                                    </div>
                                </td>
                                <td>{{ $training->department?->name ?: '-' }}</td>
                                <td>{{ number_format((float) $training->hours, 1, '.', ',') }}</td>
                                <td>
                                    <div class="table-stack">
                                        <span class="table-main">{{ $training->attended_employees_count }}</span>
                                        <span class="table-sub">{{ $training->registered_employees_count }} registered</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('trainings.show', $training) }}" class="btn btn-sm btn-light">View</a>
                                        <a href="{{ route('trainings.edit', $training) }}" class="btn btn-sm btn-warning">Edit</a>
                                        <form action="{{ route('trainings.destroy', $training) }}" method="POST" class="d-inline" data-confirm data-confirm-message="Delete this training record?">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="empty-state">No training data is available yet.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="grid gap-4 p-4 xl:hidden">
                @forelse ($trainings as $training)
                            <article class="rounded-[22px] border border-slate-100 bg-slate-50/70 p-4">
                        <a href="{{ route('trainings.show', $training) }}" class="block">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <h4 class="truncate text-sm font-extrabold text-slate-900">{{ $training->name }}</h4>
                                    <p class="mt-1 text-xs text-slate-500">{{ $training->provider ?: 'Provider not set' }}</p>
                                </div>
                                <span class="badge badge-light">{{ $training->year ?: '-' }}</span>
                            </div>
                            <div class="mt-4 grid gap-3 sm:grid-cols-2">
                                <div class="detail-item">
                                    <span class="detail-item-label">Classification</span>
                                    <div class="detail-item-value text-sm">{{ $training->training_classification ?: '-' }}</div>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-item-label">Department</span>
                                    <div class="detail-item-value text-sm">{{ $training->department?->name ?: '-' }}</div>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-item-label">Schedule</span>
                                    <div class="detail-item-value text-sm">{{ optional($training->start_date)->format('d M Y') ?? '-' }}</div>
                                    <div class="text-xs text-slate-500">{{ $training->start_time?->format('H:i') ?: '-' }} - {{ $training->end_time?->format('H:i') ?: '-' }}</div>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-item-label">Trainees</span>
                                    <div class="detail-item-value text-sm">{{ $training->attended_employees_count }}</div>
                                    <div class="text-xs text-slate-500">{{ $training->registered_employees_count }} registered</div>
                                </div>
                            </div>
                        </a>
                        <div class="mt-4 flex flex-wrap gap-2">
                            <a href="{{ route('trainings.edit', $training) }}" class="btn btn-sm btn-warning">Edit</a>
                            <form action="{{ route('trainings.destroy', $training) }}" method="POST" class="d-inline" data-confirm data-confirm-message="Delete this training record?">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </div>
                    </article>
                @empty
                    <div class="empty-state">No training data is available yet.</div>
                @endforelse
            </div>
        </div>
        <div class="card-footer">
            {{ $trainings->links() }}
        </div>
    </div>
@endsection
