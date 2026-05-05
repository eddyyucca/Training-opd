@extends('layouts.app', [
    'title' => 'Add Employee',
    'header' => 'Add Employee',
    'subtitle' => 'Create a clean employee profile'
])

@section('page_actions')
    <div class="toolbar-actions w-full lg:w-auto">
        <a href="{{ route('employees.index') }}" class="btn btn-light">Back to Employees</a>
    </div>
@endsection

@section('content')
    <div class="card mt-6">
        <div class="card-header">
            <div class="content-card-header">
                <div>
                    <div class="content-card-label">New Employee</div>
                    <h3 class="card-title">Employee profile form</h3>
                    <p class="content-card-subtitle">Enter only the fields that matter for training tracking and reporting.</p>
                </div>
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('employees.store') }}" method="POST">
                @include('employees._form')
                <div class="form-actions">
                    <a href="{{ route('employees.index') }}" class="btn btn-light">Cancel</a>
                    <button class="btn btn-primary">Save Employee</button>
                </div>
            </form>
        </div>
    </div>
@endsection
