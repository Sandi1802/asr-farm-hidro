<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\Testimonial;

class PageController extends Controller
{
    private function getSettings()
    {
        return Setting::pluck('value', 'key')->toArray();
    }

    public function about()
    {
        $settings = $this->getSettings();
        return view('about', compact('settings'));
    }

    public function contact()
    {
        $settings = $this->getSettings();
        return view('contact', compact('settings'));
    }

    public function submitContact(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string'
        ]);

        \App\Models\Message::create([
            'name' => $request->name,
            'email' => $request->email,
            'subject' => $request->subject,
            'message' => $request->message
        ]);

        return back()->with('success', 'Terima kasih! Pesan Anda telah kami terima dan akan segera kami balas.');
    }

    public function testimonials()
    {
        $settings = $this->getSettings();
        $testimonials = Testimonial::latest()->get();
        return view('testimonials', compact('settings', 'testimonials'));
    }
}
