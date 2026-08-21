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
            'name'      => 'required|string|max:255',
            'username'  => 'required|string|max:255|unique:users,username',
            'email'     => 'required|email|unique:users,email',
            'password'  => 'required|min:6',
            'role_agri' => 'required|in:it_admin,atasan,produksi,produksi_gh,produksi_konven,keuangan,pemasaran,packing',
        ]);

        User::create([
            'name'      => $request->name,
            'username'  => $request->username,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'role'      => $request->role_agri === 'it_admin' ? 'super_admin' : 'viewer',
            'role_agri' => $request->role_agri,
        ]);

        return redirect()->route('hydroponics.users')->with('success', 'Pengguna berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $this->checkSuperAdmin();

        $user = User::findOrFail($id);

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'name'      => 'required|string|max:255',
            'username'  => 'required|string|max:255|unique:users,username,' . $user->id,
            'email'     => 'required|email|unique:users,email,' . $user->id,
            'role_agri' => 'required|in:it_admin,atasan,produksi,produksi_gh,produksi_konven,keuangan,pemasaran,packing',
        ]);

        if ($validator->fails()) {
            return redirect()->route('hydroponics.users')->withErrors($validator)->withInput();
        }

        $user->name = $request->name;
        $user->username = $request->username;
        $user->email = $request->email;
        $user->role = $request->role_agri === 'it_admin' ? 'super_admin' : 'viewer';
        $user->role_agri = $request->role_agri;

        if ($request->filled('password')) {
            $passValidator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'password' => 'min:6',
            ]);
            
            if ($passValidator->fails()) {
                return redirect()->route('hydroponics.users')->withErrors($passValidator)->withInput();
            }
            
            $user->password = \Illuminate\Support\Facades\Hash::make($request->password);
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
