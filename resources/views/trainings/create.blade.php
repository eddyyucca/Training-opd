@extends('layouts.app', [
    'title' => 'Add Training',
    'header' => 'Add Training',
    'subtitle' => 'Create a training plan with clean scheduling'
])

@section('page_actions')
    <div class="toolbar-actions w-full lg:w-auto">
        <a href="{{ route('trainings.index') }}" class="btn btn-light">Back to Training</a>
    </div>
@endsection

@section('content')
    <div class="card mt-6">
        <div class="card-header">
            <div class="content-card-header">
                <div>
                    <div class="content-card-label">New Training</div>
                    <h3 class="card-title">Training setup form</h3>
                    <p class="content-card-subtitle">Create the schedule first. Participants will join through registration QR.</p>
                </div>
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('trainings.store') }}" method="POST">
                @include('trainings._form')
                <div class="form-actions">
                    <a href="{{ route('trainings.index') }}" class="btn btn-light">Cancel</a>
                    <button class="btn btn-primary">Save Training</button>
                </div>
            </form>
        </div>
    </div>
@endsection
