import re

with open('app/Http/Controllers/MaintenanceLogController.php', 'r', encoding='utf-8') as f:
    content = f.read()

old_update = """    public function update(Request $request, $id)
    {
        $request->validate([
            'notes' => 'required|string|max:1000'
        ]);

        $log = MaintenanceLog::findOrFail($id);
        $log->update([
            'notes' => $request->notes
        ]);

        return back()->with("success", "Log pemeliharaan berhasil diupdate.");
    }"""

new_update = """    public function update(Request $request, $id)
    {
        $request->validate([
            'notes' => 'required|string|max:1000'
        ]);

        $log = MaintenanceLog::findOrFail($id);
        
        $details = $log->details ? json_decode($log->details, true) : [];
        
        // Coba ekstrak jumlah dari teks catatan (misal "Panen 150 tanaman..." atau "Rusak 50 tanaman...")
        if (preg_match('/(?:Panen|Rusak)\s+(\d+)\s+tanaman/i', $request->notes, $matches)) {
            $details['jumlah'] = (int) $matches[1];
        } elseif (preg_match('/(\d+)/', $request->notes, $matches)) {
            // Fallback: ambil angka pertama yang ditemukan
            $details['jumlah'] = (int) $matches[1];
        }

        $log->update([
            'notes' => $request->notes,
            'details' => json_encode($details)
        ]);

        return back()->with("success", "Log pemeliharaan berhasil diupdate.");
    }"""

content = content.replace(old_update, new_update)

with open('app/Http/Controllers/MaintenanceLogController.php', 'w', encoding='utf-8') as f:
    f.write(content)

print("MaintenanceLogController updated.")
