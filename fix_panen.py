import re

with open('app/Http/Controllers/KonvenKebunController.php', 'r', encoding='utf-8') as f:
    content = f.read()

old_panen = re.search(r"return \[\s*'tipe' => 'panen',.*?\];", content, re.DOTALL)
if old_panen:
    new_panen = """return (object)[
                    'jenis' => 'panen',
                    'tanggal' => $item->tanggal_panen,
                    'tanaman' => $item->tanam->tanaman->nama,
                    'bedeng' => $item->tanam->bedeng->kode,
                    'hasil' => $item->jumlah_hasil,
                    'satuan' => $item->satuan,
                    'created_at' => $item->created_at
                ];"""
    content = content.replace(old_panen.group(0), new_panen)
    with open('app/Http/Controllers/KonvenKebunController.php', 'w', encoding='utf-8') as f:
        f.write(content)
    print('Panen map fixed')
