@extends('layouts.app', [
    'title' => 'Add User',
    'header' => 'Add User',
    'subtitle' => 'Create a new internal user account'
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
                    <div class="content-card-label">New User</div>
                    <h3 class="card-title">Internal access form</h3>
                    <p class="content-card-subtitle">Create a user with the right role and department access.</p>
                </div>
            </div>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('users.store') }}">
                @php($requirePassword = true)
                @include('users._form')
                <div class="form-actions">
                    <a href="{{ route('users.index') }}" class="btn btn-light">Cancel</a>
                    <button class="btn btn-primary">Save User</button>
                </div>
            </form>
        </div>
    </div>
@endsection
