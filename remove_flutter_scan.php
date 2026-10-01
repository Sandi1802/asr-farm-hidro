<?php
$f = 'D:\ASR GROUP\ASR-APPS\asr_green_mobile\lib\screens\konven_dashboard_page.dart';
$c = file_get_contents($f);

// 1. Remove the IconButton qr_code_scanner
$c = preg_replace(
    "/IconButton\(\s*icon:\s*Icon\(Icons\.qr_code_scanner.*?\).*?\}\),/s",
    "",
    $c
);

// 2. Remove the Big Scan Button block
// Let's use string manipulation to remove from "// BIG SCAN BUTTON" down to the end of the InkWell container.
// Or just regex:
$pattern = "/\/\/ BIG SCAN BUTTON.*?InkWell\(.*?child: Container\(.*?padding:.*?decoration:.*?\).*?child: Column\(.*?children: \[.*?Icon\(Icons\.qr_code_scanner_rounded.*?Text\('SCAN QR KONVEN'.*?Text\('Gunakan scanner untuk area konvensional'.*?\],.*?\),.*?\),.*?\),/s";
$c = preg_replace($pattern, "", $c);

// If the regex above is too fragile, let's just do a specific string replace.
// Instead of complex regex, let's find the position.
file_put_contents($f, $c);
echo "Tried removing scan qr button.\n";
