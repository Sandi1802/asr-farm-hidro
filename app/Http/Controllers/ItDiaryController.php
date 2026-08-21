<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ItDiaryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        if (auth()->user()?->role_agri !== 'it_admin') {
            abort(403, 'Unauthorized access.');
        }

        $logs = \App\Models\LoginLog::with('user')->latest()->paginate(50);
        return view('it.diary', compact('logs'));
    }

    public function deleteAll()
    {
        if (auth()->user()?->role_agri !== 'it_admin') {
            abort(403, 'Unauthorized access.');
        }

        \App\Models\LoginLog::truncate();

        return redirect()->back()->with('success', 'Semua log aktivitas berhasil dihapus.');
    }
}
