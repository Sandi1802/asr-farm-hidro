<?php
$f = 'D:/ASR GROUP/ASR-APPS/ASR_GREEN_WEB/app/Http/Controllers/Api/RackApiController.php';
$c = file_get_contents($f);

$find = 'public function updateAge(Request $request, $id)
    {
        try {';
$rep = 'public function updateAge(Request $request, $id)
    {
        \Log::info("updateAge called for rack $id", $request->all());
        try {';
$c = str_replace($find, $rep, $c);

$find2 = 'return response()->json(["success" => false, "message" => $e->getMessage()], 500);';
$rep2 = '\Log::error("updateAge error: " . $e->getMessage() . " \n " . $e->getTraceAsString());
            return response()->json(["success" => false, "message" => $e->getMessage()], 500);';
$c = str_replace($find2, $rep2, $c);

file_put_contents($f, $c);
echo "Done";
