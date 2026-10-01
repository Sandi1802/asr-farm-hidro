<?php
$f = 'D:\ASR GROUP\ASR-APPS\asr_green_mobile\lib\api_service.dart';
$c = file_get_contents($f);

// Add getKonvenMasterTanaman if not exists
if (!str_contains($c, 'getKonvenMasterTanaman')) {
    $c = preg_replace(
        '/static Future<Map<String, dynamic>> getKonvenLahan/',
        "static Future<Map<String, dynamic>> getKonvenMasterTanaman() => _get('/konvensional/master-tanaman');\n  static Future<Map<String, dynamic>> getKonvenLahan",
        $c
    );
}

file_put_contents($f, $c);
echo "Added getKonvenMasterTanaman.\n";
