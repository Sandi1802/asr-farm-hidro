import re

with open('resources/views/konvensional/kebun/tanam-form.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

# Replace the AJAX for zona
zona_ajax = r"\$.get\(`/konvensional/api/zona\?lahan_id=\$\{lahanId\}`.*?\}\);"
zona_local = """let selectedLahan = lahanData.find(l => l.id == lahanId);
        if(selectedLahan && selectedLahan.zona) {
            selectedLahan.zona.forEach(item => {
                $zona.append(`<option value="${item.id}">${item.nama}</option>`);
            });
        }"""
content = re.sub(zona_ajax, zona_local, content, flags=re.DOTALL)

# Replace the AJAX for pola
pola_ajax = r"\$.get\(`/konvensional/api/pola\?zona_id=\$\{zonaId\}`.*?\}\);"
pola_local = """let lahanId = $lahan.val();
        let selectedLahan = lahanData.find(l => l.id == lahanId);
        let selectedZona = selectedLahan ? selectedLahan.zona.find(z => z.id == zonaId) : null;
        if(selectedZona && selectedZona.pola) {
            selectedZona.pola.forEach(item => {
                $pola.append(`<option value="${item.id}">${item.nama}</option>`);
            });
        }"""
content = re.sub(pola_ajax, pola_local, content, flags=re.DOTALL)

# Replace the bedeng AJAX URL
content = content.replace('/konvensional/api/bedeng?pola_id=', '/konvensional/kebun/tanam/get-bedeng?pola_id=')

# Add lahanData to top of script
content = content.replace('const $lahan = $(\'#lahan_id\');', 'const lahanData = @json($lahanList);\n    const $lahan = $(\'#lahan_id\');')

with open('resources/views/konvensional/kebun/tanam-form.blade.php', 'w', encoding='utf-8') as f:
    f.write(content)

