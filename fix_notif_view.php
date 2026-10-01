<?php
$f = 'resources/views/components/notifications.blade.php';
$c = file_get_contents($f);

// Change title
$c = str_replace('Siap Panen! ({{ $count }} lubang)', 'Siap Panen! ({{ $typeCount }} jenis tanaman)', $c);

// Remove hole count badge
$c = preg_replace('/<span style="background: #ea580c;.*?\{\{ \$plantCount \}\} lubang\s*<\/span>/s', '', $c);

// Remove hole count from text
$c = str_replace(' ({{ $plantCount }})', '', $c);

file_put_contents($f, $c);
echo "Notifications view updated.\n";
