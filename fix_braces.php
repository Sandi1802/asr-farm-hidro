<?php
$f = 'app/Http/Controllers/HydroponicController.php';
$c = file_get_contents($f);
$c = str_replace("    }\n    }\n\n    public function mark", "    }\n\n    public function mark", $c);
$c = str_replace("    }\n    }\n\n    private function build", "    }\n\n    private function build", $c);
file_put_contents($f, $c);
echo "Fixed braces.\n";
