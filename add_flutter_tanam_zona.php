<?php
$f = 'D:\ASR GROUP\ASR-APPS\asr_green_mobile\lib\api_service.dart';
$c = file_get_contents($f);

if (!str_contains($c, 'tanamZonaKonven')) {
    $c = preg_replace(
        '/static Future<Map<String, dynamic>> panenKonven/',
        "static Future<Map<String, dynamic>> tanamZonaKonven(int id, String plant1, String? plant2, String date, int jumlah) {\n    final body = <String, dynamic>{'plant_name_1': plant1, 'planted_at': date, 'jumlah': jumlah};\n    if (plant2 != null && plant2.isNotEmpty) body['plant_name_2'] = plant2;\n    return _post('/konvensional/zona/\$id/tanam', body);\n  }\n\n  static Future<Map<String, dynamic>> panenKonven",
        $c
    );
    file_put_contents($f, $c);
}
echo "Added tanamZonaKonven to Flutter.\n";
