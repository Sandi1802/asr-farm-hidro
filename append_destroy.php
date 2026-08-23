<?php
$file = "D:/ASR GROUP/ASR-APPS/ASR_GREEN_WEB/app/Http/Controllers/MaintenanceLogController.php";
$content = file_get_contents($file);
$method = "
    public function destroyAll(Request \$request)
    {
        \$type = \$request->query(\"type\");
        if (\$type == \"panen\") {
            MaintenanceLog::where(\"action_type\", \"panen\")->delete();
            return back()->with(\"success\", \"Seluruh Log Panen berhasil dihapus.\");
        } elseif (\$type == \"tanam\") {
            MaintenanceLog::where(\"action_type\", \"tanam\")->delete();
            return back()->with(\"success\", \"Seluruh Log Tanam berhasil dihapus.\");
        }
        
        MaintenanceLog::truncate();
        return back()->with(\"success\", \"Seluruh Log berhasil dihapus.\");
    }
}
";
$content = preg_replace("/\}\s*$/", $method, $content);
file_put_contents($file, $content);
echo "Method added";

