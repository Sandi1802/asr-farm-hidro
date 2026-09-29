import re

with open('app/Http/Controllers/KonvenKebunController.php', 'r', encoding='utf-8') as f:
    content = f.read()

content = content.replace("view('konvensional.kebun.panen', compact('aktif'))", "view('konvensional.kebun.panen', ['tanamAktif' => $aktif])")
content = content.replace("view('konvensional.kebun.riwayat-bedeng', compact('bedeng', 'riwayats'))", "view('konvensional.kebun.riwayat-bedeng', ['bedeng' => $bedeng, 'riwayat' => $riwayats])")
content = content.replace("view('konvensional.kebun.master-tanaman', compact('tanaman'))", "view('konvensional.kebun.master-tanaman', ['tanamanList' => $tanaman])")

with open('app/Http/Controllers/KonvenKebunController.php', 'w', encoding='utf-8') as f:
    f.write(content)
