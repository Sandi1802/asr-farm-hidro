<?php
$f = 'resources/views/layouts/app.blade.php';
$c = file_get_contents($f);

$old = "var t = $(this).DataTable({\n                        dom:";
$new = "if ($.fn.DataTable.isDataTable(this)) {\n                        $(this).DataTable().destroy();\n                    }\n\n                    var t = $(this).DataTable({\n                        destroy: true,\n                        dom:";

$c = str_replace($old, $new, $c);
file_put_contents($f, $c);
echo "app.blade.php updated.\n";
