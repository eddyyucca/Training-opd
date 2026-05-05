<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    public function index(): View
    {
        abort_unless(Auth::user()?->isOpd(), 403);

        return view('users.index', [
            'users' => User::query()->with('department')->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        abort_unless(Auth::user()?->isOpd(), 403);

        return view('users.create', [
            'departments' => Department::query()->orderBy('name')->get(),
            'roles' => $this->roles(),
            'user' => new User(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless(Auth::user()?->isOpd(), 403);

        User::create($this->validatedData($request));

        return redirect()->route('users.index')->with('success', 'User added successfully.');
    }

    public function edit(User $user): View
    {
        abort_unless(Auth::user()?->isOpd(), 403);

        return view('users.edit', [
            'user' => $user->load('department'),
            'departments' => Department::query()->orderBy('name')->get(),
            'roles' => $this->roles(),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        abort_unless(Auth::user()?->isOpd(), 403);

        $data = $this->validatedData($request, $user, false);

        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user): RedirectResponse
    {
        abort_unless(Auth::user()?->isOpd(), 403);
        abort_if($user->is(Auth::user()), 422, 'The currently signed-in user cannot be deleted.');

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }

    private function validatedData(Request $request, ?User $user = null, bool $requirePassword = true): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user)],
            'role' => ['required', Rule::in(array_keys($this->roles()))],
            'department_id' => ['nullable', 'exists:departments,id'],
            'password' => [$requirePassword ? 'required' : 'nullable', 'string', 'min:6'],
        ]);
    }

    private function roles(): array
    {
        return [
            'opd' => 'Organization and People Development',
            'department' => 'Department User',
        ];
    }
}
