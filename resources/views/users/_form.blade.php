@csrf

<div class="grid gap-6 xl:grid-cols-[minmax(0,1.1fr)_minmax(280px,0.9fr)]">
    <div class="space-y-6">
        <div class="form-section">
            <div class="content-card-label">Core Access</div>
            <h3 class="form-section-title">User account details</h3>
            <div class="grid gap-4 md:grid-cols-2">
                <div class="form-group mb-0">
                    <label for="user-name">Full Name</label>
                    <input id="user-name" type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" placeholder="Enter full name" required>
                </div>
                <div class="form-group mb-0">
                    <label for="user-email">Email Address</label>
                    <input id="user-email" type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" placeholder="Enter email address" required>
                </div>
            </div>
        </div>

        <div class="form-section">
            <div class="content-card-label">Permissions</div>
            <h3 class="form-section-title">Role and department scope</h3>
            <div class="grid gap-4 md:grid-cols-2">
                <div class="form-group mb-0">
                    <label for="user-role">Role</label>
                    <select id="user-role" name="role" class="form-control no-enhance" required>
                        @foreach ($roles as $value => $label)
                            <option value="{{ $value }}" @selected(old('role', $user->role) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group mb-0">
                    <label for="user-department">Department</label>
                    <select id="user-department" name="department_id" class="form-control">
                        <option value="">Select department</option>
                        @foreach ($departments as $department)
                            <option value="{{ $department->id }}" @selected((int) old('department_id', $user->department_id) === (int) $department->id)>{{ $department->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    <aside class="space-y-6">
        <div class="glass-panel">
            <div class="card-body p-5">
                <div class="content-card-label text-white/70">Security</div>
                <h3 class="text-xl font-extrabold text-white">{{ isset($requirePassword) && $requirePassword ? 'Set an initial password' : 'Update password if needed' }}</h3>
                <p class="mt-2 text-sm text-white/75">
                    {{ isset($requirePassword) && $requirePassword ? 'Create a secure password for the new internal user.' : 'Leave the password empty to keep the current one unchanged.' }}
                </p>
                <div class="mt-5">
                    <label for="user-password" class="mb-2 block text-sm font-semibold text-white">Password</label>
                    <input id="user-password" type="password" name="password" class="form-control" placeholder="{{ isset($requirePassword) && $requirePassword ? 'Enter password' : 'Leave blank to keep current password' }}" @required(isset($requirePassword) && $requirePassword)>
                </div>
            </div>
        </div>
    </aside>
</div>
