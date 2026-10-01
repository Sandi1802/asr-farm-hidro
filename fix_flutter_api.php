<?php
$f = 'D:\ASR GROUP\ASR-APPS\asr_green_mobile\lib\api_service.dart';
$c = file_get_contents($f);

// Fix tanamKonven
$c = str_replace(
    "final body = <String, dynamic>{'plant1': plant1, 'date': date};",
    "final body = <String, dynamic>{'plant_name_1': plant1, 'planted_at': date};",
    $c
);
$c = str_replace(
    "if (plant2 != null && plant2.isNotEmpty) body['plant2'] = plant2;",
    "if (plant2 != null && plant2.isNotEmpty) body['plant_name_2'] = plant2;",
    $c
);

// Fix panenKonven
$c = str_replace(
    "{'plant': plant}",
    "{'plant_name': plant}",
    $c
);

// Fix perawatanKonven
$c = str_replace(
    "'bahan': bahan,",
    "'nama_bahan': bahan,",
    $c
);
$c = str_replace(
    "'date': date,",
    "'tanggal': date,",
    $c
);

file_put_contents($f, $c);
echo "Flutter api_service.dart updated.\n";
