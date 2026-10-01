<?php
$f = 'app/Http/Controllers/Api/KonvensionalApiController.php';
$c = file_get_contents($f);

if (!str_contains($c, 'public function masterTanaman')) {
    $method = <<<EOF

    public function masterTanaman()
    {
        \$master = \App\Models\KonvenTanaman::orderBy('nama')->get();
        return response()->json([
            'success' => true,
            'data' => \$master
        ]);
    }
EOF;
    
    // insert before closing brace of the class
    $c = preg_replace('/}\s*$/', $method . "\n}", $c);
    file_put_contents($f, $c);
}
echo "Added masterTanaman method.\n";
