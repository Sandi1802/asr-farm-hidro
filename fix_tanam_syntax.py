import re

with open('resources/views/konvensional/kebun/tanam-form.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

bad_zona = """        if(lahanId) {
            let selectedLahan = lahanData.find(l => l.id == lahanId);
        if(selectedLahan && selectedLahan.zona) {
            selectedLahan.zona.forEach(item => {
                $zona.append(`<option value="${item.id}">${item.nama}</option>`);
            });
        }
            });
        }"""

good_zona = """        if(lahanId) {
            let selectedLahan = lahanData.find(l => l.id == lahanId);
            if(selectedLahan && selectedLahan.zona) {
                selectedLahan.zona.forEach(item => {
                    $zona.append(`<option value="${item.id}">${item.nama}</option>`);
                });
            }
        }"""

content = content.replace(bad_zona, good_zona)

bad_pola = """        if(zonaId) {
            let lahanId = $lahan.val();
        let selectedLahan = lahanData.find(l => l.id == lahanId);
        let selectedZona = selectedLahan ? selectedLahan.zona.find(z => z.id == zonaId) : null;
        if(selectedZona && selectedZona.pola) {
            selectedZona.pola.forEach(item => {
                $pola.append(`<option value="${item.id}">${item.nama}</option>`);
            });
        }
            });
        }"""

good_pola = """        if(zonaId) {
            let lahanId = $lahan.val();
            let selectedLahan = lahanData.find(l => l.id == lahanId);
            let selectedZona = selectedLahan ? selectedLahan.zona.find(z => z.id == zonaId) : null;
            if(selectedZona && selectedZona.pola) {
                selectedZona.pola.forEach(item => {
                    $pola.append(`<option value="${item.id}">${item.nama}</option>`);
                });
            }
        }"""

content = content.replace(bad_pola, good_pola)

with open('resources/views/konvensional/kebun/tanam-form.blade.php', 'w', encoding='utf-8') as f:
    f.write(content)

