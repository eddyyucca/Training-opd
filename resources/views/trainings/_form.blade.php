@csrf

<div class="grid gap-6 xl:grid-cols-[minmax(0,1.18fr)_minmax(300px,0.82fr)]">
    <div class="space-y-6">
        <div class="form-section">
            <div class="content-card-label">Training Identity</div>
            <h3 class="form-section-title">Core training information</h3>
            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                <div class="form-group mb-0">
                    <label for="training-year">Year</label>
                    <select id="training-year" name="year" class="form-control no-enhance">
                        <option value="">Select year</option>
                        @foreach ($years as $year)
                            <option value="{{ $year }}" @selected((int) old('year', $training->year) === (int) $year)>{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group mb-0">
                    <label for="training-month">Month</label>
                    <select id="training-month" name="month" class="form-control no-enhance">
                        <option value="">Select month</option>
                        @foreach ($months as $month)
                            <option value="{{ $month }}" @selected(old('month', $training->month) === $month)>{{ $month }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group mb-0">
                    <label for="training-department">Owner Department</label>
                    @if ($canChooseDepartment)
                        <select id="training-department" name="department_id" class="form-control" required>
                            <option value="">Select department</option>
                            @foreach ($departments as $department)
                                <option value="{{ $department->id }}" @selected((int) old('department_id', $training->department_id) === (int) $department->id)>{{ $department->name }}</option>
                            @endforeach
                        </select>
                    @else
                        <input type="hidden" name="department_id" value="{{ old('department_id', $training->department_id) }}">
                        <input id="training-department" type="text" class="form-control" value="{{ optional($departments->firstWhere('id', old('department_id', $training->department_id)))->name }}" readonly>
                    @endif
                </div>
                <div class="form-group mb-0 md:col-span-2 xl:col-span-3">
                    <label for="training-name">Training Name</label>
                    <input id="training-name" type="text" name="name" class="form-control" value="{{ old('name', $training->name) }}" placeholder="Enter training name" required>
                </div>
                <div class="form-group mb-0">
                    <label for="training-classification-id">Classification</label>
                    <select name="training_classification_id" id="training-classification-id" class="form-control">
                        <option value="">Select classification</option>
                        @foreach ($classifications as $classification)
                            <option value="{{ $classification->id }}" @selected((int) old('training_classification_id', $training->training_classification_id) === (int) $classification->id)>{{ $classification->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group mb-0">
                    <label for="training-sub-classification-id">Sub-classification</label>
                    <select name="training_sub_classification_id" id="training-sub-classification-id" class="form-control">
                        <option value="">Select sub-classification</option>
                        @foreach ($classifications as $classification)
                            @foreach ($classification->subClassifications as $subClassification)
                                <option
                                    value="{{ $subClassification->id }}"
                                    data-classification="{{ $classification->id }}"
                                    @selected((int) old('training_sub_classification_id', $training->training_sub_classification_id) === (int) $subClassification->id)
                                >
                                    {{ $subClassification->name }}
                                </option>
                            @endforeach
                        @endforeach
                    </select>
                </div>
                <div class="form-group mb-0">
                    <label for="training-category-id">Category</label>
                    <select id="training-category-id" name="training_category_id" class="form-control">
                        <option value="">Select category</option>
                        @foreach ($categories as $item)
                            <option value="{{ $item->id }}" @selected((int) old('training_category_id', $training->training_category_id) === (int) $item->id)>{{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group mb-0">
                    <label for="training-type-id">Training Type</label>
                    <select id="training-type-id" name="training_type_id" class="form-control">
                        <option value="">Select training type</option>
                        @foreach ($types as $item)
                            <option value="{{ $item->id }}" @selected((int) old('training_type_id', $training->training_type_id) === (int) $item->id)>{{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group mb-0 md:col-span-2">
                    <label for="training-provider-id">Provider / Trainer</label>
                    <select id="training-provider-id" name="training_provider_id" class="form-control">
                        <option value="">Select provider</option>
                        @foreach ($providers as $item)
                            <option value="{{ $item->id }}" @selected((int) old('training_provider_id', $training->training_provider_id) === (int) $item->id)>{{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group mb-0">
                    <label for="training-quota">Registration Quota</label>
                    <input id="training-quota" type="number" min="1" name="quota" class="form-control" value="{{ old('quota', $training->quota) }}" placeholder="Enter participant quota">
                </div>
                <div class="form-group mb-0">
                    <label for="training-cost-per-person">Cost per Person (Rp)</label>
                    <input id="training-cost-per-person" type="number" min="0" step="1000" name="cost_per_person" class="form-control" value="{{ old('cost_per_person', $training->cost_per_person) }}" placeholder="e.g. 1500000">
                </div>
                <div class="form-group mb-0">
                    <label for="training-pr-number">PR Number <span class="text-slate-400 font-normal">(optional)</span></label>
                    <input id="training-pr-number" type="text" name="pr_number" class="form-control" value="{{ old('pr_number', $training->pr_number) }}" placeholder="e.g. PR-2026-001">
                </div>
            </div>
        </div>

        <div class="form-section">
            <div class="content-card-label">Schedule</div>
            <h3 class="form-section-title">Training timing</h3>
            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                <div class="form-group mb-0">
                    <label for="training-start-date">Start Date</label>
                    <input id="training-start-date" type="date" name="start_date" class="form-control" value="{{ old('start_date', optional($training->start_date)->format('Y-m-d')) }}">
                </div>
                <div class="form-group mb-0">
                    <label for="training-start-time">Start Time</label>
                    <input id="training-start-time" type="time" name="start_time" class="form-control" value="{{ old('start_time', optional($training->start_time)->format('H:i')) }}">
                </div>
                <div class="form-group mb-0">
                    <label for="training-end-date">End Date</label>
                    <input id="training-end-date" type="date" name="end_date" class="form-control" value="{{ old('end_date', optional($training->end_date)->format('Y-m-d')) }}">
                </div>
                <div class="form-group mb-0">
                    <label for="training-end-time">End Time</label>
                    <input id="training-end-time" type="time" name="end_time" class="form-control" value="{{ old('end_time', optional($training->end_time)->format('H:i')) }}">
                </div>
            </div>
        </div>

        <div class="form-section">
            <div class="content-card-label">Notes</div>
            <h3 class="form-section-title">Additional information</h3>
            <div class="form-group mb-0">
                <label for="training-notes">Notes</label>
                <textarea id="training-notes" name="notes" class="form-control" rows="4" placeholder="Add optional notes for internal use">{{ old('notes', $training->notes) }}</textarea>
            </div>
        </div>
    </div>

    <aside class="space-y-6">
        <div class="glass-panel">
            <div class="card-body p-5">
                <div class="content-card-label text-white/70">Duration Preview</div>
                <h3 class="text-xl font-extrabold text-white">Auto-calculated training duration</h3>
                <p class="mt-2 text-sm text-white/75">Hours are calculated from the daily start and end time, then multiplied by the number of scheduled days.</p>
                <div class="mt-5 grid gap-3">
                    <div class="detail-item bg-white/12 border-white/10">
                        <span class="detail-item-label text-white/65">Total Hours</span>
                        <div class="detail-item-value text-white" id="duration-preview">{{ number_format((float) ($training->hours ?? 0), 2, '.', ',') }} hours</div>
                    </div>
                    <div class="detail-item bg-white/12 border-white/10">
                        <span class="detail-item-label text-white/65">Scheduled Days</span>
                        <div class="detail-item-value text-white" id="days-preview">{{ (int) ($training->days ?? 0) }} days</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="glass-panel">
            <div class="card-body p-5">
                <div class="content-card-label text-white/70">Cost Preview</div>
                <h3 class="text-xl font-extrabold text-white">Estimated training budget</h3>
                <p class="mt-2 text-sm text-white/75">Estimated total cost based on cost per person and registration quota.</p>
                <div class="mt-5 grid gap-3">
                    <div class="detail-item bg-white/12 border-white/10">
                        <span class="detail-item-label text-white/65">Cost per Person</span>
                        <div class="detail-item-value text-white" id="cost-per-person-preview">
                            @if ($training->cost_per_person)
                                Rp {{ number_format((float) $training->cost_per_person, 0, ',', '.') }}
                            @else
                                -
                            @endif
                        </div>
                    </div>
                    <div class="detail-item bg-white/12 border-white/10">
                        <span class="detail-item-label text-white/65">Estimated Total (quota)</span>
                        <div class="detail-item-value text-white" id="total-cost-preview">
                            @if ($training->cost_per_person && $training->quota)
                                Rp {{ number_format((float) $training->cost_per_person * $training->quota, 0, ',', '.') }}
                            @else
                                -
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </aside>
</div>

@push('scripts')
    <script>
        (() => {
            const classificationSelect = document.getElementById('training-classification-id');
            const subClassificationSelect = document.getElementById('training-sub-classification-id');
            const startDateInput = document.getElementById('training-start-date');
            const startTimeInput = document.getElementById('training-start-time');
            const endDateInput = document.getElementById('training-end-date');
            const endTimeInput = document.getElementById('training-end-time');
            const durationPreview = document.getElementById('duration-preview');
            const daysPreview = document.getElementById('days-preview');

            if (classificationSelect && subClassificationSelect) {
                const filterSubClassification = () => {
                    const activeClassification = classificationSelect.value;

                    Array.from(subClassificationSelect.options).forEach((option) => {
                        if (!option.value) {
                            option.hidden = false;
                            return;
                        }

                        const visible = !activeClassification || option.dataset.classification === activeClassification;
                        option.hidden = !visible;

                        if (!visible && option.selected) {
                            option.selected = false;
                        }
                    });
                };

                classificationSelect.addEventListener('change', filterSubClassification);
                filterSubClassification();
            }

            if (startDateInput && startTimeInput && endDateInput && endTimeInput && durationPreview && daysPreview) {
                const updateDuration = () => {
                    if (!startDateInput.value) {
                        durationPreview.textContent = '0.00 hours';
                        daysPreview.textContent = '0 days';
                        return;
                    }

                    const resolvedEndDate = endDateInput.value || startDateInput.value;
                    const resolvedStartTime = startTimeInput.value || '00:00';
                    const resolvedEndTime = endTimeInput.value || resolvedStartTime;
                    const startDay = new Date(`${startDateInput.value}T00:00:00`);
                    const endDay = new Date(`${resolvedEndDate}T00:00:00`);
                    const dailyStart = new Date(`2000-01-01T${resolvedStartTime}:00`);
                    const dailyEnd = new Date(`2000-01-01T${resolvedEndTime}:00`);

                    if ([startDay, endDay, dailyStart, dailyEnd].some((item) => Number.isNaN(item.getTime()))) {
                        durationPreview.textContent = '0.00 hours';
                        daysPreview.textContent = '0 days';
                        return;
                    }

                    const diffDays = Math.max(Math.floor((endDay - startDay) / 86400000) + 1, 1);
                    const dailyHours = Math.max((dailyEnd - dailyStart) / 3600000, 0);
                    const totalHours = dailyHours * diffDays;

                    durationPreview.textContent = `${totalHours.toLocaleString('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    })} hours`;
                    daysPreview.textContent = `${diffDays} days`;
                };

                [startDateInput, startTimeInput, endDateInput, endTimeInput].forEach((input) => {
                    input.addEventListener('input', updateDuration);
                    input.addEventListener('change', updateDuration);
                });

                updateDuration();
            }

            const costInput = document.getElementById('training-cost-per-person');
            const quotaInput = document.getElementById('training-quota');
            const costPreview = document.getElementById('cost-per-person-preview');
            const totalCostPreview = document.getElementById('total-cost-preview');

            if (costInput && quotaInput && costPreview && totalCostPreview) {
                const updateCost = () => {
                    const cost = parseFloat(costInput.value) || 0;
                    const quota = parseInt(quotaInput.value) || 0;

                    if (!cost) {
                        costPreview.textContent = '-';
                        totalCostPreview.textContent = '-';
                        return;
                    }

                    costPreview.textContent = 'Rp ' + cost.toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
                    totalCostPreview.textContent = quota
                        ? 'Rp ' + (cost * quota).toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0 })
                        : '-';
                };

                [costInput, quotaInput].forEach((input) => {
                    input.addEventListener('input', updateCost);
                    input.addEventListener('change', updateCost);
                });

                updateCost();
            }
        })();
    </script>
@endpush
