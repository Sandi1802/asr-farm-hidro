import re

with open('app/Http/Controllers/KonvenKebunController.php', 'r', encoding='utf-8') as f:
    content = f.read()

old_tanam = """        return view('konvensional.kebun.tanam-form', compact(
            'lahans', 'tanaman_master', 
            'selected_lahan', 'selected_zona', 'selected_pola',
            'bedengs'
        ));"""

new_tanam = """        return view('konvensional.kebun.tanam-form', [
            'lahanList' => $lahans,
            'tanamanList' => $tanaman_master,
            'selectedPola' => $selected_pola,
            'bedengList' => $bedengs
        ]);"""

content = content.replace(old_tanam, new_tanam)

with open('app/Http/Controllers/KonvenKebunController.php', 'w', encoding='utf-8') as f:
    f.write(content)
