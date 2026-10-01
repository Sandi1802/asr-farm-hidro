<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;

class SettingController extends Controller
{
    public function index()
    {
        $marqueeText = Setting::where('key', 'marquee_text')->value('value') 
                       ?? 'Pantau perkembangbiakan, produksi, dan operasional ASR FARM dengan mudah di sini.';
                       
        return view('master-data.settings.index', compact('marqueeText'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'marquee_text' => 'required|string|max:1000',
        ]);

        Setting::updateOrCreate(
            ['key' => 'marquee_text'],
            ['value' => $request->marquee_text]
        );

        return back()->with('success', 'Pengaturan teks berhasil disimpan.');
    }
}
