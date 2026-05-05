@extends('layouts.app', [
    'title' => 'Edit Employee',
    'header' => 'Edit Employee',
    'subtitle' => 'Update the employee profile'
])

@section('page_actions')
    <div class="toolbar-actions w-full lg:w-auto">
        <a href="{{ route('employees.show', $employee) }}" class="btn btn-light">View Detail</a>
        <a href="{{ route('employees.index') }}" class="btn btn-light">Back to Employees</a>
    </div>
@endsection

@section('content')
    <div class="card mt-6">
        <div class="card-header">
            <div class="content-card-header">
                <div>
                    <div class="content-card-label">Edit Profile</div>
                    <h3 class="card-title">Employee profile form</h3>
                    <p class="content-card-subtitle">Keep employee information accurate for dashboard and training reports.</p>
                </div>
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('employees.update', $employee) }}" method="POST">
                @method('PUT')
                @include('employees._form')
                <div class="form-actions">
                    <a href="{{ route('employees.show', $employee) }}" class="btn btn-light">Cancel</a>
                    <button class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
@endsection
