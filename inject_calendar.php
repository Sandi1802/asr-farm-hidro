<?php
$hydroponicPath = resource_path('views/hydroponics/dashboard.blade.php');
$konvensionalPath = resource_path('views/konvensional/dashboard.blade.php');

$hydroLines = file($hydroponicPath);
$konvContent = file_get_contents($konvensionalPath);

// Extract HTML lines 121 to 195 (array indices 121 to 195)
// Let's just slice it
$calendarHtmlLines = array_slice($hydroLines, 121, 75);
$calendarHtml = implode("", $calendarHtmlLines);

// Extract JS lines 544 to 929
$calendarJsLines = array_slice($hydroLines, 544, 386);
$calendarJs = implode("", $calendarJsLines);

// Make some adjustments for conventional (remove 'Tambah Kegiatan' modal button)
$calendarHtml = preg_replace('/<button onclick="document\.getElementById\(\'addEventModal\'\).*?<\/button>/s', '', $calendarHtml);

// Remove Kegiatan custom filter from HTML
$calendarHtml = preg_replace('/<span class="cal-legend-item" onclick="toggleCalFilter\(\'custom\', this\)".*?Kegiatan<\/span>/s', '', $calendarHtml);

// Adjust JS variables
$calendarJs = str_replace('const rotationData  = {!! $rotationJson !!};', '', $calendarJs);
$calendarJs = str_replace('const plantStageData= {!! $plantStageJson !!};', '', $calendarJs);
$calendarJs = str_replace('custom: true', 'custom: false', $calendarJs);

// Inject HTML before the graphics section
$konvContent = str_replace('    {{-- GRAFIK SECTION (2 KOLOM) --}}', $calendarHtml . "\n    {{-- GRAFIK SECTION (2 KOLOM) --}}", $konvContent);

// Inject JS before @endsection
$konvContent = str_replace('</script>', $calendarJs . "\n    if (typeof initCalendar === 'function') initCalendar();\n</script>", $konvContent);

file_put_contents($konvensionalPath, $konvContent);
echo "Injected successfully\n";
