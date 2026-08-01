<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    private function checkSuperAdmin()
    {
        abort_if(auth()->user()->role !== 'super_admin', 403, 'Akses ditolak. Anda bukan Super Admin.');
    }

    public function index()
    {
        $this->checkSuperAdmin();
        $users = User::orderBy('name')->get();
        return view('hydroponics.users.index', compact('users'));
    }

    public function store(Request $request)
    {
        $this->checkSuperAdmin();

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role'     => 'required|in:super_admin,staff',
        ]);

        $role_agri = $request->role === 'super_admin' ? 'admin' : 'pegawai';

        User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'role'      => $request->role === 'staff' ? 'viewer' : 'super_admin',
            'role_agri' => $role_agri,
        ]);

        return redirect()->route('hydroponics.users')->with('success', 'Pengguna berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $this->checkSuperAdmin();

        $user = User::findOrFail($id);

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'role'     => 'required|in:super_admin,staff',
        ]);

        $role_agri = $request->role === 'super_admin' ? 'admin' : 'pegawai';

        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role === 'staff' ? 'viewer' : 'super_admin';
        $user->role_agri = $role_agri;

        if ($request->filled('password')) {
            $request->validate([
                'password' => 'min:6',
            ]);
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('hydroponics.users')->with('success', 'Pengguna berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $this->checkSuperAdmin();
        
        $user = User::findOrFail($id);
        
        if ($user->id === auth()->id()) {
            return redirect()->route('hydroponics.users')->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user->delete();

        return redirect()->route('hydroponics.users')->with('success', 'Pengguna berhasil dihapus!');
    }
}
