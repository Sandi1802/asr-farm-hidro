<?php
$f = 'app/Http/Controllers/HydroponicController.php';
$c = file_get_contents($f);
$c = preg_replace("/\}\s*\}\s*public function mark/s", "}\n\n    public function mark", $c);
$c = preg_replace("/\}\s*\}\s*private function build/s", "}\n\n    private function build", $c);
file_put_contents($f, $c);
echo "Fixed braces properly.\n";
