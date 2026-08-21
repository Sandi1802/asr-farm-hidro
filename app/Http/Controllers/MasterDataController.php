<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vessel;
use App\Models\Stakeholder;
use App\Models\ProjectScope;
use App\Models\Employee;

class MasterDataController extends Controller
{
    // VESSELS
    public function vessels()
    {
        $vessels = Vessel::all();
        return view('master-data.vessels', compact('vessels'));
    }

    public function storeVessel(Request $request)
    {
        $request->validate([
            'name'     => 'required',
            'hull_no'  => 'required',
            'location' => 'required',
            'length'   => 'required|numeric',
            'year'     => 'required|numeric',
            'type'     => 'required',
            'status'   => 'required',
        ]);
        Vessel::create($request->all());
        return back()->with('success', 'Vessel added successfully.');
    }

    public function deleteVessel($id)
    {
        Vessel::findOrFail($id)->delete();
        return back()->with('success', 'Vessel deleted successfully.');
    }

    // STAKEHOLDERS
    public function stakeholders()
    {
        $stakeholders = Stakeholder::all();
        return view('master-data.stakeholders', compact('stakeholders'));
    }

    public function storeStakeholder(Request $request)
    {
        $request->validate([
            'name'           => 'required',
            'category'       => 'required',
            'role'           => 'required',
            'contact_person' => 'required',
            'country'        => 'required',
        ]);
        Stakeholder::create($request->all());
        return back()->with('success', 'Stakeholder added successfully.');
    }

    public function deleteStakeholder($id)
    {
        Stakeholder::findOrFail($id)->delete();
        return back()->with('success', 'Stakeholder deleted successfully.');
    }

    // SCOPE
    public function scope()
    {
        $scopes = ProjectScope::all();
        return view('master-data.scope', compact('scopes'));
    }

    public function storeScope(Request $request)
    {
        $request->validate([
            'system_name' => 'required',
            'category'    => 'required',
            'principal'   => 'required',
            'status'      => 'required',
            'progress'    => 'required|numeric',
        ]);
        ProjectScope::create($request->all());
        return back()->with('success', 'Project Scope added successfully.');
    }

    public function deleteScope($id)
    {
        ProjectScope::findOrFail($id)->delete();
        return back()->with('success', 'Project Scope deleted successfully.');
    }

    // EMPLOYEES
    public function employees()
    {
        $employees = Employee::all();
        return view('master-data.employees', compact('employees'));
    }

    public function storeEmployee(Request $request)
    {
        $request->validate([
            'nip'        => 'required|unique:employees,nip',
            'name'       => 'required',
            'position'   => 'required',
            'department' => 'required',
            'status'     => 'required',
            'username'   => 'required|unique:users,username',
            'role_agri'  => 'required',
            'password'   => 'required|min:6',
            'avatar'     => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
        }

        // 1. Buat User Account
        $email = strtolower(str_replace(' ', '', $request->username)) . '@asrfarm.local';

        $user = \App\Models\User::create([
            'name'      => $request->name,
            'nip'       => $request->nip,
            'username'  => $request->username,
            'email'     => $email,
            'password'  => \Illuminate\Support\Facades\Hash::make($request->password),
            'role'      => $request->role_agri === 'it_admin' ? 'super_admin' : 'viewer',
            'role_agri' => $request->role_agri,
            'avatar'    => $avatarPath,
        ]);

        // 2. Buat Employee Record
        Employee::create([
            'nip'        => $request->nip,
            'name'       => $request->name,
            'position'   => $request->position,
            'department' => $request->department,
            'email'      => $email,
            'phone'      => $request->phone,
            'status'     => $request->status,
            'avatar'     => $avatarPath,
        ]);

        return back()->with('success', 'Pegawai & Akun berhasil ditambahkan.');
    }

    public function deleteEmployee($id)
    {
        Employee::findOrFail($id)->delete();
        return back()->with('success', 'Pegawai berhasil dihapus.');
    }
}
