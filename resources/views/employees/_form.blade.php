@csrf

<div class="grid gap-6 xl:grid-cols-[minmax(0,1.2fr)_minmax(280px,0.8fr)]">
    <div class="space-y-6">
        <div class="form-section">
            <div class="content-card-label">Core Information</div>
            <h3 class="form-section-title">Employee identity</h3>
            <div class="grid gap-4 md:grid-cols-2">
                <div class="form-group mb-0">
                    <label for="employee-nik">Employee ID @if($employee->is_external ?? false)<span class="text-slate-400 font-normal">(optional for external)</span>@endif</label>
                    <input id="employee-nik" type="text" name="nik" class="form-control" value="{{ old('nik', $employee->nik) }}" placeholder="Enter employee ID" @required(!($employee->is_external ?? false))>
                </div>
                <div class="form-group mb-0">
                    <label for="employee-name">Full Name</label>
                    <input id="employee-name" type="text" name="name" class="form-control" value="{{ old('name', $employee->name) }}" placeholder="Enter employee full name" required>
                </div>
            </div>
        </div>

        <div class="form-section">
            <div class="content-card-label">Organization</div>
            <h3 class="form-section-title">Placement details</h3>
            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                <div class="form-group mb-0">
                    <label for="employee-gender">Gender</label>
                    <select id="employee-gender" name="gender" class="form-control no-enhance">
                        <option value="">Select gender</option>
                        @foreach ($genders as $gender)
                            <option value="{{ $gender }}" @selected(old('gender', $employee->gender) === $gender)>{{ $gender }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group mb-0">
                    <label for="employee-division">Division</label>
                    <input id="employee-division" type="text" name="division" class="form-control" value="{{ old('division', $employee->division) }}" placeholder="Enter division">
                </div>
                <div class="form-group mb-0">
                    <label for="employee-department">Department</label>
                    @if ($canChooseDepartment)
                        <select id="employee-department" name="department" class="form-control">
                            <option value="">Select department</option>
                            @foreach ($departments as $department)
                                <option value="{{ $department->name }}" @selected(old('department', $employee->department) === $department->name)>{{ $department->name }}</option>
                            @endforeach
                        </select>
                    @else
                        <input type="hidden" name="department" value="{{ old('department', $employee->department) }}">
                        <input id="employee-department" type="text" class="form-control" value="{{ old('department', $employee->department) }}" readonly>
                    @endif
                </div>
                <div class="form-group mb-0">
                    <label for="employee-position">Position</label>
                    <input id="employee-position" type="text" name="position_title" class="form-control" value="{{ old('position_title', $employee->position_title) }}" placeholder="Enter position title">
                </div>
                <div class="form-group mb-0">
                    <label for="employee-company">Company</label>
                    <input id="employee-company" type="text" name="company" class="form-control" value="{{ old('company', $employee->company) }}" placeholder="Enter company name">
                </div>
                <div class="form-group mb-0">
                    <label for="employee-job-level">Job Level Group</label>
                    <select id="employee-job-level" name="job_level_group" class="form-control no-enhance" @required(!($employee->is_external ?? false))>
                        @if ($employee->is_external ?? false)
                            <option value="">— Not applicable —</option>
                        @endif
                        @foreach ($jobLevelGroups as $group)
                            <option value="{{ $group }}" @selected(old('job_level_group', $employee->job_level_group ?: ($employee->is_external ? '' : 'Staff & Non Staff')) === $group)>{{ $group }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group mb-0">
                    <label for="employee-whatsapp">WhatsApp Number</label>
                    <input id="employee-whatsapp" type="text" name="whatsapp_number" class="form-control" value="{{ old('whatsapp_number', $employee->whatsapp_number) }}" placeholder="Enter WhatsApp number">
                </div>
                <div class="form-group mb-0 md:col-span-2">
                    <label for="employee-email">Email Address</label>
                    <input id="employee-email" type="email" name="email" class="form-control" value="{{ old('email', $employee->email) }}" placeholder="Enter email address">
                </div>
            </div>
        </div>
    </div>

    <aside class="space-y-6">
        <div class="glass-panel">
            <div class="card-body p-5">
                <div class="content-card-label text-white/70">Profile Status</div>
                <h3 class="text-xl font-extrabold text-white">Keep the employee record clean</h3>
                <p class="mt-2 text-sm text-white/75">Use only relevant fields so training reports stay accurate and easy to read.</p>
                <div class="mt-5 rounded-[22px] border border-white/10 bg-white/10 p-4">
                    <label for="is_active" class="mb-0 flex cursor-pointer items-start gap-3 text-white">
                        <input type="checkbox" name="is_active" value="1" class="mt-1 h-4 w-4 rounded border-white/30 text-brand-700" id="is_active" @checked(old('is_active', $employee->is_active))>
                        <span>
                            <span class="block text-sm font-bold">Active employee</span>
                            <span class="mt-1 block text-xs text-white/70">Inactive employees remain in history but can be marked as inactive.</span>
                        </span>
                    </label>
                </div>
            </div>
        </div>
    </aside>
</div>
