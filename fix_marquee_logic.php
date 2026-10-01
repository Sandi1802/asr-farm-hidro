<?php
$f = 'resources/views/layouts/app.blade.php';
$c = file_get_contents($f);

// Find the marquee PHP block
$oldPhp = <<<'EOF'
                    @php
                        $marqueeText = \App\Models\Setting::where('key', 'marquee_text')->value('value') ?? 'Selamat Datang! Pantau perkembangbiakan, produksi, dan operasional ASR FARM dengan mudah di sini.';
                    @endphp
EOF;

$newPhp = <<<'EOF'
                    @php
                        $customText = \App\Models\Setting::where('key', 'marquee_text')->value('value') ?? 'Pantau perkembangbiakan, produksi, dan operasional ASR FARM dengan mudah di sini.';
                        $userName = Auth::user()->name ?? 'Super Admin';
                        $marqueeText = "Selamat Datang, {$userName}! {$customText}";
                    @endphp
EOF;

$c = str_replace($oldPhp, $newPhp, $c);
file_put_contents($f, $c);

// We should also run a quick DB update in a script to strip the prefix
echo "App.blade updated.\n";
