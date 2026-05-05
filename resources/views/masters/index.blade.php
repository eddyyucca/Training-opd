@extends('layouts.app', [
    'title' => 'Master Data',
    'header' => 'Master Data',
    'subtitle' => 'Choose the master data area to manage'
])

@section('content')
    <div class="mt-6 grid gap-6 md:grid-cols-2 xl:grid-cols-3">
        @foreach ($sections as $section => $config)
            <a href="{{ route('masters.section', $section) }}" class="card block transition hover:-translate-y-1">
                <div class="card-body">
                    <div class="content-card-label">Master Data</div>
                    <h3 class="card-title mb-2">{{ $config['title'] }}</h3>
                    <p class="content-card-subtitle">{{ $config['subtitle'] }}</p>
                    <div class="form-actions mt-5 border-0 p-0">
                        <span class="btn btn-primary">Open</span>
                    </div>
                </div>
            </a>
        @endforeach
    </div>
@endsection
