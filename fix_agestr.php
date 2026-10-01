<?php
$f = 'app/Http/Controllers/HydroponicController.php';
$c = file_get_contents($f);
$oldStr = "\$ageStr = (\$minAge == \$maxAge) ? \$minAge . ' Hari' : \$minAge . ' - ' . \$maxAge . ' Hari';";
$newStr = "\$ageStr = \$maxAge . ' Hari';";
$c = str_replace($oldStr, $newStr, $c);
file_put_contents($f, $c);
echo "Fixed age string to be a single number.\n";
