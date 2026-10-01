<?php
$f = 'resources/views/layouts/app.blade.php';
$c = file_get_contents($f);

$c = preg_replace(
    "/var t = \\$\(this\)\.DataTable\(\{\s*dom:/m",
    "if ($.fn.DataTable.isDataTable(this)) {\n                        $(this).DataTable().destroy();\n                    }\n\n                    var t = $(this).DataTable({\n                        destroy: true,\n                        dom:",
    $c
);

file_put_contents($f, $c);
echo "app.blade.php updated.\n";
