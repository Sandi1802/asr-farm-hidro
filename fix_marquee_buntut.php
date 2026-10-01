<?php
$f = 'resources/views/layouts/app.blade.php';
$c = file_get_contents($f);

// 1. Remove the old marquee CSS
$c = preg_replace('/\.global-marquee-wrapper\s*\{.*?\}\s*\.global-marquee-content\s*\{.*?\}\s*\.global-marquee-content:hover\s*\{.*?\}\s*\.global-marquee-item\s*\{.*?\}\s*@keyframes\s+global-marquee-anim\s*\{.*?\}/s', '', $c);

// 2. Replace the HTML with <marquee>
$oldHtmlRegex = '/<div class="global-marquee-wrapper">.*?<div class="global-marquee-content">.*?@php.*?@endphp.*?<div class="global-marquee-item">.*?<\/div>.*?<div class="global-marquee-item">.*?<\/div>.*?<\/div>.*?<\/div>/s';

$newHtml = <<<'EOF'
                    @php
                        $rawMarquee = \App\Models\Setting::where('key', 'marquee_text')->value('value') ?? 'Selamat Datang, {user}! Pantau perkembangbiakan, produksi, dan operasional ASR FARM dengan mudah di sini.';
                        $userName = Auth::user()->name ?? 'Super Admin';
                        $marqueeText = str_replace('{user}', $userName, $rawMarquee);
                    @endphp
                    <marquee behavior="scroll" direction="left" scrollamount="6" onmouseover="this.stop();" onmouseout="this.start();" style="width: 100%; display: flex; align-items: center;">
                        <div style="display: inline-flex; align-items: center;">
                            <img src="{{ asset('images/logo-asr.png') }}" alt="Logo" style="height: 24px; width: 24px; object-fit: cover; margin-right: 10px; background-color: #ffffff; border-radius: 50%; padding: 1px;">
                            <span style="font-weight: 500; color: var(--text-main); font-size: 0.9rem;">{{ $marqueeText }}</span>
                        </div>
                    </marquee>
EOF;

$c = preg_replace($oldHtmlRegex, $newHtml, $c);

file_put_contents($f, $c);
echo "Reverted to standard marquee.\n";
