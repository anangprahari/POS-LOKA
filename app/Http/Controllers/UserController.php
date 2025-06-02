<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Rap2hpoutre\FastExcel\FastExcel;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Common\Entity\Style\Color;

class UserController extends Controller
{
    public function index()
    {
        $users = User::latest()->paginate(10);

        // Calculate user statistics for dashboard
        $stats = [
            'total' => User::count(),
            'admins' => User::where('role', 'admin')->count(),
            'regular_users' => User::where('role', 'user')->count(),
            'new_this_month' => User::where('created_at', '>=', now()->subDays(30))->count(),
        ];

        return view('users.index', compact('users', 'stats'));
    }

    /**
     * Export users to Excel file with elegant modern styling
     * 
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function export()
    {
        $users = User::all();

        // Define the column headers as key => value pairs
        $headers = [
            'id' => 'ID',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'full_name' => 'Full Name',
            'email' => 'Email',
            'role' => 'Role',
            'created_at' => 'Joined Date',
            'updated_at' => 'Last Updated'
        ];

        // Format the data
        $formattedUsers = $users->map(function ($user) use ($headers) {
            return [
                $headers['id'] => $user->id,
                $headers['first_name'] => $user->first_name,
                $headers['last_name'] => $user->last_name,
                $headers['full_name'] => $user->first_name . ' ' . $user->last_name,
                $headers['email'] => $user->email,
                $headers['role'] => ucfirst($user->role),
                $headers['created_at'] => $user->created_at->format('d M Y H:i'),
                $headers['updated_at'] => $user->updated_at->format('d M Y H:i'),
            ];
        });

        // Modern header style
        $headerStyle = (new Style())
            ->setFontBold()
            ->setFontSize(13)
            ->setFontColor(Color::WHITE)
            ->setBackgroundColor('728FCE'); // Dark blue-gray color

        return (new FastExcel($formattedUsers))
            ->headerStyle($headerStyle)
            ->download('users_report.xlsx');
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,user',
            'avatar' => 'nullable|image|max:2048',
        ]);

        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('users', 'public');
        }

        User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'avatar' => $avatarPath,
        ]);

        return redirect()->route('users.index')
            ->with('success', __('Success, you user have been created.'));
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:admin,user',
            'avatar' => 'nullable|image|max:2048',
        ]);

        $data = [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'role' => $request->role,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('users', 'public');
        }

        $user->update($data);

        return redirect()->route('users.index')
            ->with('success', __('Success, your user have been updated.'));
    }

    public function destroy(User $user)
    {
        // Prevent self-deletion
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                ->with('error', __('You cannot delete your own account.'));
        }

        // Delete avatar if exists
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->delete();

        // If it's an AJAX request, return JSON response
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('User deleted successfully.')
            ]);
        }

        return redirect()->route('users.index')
            ->with('success', __('User deleted successfully.'));
    }

    public function show(User $user)
    {
        // Always return JSON for show method if it's AJAX request
        if (request()->ajax() || request()->wantsJson() || request()->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'role' => $user->role,
                'avatar' => $user->avatar,
                'email_verified_at' => $user->email_verified_at,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at
            ]);
        }

        return view('users.show', compact('user'));
    }
}
