import re

with open('resources/views/konvensional/kebun/tanam-form.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

new_script = """
    const lahanData = @json($lahanList);
    const $lahan = $('#lahan_id');
    const $zona = $('#zona_id');
    const $pola = $('#pola_id');
    const $bedengGrid = $('#bedengGrid');
    const $bedengContainer = $('#bedengContainer');
    const $detailContainer = $('#detailContainer');
    const $tanaman = $('#tanaman_id');
    const $tglTanam = $('#tanggal_tanam');
    const $jmlSama = $('#jumlah_tanam_sama');
    const $modeSama = $('#mode_sama');
    const $modeBeda = $('#mode_beda');

    // Cascading Lahan -> Zona
    $lahan.change(function() {
        let lahanId = $(this).val();
        $zona.html('<option value="">-- Pilih Zona --</option>').prop('disabled', !lahanId);
        $pola.html('<option value="">-- Pilih Pola --</option>').prop('disabled', true);
        $bedengContainer.addClass('d-none');
        $detailContainer.addClass('d-none');
        if(lahanId) {
            let selectedLahan = lahanData.find(l => l.id == lahanId);
            if(selectedLahan && selectedLahan.zona) {
                selectedLahan.zona.forEach(item => {
                    $zona.append(`<option value="${item.id}">${item.nama}</option>`);
                });
            }
        }
    });

    // Cascading Zona -> Pola
    $zona.change(function() {
        let zonaId = $(this).val();
        $pola.html('<option value="">-- Pilih Pola --</option>').prop('disabled', !zonaId);
        $bedengContainer.addClass('d-none');
        $detailContainer.addClass('d-none');
        if(zonaId) {
            let lahanId = $lahan.val();
            let selectedLahan = lahanData.find(l => l.id == lahanId);
            let selectedZona = selectedLahan.zona.find(z => z.id == zonaId);
            if(selectedZona && selectedZona.pola) {
                selectedZona.pola.forEach(item => {
                    $pola.append(`<option value="${item.id}">${item.nama}</option>`);
                });
            }
        }
    });

    // Fetch Bedeng -> Wait, since we need to fetch Bedeng with their `tanamAktif` relation, we should still use AJAX to a proper route.
"""

content = re.sub(r'const \$lahan = \$\(\'#lahan_id\'\);.*?\}\);', new_script, content, flags=re.DOTALL)

with open('resources/views/konvensional/kebun/tanam-form.blade.php', 'w', encoding='utf-8') as f:
    f.write(content)

print('Replaced dropdown cascading')
