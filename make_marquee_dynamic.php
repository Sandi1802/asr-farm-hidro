<?php
$f = 'resources/views/layouts/app.blade.php';
$c = file_get_contents($f);

$oldHtml = <<<'EOF'
<span style="font-weight: 500; color: var(--text-main); font-size: 0.9rem;">Selamat Datang, {{ Auth::user()->name ?? 'Super Admin' }}! Pantau perkembangbiakan, produksi, dan operasional ASR FARM dengan mudah di sini.</span>
EOF;

$newHtml = <<<'EOF'
@php
    $rawMarquee = \App\Models\Setting::where('key', 'marquee_text')->value('value') ?? 'Selamat Datang, {user}! Pantau perkembangbiakan, produksi, dan operasional ASR FARM dengan mudah di sini.';
    $userName = Auth::user()->name ?? 'Super Admin';
    $marqueeText = str_replace('{user}', $userName, $rawMarquee);
@endphp
<span style="font-weight: 500; color: var(--text-main); font-size: 0.9rem;">{{ $marqueeText }}</span>
EOF;

// Since there are two spans now, I will just str_replace the first one with the PHP block, and then regex replace both spans to use $marqueeText.
$c = preg_replace('/<span style="font-weight: 500; color: var\(--text-main\); font-size: 0\.9rem;">Selamat Datang.*?<\/span>/', '<span style="font-weight: 500; color: var(--text-main); font-size: 0.9rem;">{{ $marqueeText }}</span>', $c);

// Inject the PHP block right before the first marquee item
$phpBlock = <<<'EOF'
                    <div class="global-marquee-content">
                        @php
                            $rawMarquee = \App\Models\Setting::where('key', 'marquee_text')->value('value') ?? 'Selamat Datang, {user}! Pantau perkembangbiakan, produksi, dan operasional ASR FARM dengan mudah di sini.';
                            $userName = Auth::user()->name ?? 'Super Admin';
                            $marqueeText = str_replace('{user}', $userName, $rawMarquee);
                        @endphp
                        <div class="global-marquee-item">
EOF;
$c = str_replace('<div class="global-marquee-content">
                        <div class="global-marquee-item">', $phpBlock, $c);

file_put_contents($f, $c);
echo "Marquee dynamic text updated.\n";
