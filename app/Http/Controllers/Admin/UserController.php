<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(): Factory|View
    {
        $users = User::orderBy('name')->get();
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create(): Factory|View
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'is_admin' => ['sometimes', 'boolean'],
        ]);

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'is_admin' => $data['is_admin'] ?? false,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User created.');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(Request $request, User $user): RedirectResponse
    {
        $current = $request->user();

        if ($current->id === $user->id) {
            return redirect()->back()->withErrors(['user' => 'You cannot delete your own account from the admin panel.']);
        }

        if ($user->is_admin) {
            $adminCount = User::where('is_admin', true)->count();
            if ($adminCount <= 1) {
                return redirect()->back()->withErrors(['user' => 'Cannot delete the last admin user.']);
            }
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted.');
    }

    /**
     * Toggle admin status on a user (promote/demote).
     */
    public function toggleAdmin(Request $request, User $user): RedirectResponse
    {
        $current = $request->user();

        if ($current->id === $user->id) {
            return redirect()->back()->withErrors(['user' => 'You cannot change your own admin status.']);
        }

        if ($user->is_admin) {
            // demote
            $adminCount = User::where('is_admin', true)->count();
            if ($adminCount <= 1) {
                return redirect()->back()->withErrors(['user' => 'Cannot demote the last admin user.']);
            }
            $user->is_admin = false;
        } else {
            // promote
            $user->is_admin = true;
        }

        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'User admin status updated.');
    }
}
