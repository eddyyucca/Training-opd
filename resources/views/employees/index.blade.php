@extends('layouts.app', [
    'title' => 'Employees',
    'header' => 'Employees',
    'subtitle' => 'Employee directory and training participants'
])

@section('page_actions')
    <div class="toolbar-actions w-full lg:w-auto">
        <a href="{{ route('employees.create') }}" class="btn btn-primary">Add Employee</a>
    </div>
@endsection

@section('content')
    <div class="card mt-6">
        <div class="card-header">
            <div class="content-card-header">
                <div>
                    <div class="content-card-label">Employee Filters</div>
                    <h3 class="card-title">Find the right employee quickly</h3>
                    <p class="content-card-subtitle">Search by employee ID, name, division, department, position, WhatsApp number, or email.</p>
                </div>
            </div>
        </div>
        <div class="card-body">
            <form method="GET" class="grid gap-4 lg:grid-cols-[minmax(0,1.5fr)_minmax(180px,0.7fr)_minmax(180px,0.7fr)_auto]">
                <div>
                    <label for="employee-search">Search</label>
                    <input id="employee-search" type="text" name="search" value="{{ $search }}" class="form-control" placeholder="Search by ID, name, department, WhatsApp, or email">
                </div>
                <div>
                    <label for="employee-department">Department</label>
                    <select id="employee-department" name="department" class="form-control">
                        <option value="">All Departments</option>
                        @foreach ($departmentOptions as $item)
                            <option value="{{ $item }}" @selected($department === $item)>{{ $item }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="employee-status">Status</label>
                    <select id="employee-status" name="status" class="form-control no-enhance">
                        <option value="">All Statuses</option>
                        <option value="active" @selected($status === 'active')>Active</option>
                        <option value="inactive" @selected($status === 'inactive')>Inactive</option>
                    </select>
                </div>
                <div class="flex items-end gap-3">
                    <button class="btn btn-primary w-full sm:w-auto">Apply</button>
                    <a href="{{ route('employees.index') }}" class="btn btn-light w-full sm:w-auto">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        <div class="detail-item">
            <span class="detail-item-label">Total Employees</span>
            <div class="detail-item-value">{{ number_format($totalEmployees, 0, ',', '.') }}</div>
        </div>
        <div class="detail-item">
            <span class="detail-item-label">Active Employees</span>
            <div class="detail-item-value">{{ number_format($activeEmployees, 0, ',', '.') }}</div>
        </div>
        <div class="detail-item">
            <span class="detail-item-label">Departments</span>
            <div class="detail-item-value">{{ number_format($departmentCount, 0, ',', '.') }}</div>
        </div>
    </div>

    <div class="card mt-6">
        <div class="card-header">
            <div class="content-card-header">
                <div>
                    <div class="content-card-label">Employee List</div>
                    <h3 class="card-title">Employee records</h3>
                </div>
                <div class="text-sm text-slate-500">{{ number_format($employees->total(), 0, ',', '.') }} results</div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="hidden xl:block">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                        <tr>
                            <th>Employee ID</th>
                            <th>Name</th>
                            <th>Division</th>
                            <th>Department</th>
                            <th>Position</th>
                            <th>Contact</th>
                            <th>Job Level</th>
                            <th>Status</th>
                            <th class="text-right">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse ($employees as $employee)
                            <tr>
                                <td><span class="table-main">{{ $employee->nik }}</span></td>
                                <td>
                                    <div class="table-stack">
                                        <span class="table-main">{{ $employee->name }}</span>
                                        <span class="table-sub">{{ $employee->company ?: 'Company not set' }}</span>
                                    </div>
                                </td>
                                <td>{{ $employee->division ?: '-' }}</td>
                                <td>{{ $employee->department ?: '-' }}</td>
                                <td>{{ $employee->position_title ?: '-' }}</td>
                                <td>
                                    <div class="table-stack">
                                        <span class="table-main">{{ $employee->whatsapp_number ?: '-' }}</span>
                                        <span class="table-sub">{{ $employee->email ?: 'Email not set' }}</span>
                                    </div>
                                </td>
                                <td>{{ $employee->job_level_group }}</td>
                                <td>
                                    <span class="badge badge-{{ $employee->is_active ? 'success' : 'secondary' }}">
                                        {{ $employee->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('employees.show', $employee) }}" class="btn btn-sm btn-light">View</a>
                                        <a href="{{ route('employees.edit', $employee) }}" class="btn btn-sm btn-warning">Edit</a>
                                        <form action="{{ route('employees.destroy', $employee) }}" method="POST" class="d-inline" data-confirm data-confirm-message="Delete this employee record?">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="9" class="empty-state">No employee data is available yet.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="grid gap-4 p-4 xl:hidden">
                @forelse ($employees as $employee)
                    <article class="rounded-[22px] border border-slate-100 bg-slate-50/70 p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <h4 class="truncate text-sm font-extrabold text-slate-900">{{ $employee->name }}</h4>
                                <p class="mt-1 text-xs text-slate-500">{{ $employee->nik }} • {{ $employee->company ?: 'Company not set' }}</p>
                            </div>
                            <span class="badge badge-{{ $employee->is_active ? 'success' : 'secondary' }}">
                                {{ $employee->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                        <div class="mt-4 grid gap-3 sm:grid-cols-2">
                            <div class="detail-item">
                                <span class="detail-item-label">Department</span>
                                <div class="detail-item-value text-sm">{{ $employee->department ?: '-' }}</div>
                            </div>
                            <div class="detail-item">
                                <span class="detail-item-label">Division</span>
                                <div class="detail-item-value text-sm">{{ $employee->division ?: '-' }}</div>
                            </div>
                            <div class="detail-item">
                                <span class="detail-item-label">Position</span>
                                <div class="detail-item-value text-sm">{{ $employee->position_title ?: '-' }}</div>
                            </div>
                            <div class="detail-item">
                                <span class="detail-item-label">WhatsApp</span>
                                <div class="detail-item-value text-sm">{{ $employee->whatsapp_number ?: '-' }}</div>
                            </div>
                            <div class="detail-item sm:col-span-2">
                                <span class="detail-item-label">Email</span>
                                <div class="detail-item-value text-sm break-all">{{ $employee->email ?: '-' }}</div>
                            </div>
                            <div class="detail-item">
                                <span class="detail-item-label">Job Level</span>
                                <div class="detail-item-value text-sm">{{ $employee->job_level_group }}</div>
                            </div>
                        </div>
                        <div class="mt-4 flex flex-wrap gap-2">
                            <a href="{{ route('employees.show', $employee) }}" class="btn btn-sm btn-light">View</a>
                            <a href="{{ route('employees.edit', $employee) }}" class="btn btn-sm btn-warning">Edit</a>
                            <form action="{{ route('employees.destroy', $employee) }}" method="POST" class="d-inline" data-confirm data-confirm-message="Delete this employee record?">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </div>
                    </article>
                @empty
                    <div class="empty-state">No employee data is available yet.</div>
                @endforelse
            </div>
        </div>
        <div class="card-footer">
            {{ $employees->links() }}
        </div>
    </div>
@endsection
