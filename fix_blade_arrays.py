import re

views = [
    'resources/views/konvensional/kebun/dashboard.blade.php',
    'resources/views/konvensional/kebun/tanam-form.blade.php',
    'resources/views/konvensional/kebun/riwayat-bedeng.blade.php',
    'resources/views/konvensional/kebun/panen.blade.php'
]

for view in views:
    with open(view, 'r', encoding='utf-8') as f:
        content = f.read()
    
    # regex to replace $bedeng['luas_m2'] with $bedeng->luas_m2
    content = re.sub(r"\$([a-zA-Z0-9_]+)\['([a-zA-Z0-9_]+)'\]", r"$\1->\2", content)
    
    # second pass for nested like $tanam->tanaman['nama']
    content = re.sub(r"->([a-zA-Z0-9_]+)\['([a-zA-Z0-9_]+)'\]", r"->\1->\2", content)
    
    # third pass for deep nested like $bedeng->tanamAktif->tanaman['nama']
    content = re.sub(r"->([a-zA-Z0-9_]+)\['([a-zA-Z0-9_]+)'\]", r"->\1->\2", content)
    
    with open(view, 'w', encoding='utf-8') as f:
        f.write(content)

print("Fixed array access.")
