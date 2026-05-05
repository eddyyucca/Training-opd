@extends('layouts.app', [
    'title' => 'Users',
    'header' => 'User Management',
    'subtitle' => 'Access, roles, and department scope'
])

@section('page_actions')
    <div class="toolbar-actions w-full lg:w-auto">
        <a href="{{ route('users.create') }}" class="btn btn-primary">Add User</a>
    </div>
@endsection

@section('content')
    <div class="card mt-6">
        <div class="card-header">
            <div class="content-card-header">
                <div>
                    <div class="content-card-label">User Directory</div>
                    <h3 class="card-title">Internal users</h3>
                    <p class="content-card-subtitle">Create and manage internal access from dedicated pages.</p>
                </div>
                <div class="text-sm text-slate-500">{{ number_format($users->count(), 0, ',', '.') }} users</div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="hidden xl:block">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Department</th>
                            <th class="text-right">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse ($users as $user)
                            <tr>
                                <td><span class="table-main">{{ $user->name }}</span></td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->role === 'opd' ? 'Organization and People Development' : 'Department User' }}</td>
                                <td>{{ $user->department?->name ?: '-' }}</td>
                                <td>
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-warning">Edit</a>
                                        <form method="POST" action="{{ route('users.destroy', $user) }}" class="d-inline" data-confirm data-confirm-message="Delete this user?">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger" @disabled(auth()->id() === $user->id)>Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="empty-state">No users are available yet.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="grid gap-4 p-4 xl:hidden">
                @forelse ($users as $user)
                    <article class="rounded-[22px] border border-slate-100 bg-slate-50/70 p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <h4 class="truncate text-sm font-extrabold text-slate-900">{{ $user->name }}</h4>
                                <p class="mt-1 text-xs text-slate-500">{{ $user->email }}</p>
                            </div>
                            <span class="badge badge-light">{{ $user->role === 'opd' ? 'OPD' : 'Department' }}</span>
                        </div>
                        <div class="mt-4 grid gap-3 sm:grid-cols-2">
                            <div class="detail-item">
                                <span class="detail-item-label">Role</span>
                                <div class="detail-item-value text-sm">{{ $user->role === 'opd' ? 'Organization and People Development' : 'Department User' }}</div>
                            </div>
                            <div class="detail-item">
                                <span class="detail-item-label">Department</span>
                                <div class="detail-item-value text-sm">{{ $user->department?->name ?: '-' }}</div>
                            </div>
                        </div>
                        <div class="mt-4 flex flex-wrap gap-2">
                            <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-warning">Edit</a>
                            <form method="POST" action="{{ route('users.destroy', $user) }}" class="d-inline" data-confirm data-confirm-message="Delete this user?">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger" @disabled(auth()->id() === $user->id)>Delete</button>
                            </form>
                        </div>
                    </article>
                @empty
                    <div class="empty-state">No users are available yet.</div>
                @endforelse
            </div>
        </div>
    </div>
@endsection
