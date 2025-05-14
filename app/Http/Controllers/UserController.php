<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;


class UserController extends Controller
{
    public function index()
    {
        $userRole = Auth::user()->role;
        $user = User::paginate(5);
        return view('user.index', compact('user', 'userRole'));
    }

    public function search(Request $request)
    {
        $userRole = Auth::user()->role;
        $search = $request->input('search');
        // Mengambil data pengguna berdasarkan pencarian nama, email, atau role
        $user = User::when($search, function ($query) use ($search) {
            return $query->where(function ($query) use ($search) {
                $query->where('name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%")
                    ->orWhere('role', 'like', "%$search%");
            });
        })->paginate(5);

        return view('user.index', compact('user', 'userRole'));
    }

    public function resetPassword(User $user)
    {
        $user->password = Hash::make('11111111');
        $user->save();

        return redirect()->route('user.index')->with('success', 'Password berhasil direset ke default.');
    }

    public function update(Request $request, $id)
    {
        $user = User::all();

        // Validasi input
        $request->validate([
            'role' => 'required|string|max:255',
        ]);

        // Temukan mahasiswa dan user yang akan diupdate
        $user = User::findOrFail($id);

        // Update data user
        $user->update([
            'role' => $request->role,
        ]);

        return redirect()->route('user.index')->with('success', 'Data user berhasil diupdate.');
    }

    public function dropdownSearch(Request $request)
    {
        $userRole = Auth::user()->role;
        $user = User::all();

        // Ambil nilai dari dropdown
        $role = $request->input('role');

        // Query untuk mencari mahasiswa dengan kondisi yang dipilih
        $user = User::when($role, function ($query) use ($role) {
            return $query->where('role', $role);
        })
            ->paginate(5);

        return view('user.index', compact('user', 'role', 'userRole'));
    }
}
