@extends('layouts.app', [
    'title' => 'Edit User',
    'header' => 'Edit User',
    'subtitle' => 'Update user access and department scope'
])

@section('page_actions')
    <div class="toolbar-actions w-full lg:w-auto">
        <a href="{{ route('users.index') }}" class="btn btn-light">Back to Users</a>
    </div>
@endsection

@section('content')
    <div class="card mt-6">
        <div class="card-header">
            <div class="content-card-header">
                <div>
                    <div class="content-card-label">Edit User</div>
                    <h3 class="card-title">Internal access form</h3>
                    <p class="content-card-subtitle">Update role, department scope, and password only when needed.</p>
                </div>
            </div>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('users.update', $user) }}">
                @method('PUT')
                @php($requirePassword = false)
                @include('users._form')
                <div class="form-actions">
                    <a href="{{ route('users.index') }}" class="btn btn-light">Cancel</a>
                    <button class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
@endsection
