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

    public function testimonials()
    {
        $settings = $this->getSettings();
        $testimonials = Testimonial::latest()->get();
        return view('testimonials', compact('settings', 'testimonials'));
    }
}
