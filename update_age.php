<?php
// Script to append updateAge to RackApiController
$file = "D:\ASR GROUP\ASR-APPS\ASR_GREEN_WEB\app\Http\Controllers\Api\RackApiController.php";
$content = file_get_contents($file);
$method = "
    public function updateAge(Request \$request, \$id)
    {
        try {
            \$request->validate([
                \"plant_name\" => \"required|string\",
                \"usia_hari\" => \"required|integer|min:0\"
            ]);

            \$rack = Rack::with(\"rows.holes\")->findOrFail(\$id);
            \$plantName = \$request->plant_name;
            \$usiaHari = \$request->usia_hari;
            \$newPlantedAt = \Carbon\Carbon::now()->subDays(\$usiaHari);

            \$updatedCount = 0;
            foreach (\$rack->rows as \$row) {
                foreach (\$row->holes as \$hole) {
                    if (\$hole->status === \"ditanam\" && \$hole->plant_name === \$plantName) {
                        \$hole->planted_at = \$newPlantedAt;
                        \$hole->save();
                        \$updatedCount++;
                    }
                }
            }

            return response()->json([
                \"success\" => true,
                \"message\" => \"Berhasil memperbarui usia \$updatedCount tanaman \$plantName menjadi \$usiaHari hari.\",
                \"data\" => [\"updated_count\" => \$updatedCount]
            ]);
        } catch (\Exception \$e) {
            return response()->json([\"success\" => false, \"message\" => \$e->getMessage()], 500);
        }
    }
}
";
$content = preg_replace("/\}\s*$/", $method, $content);
file_put_contents($file, $content);
echo "Done";

