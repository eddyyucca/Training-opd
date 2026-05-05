@php
    $pageTitle = $mode === 'registration' ? 'Registration QR' : 'Attendance QR';
    $qrMarkup = null;

    if ($publicUrl) {
        $qrMarkup = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
            ->size(280)
            ->margin(1)
            ->generate($publicUrl);
    }
@endphp

@extends('layouts.app', [
    'title' => $pageTitle,
    'header' => $pageTitle,
    'subtitle' => $training->name
])

@section('page_actions')
    <div class="toolbar-actions w-full lg:w-auto">
        <a href="{{ route('trainings.show', $training) }}" class="btn btn-light">Back to Detail</a>
    </div>
@endsection

@section('content')
    <div class="mt-6 grid gap-6 xl:grid-cols-[minmax(300px,0.75fr)_minmax(0,1.25fr)]">
        <div class="card">
            <div class="card-header">
                <div class="content-card-label">Training Info</div>
                <h3 class="card-title">{{ $mode === 'registration' ? 'Registration access' : 'Attendance access' }}</h3>
            </div>
            <div class="card-body">
                <div class="metric-inline">
                    <div>
                        <strong>Training</strong>
                        <small>Training name</small>
                    </div>
                    <div class="text-right">
                        <strong>{{ $training->name }}</strong>
                    </div>
                </div>
                <div class="metric-inline">
                    <div>
                        <strong>Department</strong>
                        <small>Owner department</small>
                    </div>
                    <div class="text-right">
                        <strong>{{ $training->department?->name ?: '-' }}</strong>
                    </div>
                </div>
                <div class="metric-inline">
                    <div>
                        <strong>Date</strong>
                        <small>Scheduled date</small>
                    </div>
                    <div class="text-right">
                        <strong>{{ $training->start_date?->format('d M Y') ?: '-' }}</strong>
                    </div>
                </div>
                <div class="metric-inline">
                    <div>
                        <strong>Time</strong>
                        <small>Daily session time</small>
                    </div>
                    <div class="text-right">
                        <strong>{{ $training->start_time?->format('H:i') ?: '-' }} - {{ $training->end_time?->format('H:i') ?: '-' }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <div class="content-card-label">QR Access</div>
                <h3 class="card-title">{{ $pageTitle }}</h3>
            </div>
            <div class="card-body text-center">
                @if ($publicUrl && $qrMarkup)
                    <div class="inline-flex rounded-[28px] border border-slate-100 bg-white p-5 shadow-sm">
                        {!! $qrMarkup !!}
                    </div>
                    <div class="mt-5 detail-item text-left">
                        <span class="detail-item-label">Public Link</span>
                        <div class="detail-item-value break-all text-sm">{{ $publicUrl }}</div>
                    </div>
                    <div class="mt-4 flex flex-wrap justify-center gap-3">
                        <a href="{{ $publicUrl }}" target="_blank" rel="noopener" class="btn btn-primary">Open Public Page</a>
                        <button type="button" class="btn btn-light" onclick="window.print()">Print QR</button>
                    </div>
                @else
                    <div class="empty-state">
                        {{ $mode === 'registration' ? 'Registration is already closed because the training has started.' : 'Attendance is already closed because the training has ended.' }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
