import re

with open('app/Http/Controllers/KonvenKebunController.php', 'r', encoding='utf-8') as f:
    content = f.read()

# Replace tanam map
old_tanam = """                return [
                    'tipe' => 'tanam',
                    'tanggal' => $item->tanggal_tanam,
                    'keterangan' => "Tanam {$item->tanaman->nama} di Bedeng {$item->bedeng->kode} ({$item->jumlah_tanam} populasi)",
                    'created_at' => $item->created_at,
                    'user' => $item->dibuat_oleh
                ];"""
new_tanam = """                return (object)[
                    'jenis' => 'tanam',
                    'tanggal' => $item->tanggal_tanam,
                    'tanaman' => $item->tanaman->nama,
                    'bedeng' => $item->bedeng->kode,
                    'jumlah' => $item->jumlah_tanam,
                    'created_at' => $item->created_at
                ];"""

content = content.replace(old_tanam, new_tanam)

# Replace panen map
old_panen = """                return [
                    'tipe' => 'panen',
                    'tanggal' => $item->tanggal_panen,
                    'keterangan' => "Panen {$item->tanam->tanaman->nama} di Bedeng {$item->tanam->bedeng->kode} ({$item->jumlah_hasil} {$item->satuan})",
                    'created_at' => $item->created_at,
                    'user' => null
                ];"""
new_panen = """                return (object)[
                    'jenis' => 'panen',
                    'tanggal' => $item->tanggal_panen,
                    'tanaman' => $item->tanam->tanaman->nama,
                    'bedeng' => $item->tanam->bedeng->kode,
                    'hasil' => $item->jumlah_hasil,
                    'satuan' => $item->satuan,
                    'created_at' => $item->created_at
                ];"""

content = content.replace(old_panen, new_panen)

with open('app/Http/Controllers/KonvenKebunController.php', 'w', encoding='utf-8') as f:
    f.write(content)

print("Fixed map returns.")
