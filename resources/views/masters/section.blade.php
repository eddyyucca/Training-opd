@extends('layouts.app', [
    'title' => $config['title'],
    'header' => $config['title'],
    'subtitle' => $config['subtitle']
])

@section('page_actions')
    <div class="toolbar-actions w-full lg:w-auto">
        <a href="{{ route('masters.index') }}" class="btn btn-light">Back to Master Data</a>
    </div>
@endsection

@section('content')
    <div class="mt-6 grid gap-6 xl:grid-cols-[minmax(320px,0.85fr)_minmax(0,1.15fr)]">
        <div class="card self-start">
            <div class="card-header">
                <div class="content-card-label">Create</div>
                <h3 class="card-title">Add {{ $config['title'] }}</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('masters.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="type" value="{{ $config['route_type'] }}">
                    @if ($section === 'department')
                        <div class="form-group">
                            <label for="master-code">Code</label>
                            <input id="master-code" type="text" name="code" class="form-control" required>
                        </div>
                    @endif
                    <div class="form-group">
                        <label for="master-name">{{ $section === 'classification' ? 'Classification Name' : 'Name' }}</label>
                        <input id="master-name" type="text" name="name" class="form-control" required>
                    </div>
                    <button class="btn btn-primary w-full">Save</button>
                </form>

                @if ($section === 'classification')
                    <div class="mt-6 border-t border-slate-100 pt-5">
                        <div class="content-card-label">Create</div>
                        <h3 class="card-title mb-4">Add Sub-classification</h3>
                        <form action="{{ route('masters.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="type" value="sub_classification">
                            <div class="form-group">
                                <label for="master-classification">Classification</label>
                                <select id="master-classification" name="training_classification_id" class="form-control" required>
                                    <option value="">Select classification</option>
                                    @foreach ($classifications as $classification)
                                        <option value="{{ $classification->id }}">{{ $classification->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="master-sub-name">Sub-classification Name</label>
                                <input id="master-sub-name" type="text" name="name" class="form-control" required>
                            </div>
                            <button class="btn btn-light w-full">Save Sub-classification</button>
                        </form>
                    </div>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <div class="content-card-label">Directory</div>
                <h3 class="card-title">{{ $config['title'] }} List</h3>
            </div>
            <div class="card-body">
                @if ($section === 'classification')
                    <div class="grid gap-4">
                        @forelse ($items as $item)
                            <div class="rounded-[22px] border border-slate-100 bg-slate-50/70 p-4">
                                <form method="POST" action="{{ route('masters.update', ['type' => 'classification', 'id' => $item->id]) }}" id="classification-form-{{ $item->id }}">
                                    @csrf
                                    @method('PUT')
                                    <div class="form-group mb-0">
                                        <label for="classification-name-{{ $item->id }}">Classification Name</label>
                                        <input id="classification-name-{{ $item->id }}" type="text" name="name" class="form-control" value="{{ $item->name }}" required>
                                    </div>
                                </form>
                                <div class="form-actions">
                                    <form method="POST" action="{{ route('masters.destroy', ['type' => 'classification', 'id' => $item->id]) }}" data-confirm data-confirm-message="Delete this classification?">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger">Delete</button>
                                    </form>
                                    <button class="btn btn-primary" form="classification-form-{{ $item->id }}">Save</button>
                                </div>

                                <div class="mt-4 grid gap-3">
                                    @foreach ($item->subClassifications as $sub)
                                        <div class="detail-item">
                                            <form method="POST" action="{{ route('masters.update', ['type' => 'sub_classification', 'id' => $sub->id]) }}" id="sub-form-{{ $sub->id }}">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="training_classification_id" value="{{ $item->id }}">
                                                <div class="form-group mb-0">
                                                    <label for="sub-name-{{ $sub->id }}">Sub-classification Name</label>
                                                    <input id="sub-name-{{ $sub->id }}" type="text" name="name" class="form-control" value="{{ $sub->name }}" required>
                                                </div>
                                            </form>
                                            <div class="form-actions">
                                                <form method="POST" action="{{ route('masters.destroy', ['type' => 'sub_classification', 'id' => $sub->id]) }}" data-confirm data-confirm-message="Delete this sub-classification?">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-danger">Delete</button>
                                                </form>
                                                <button class="btn btn-primary" form="sub-form-{{ $sub->id }}">Save</button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @empty
                            <div class="empty-state">No master data is available yet.</div>
                        @endforelse
                    </div>
                @else
                    <div class="grid gap-4">
                        @forelse ($items as $item)
                            <div class="rounded-[22px] border border-slate-100 bg-slate-50/70 p-4">
                                <form method="POST" action="{{ route('masters.update', ['type' => $config['route_type'], 'id' => $item->id]) }}" id="master-form-{{ $section }}-{{ $item->id }}">
                                    @csrf
                                    @method('PUT')
                                    <div class="grid gap-4 {{ $section === 'department' ? 'md:grid-cols-[160px_minmax(0,1fr)]' : '' }}">
                                        @if ($section === 'department')
                                            <div class="form-group mb-0">
                                                <label for="master-code-{{ $item->id }}">Code</label>
                                                <input id="master-code-{{ $item->id }}" type="text" name="code" class="form-control" value="{{ $item->code }}" required>
                                            </div>
                                        @endif
                                        <div class="form-group mb-0">
                                            <label for="master-name-{{ $item->id }}">Name</label>
                                            <input id="master-name-{{ $item->id }}" type="text" name="name" class="form-control" value="{{ $item->name }}" required>
                                        </div>
                                    </div>
                                </form>
                                <div class="form-actions">
                                    <form method="POST" action="{{ route('masters.destroy', ['type' => $config['route_type'], 'id' => $item->id]) }}" data-confirm data-confirm-message="Delete this master data item?">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger">Delete</button>
                                    </form>
                                    <button class="btn btn-primary" form="master-form-{{ $section }}-{{ $item->id }}">Save</button>
                                </div>
                            </div>
                        @empty
                            <div class="empty-state">No master data is available yet.</div>
                        @endforelse
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
