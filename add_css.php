<?php
$file = "public/css/app.css";
$content = file_get_contents($file);
$search = "/* Card Variants */";
$replace = "/* Card Variants */\n" . 
".sbc-paprika-red { background: linear-gradient(135deg, #b91c1c 0%, #dc2626 100%); box-shadow: 0 4px 16px rgba(185,28,28,0.35); }\n" .
".sbc-paprika-orange { background: linear-gradient(135deg, #c2410c 0%, #ea580c 100%); box-shadow: 0 4px 16px rgba(194,65,12,0.35); }\n" .
".sbc-paprika-yellow { background: linear-gradient(135deg, #a16207 0%, #ca8a04 100%); box-shadow: 0 4px 16px rgba(161,98,7,0.35); }\n" .
".sbc-paprika-green { background: linear-gradient(135deg, #15803d 0%, #16a34a 100%); box-shadow: 0 4px 16px rgba(21,128,61,0.35); }\n";

$content = str_replace($search, $replace, $content);
file_put_contents($file, $content);

