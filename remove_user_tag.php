<?php
$f = 'resources/views/layouts/app.blade.php';
$c = file_get_contents($f);

// Remove the replacement logic
$oldPhp = <<<'EOF'
                    @php
                        $rawMarquee = \App\Models\Setting::where('key', 'marquee_text')->value('value') ?? 'Selamat Datang, {user}! Pantau perkembangbiakan, produksi, dan operasional ASR FARM dengan mudah di sini.';
                        $userName = Auth::user()->name ?? 'Super Admin';
                        $marqueeText = str_replace('{user}', $userName, $rawMarquee);
                    @endphp
EOF;

$newPhp = <<<'EOF'
                    @php
                        $marqueeText = \App\Models\Setting::where('key', 'marquee_text')->value('value') ?? 'Selamat Datang! Pantau perkembangbiakan, produksi, dan operasional ASR FARM dengan mudah di sini.';
                    @endphp
EOF;

$c = str_replace($oldPhp, $newPhp, $c);
file_put_contents($f, $c);

// Remove the hint in settings view
$sf = 'resources/views/master-data/settings/index.blade.php';
$sc = file_get_contents($sf);
$sc = preg_replace('/<small.*?Gunakan <code>\{user\}<\/code>.*?<\/small>/s', '', $sc);
file_put_contents($sf, $sc);

echo "Removed {user} replacement logic.\n";
