@extends('layouts.app', [
    'title' => 'Edit Training',
    'header' => 'Edit Training',
    'subtitle' => 'Update schedule, category, and access settings'
])

@section('page_actions')
    <div class="toolbar-actions w-full lg:w-auto">
        <a href="{{ route('trainings.show', $training) }}" class="btn btn-light">View Detail</a>
        <a href="{{ route('trainings.index') }}" class="btn btn-light">Back to Training</a>
    </div>
@endsection

@section('content')
    <div class="card mt-6">
        <div class="card-header">
            <div class="content-card-header">
                <div>
                    <div class="content-card-label">Edit Training</div>
                    <h3 class="card-title">Training setup form</h3>
                    <p class="content-card-subtitle">Keep training data accurate for registration, attendance, and reporting.</p>
                </div>
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('trainings.update', $training) }}" method="POST">
                @method('PUT')
                @include('trainings._form')
                <div class="form-actions">
                    <a href="{{ route('trainings.show', $training) }}" class="btn btn-light">Cancel</a>
                    <button class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
@endsection
