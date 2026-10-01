<?php
$f = 'app/Http/Controllers/KonvenV2Controller.php';
$c = file_get_contents($f);

$oldCodeUpdate = <<<'EOF'
    public function kodeUpdate(Request $r, $id)
    {
        $r->validate(['nomor_urut' => 'required|integer|min:1']);

        $kodeModel  = KonvenKodeV2::with('posisi')->findOrFail($id);
        $kodeBaru   = $kodeModel->posisi->prefix_kode . $r->nomor_urut;

        $kodeModel->update([
            'kode'       => $kodeBaru,
            'nomor_urut' => $r->nomor_urut,
        ]);

        return back()->with('success', "Kode berhasil diperbarui menjadi {$kodeBaru}.");
    }
EOF;

$newCodeUpdate = <<<'EOF'
    public function kodeUpdate(Request $r, $id)
    {
        $r->validate([
            'prefix_kode'  => 'required|alpha|size:1',
            'nomor_urut'   => 'required|integer|min:1',
            'label_posisi' => 'nullable|string|max:50',
        ]);

        $kodeModel = KonvenKodeV2::findOrFail($id);
        $kodeBaru  = strtoupper($r->prefix_kode) . $r->nomor_urut;

        $kodeModel->update([
            'kode'         => $kodeBaru,
            'nomor_urut'   => $r->nomor_urut,
            'label_posisi' => $r->label_posisi,
        ]);

        return back()->with('success', "Kode berhasil diperbarui menjadi {$kodeBaru}.");
    }
EOF;

$c = str_replace($oldCodeUpdate, $newCodeUpdate, $c);
file_put_contents($f, $c);
echo "Controller kodeUpdate updated.\n";

$v = 'resources/views/konvensional/v2/kode.blade.php';
$vc = file_get_contents($v);

$oldModal = <<<'EOF'
        <form id="formEditKode" method="POST">
            @csrf @method('PUT')
            <div style="margin-bottom:1rem;">
                <label style="display:block; margin-bottom:0.4rem; font-size:0.85rem; font-weight:500;">Nomor Urut *</label>
                <input type="number" id="editKodeNomor" name="nomor_urut" required min="1"
                       style="width:100%; padding:0.65rem; border:1px solid var(--border-color); border-radius:8px; box-sizing:border-box;">
            </div>
            <div style="margin-bottom:1.25rem;">
                <label style="display:block; margin-bottom:0.4rem; font-size:0.85rem; font-weight:500;">Label Posisi</label>
                <input type="text" id="editKodeLabelPosisi" name="label_posisi" maxlength="50" placeholder="Atas"
                       style="width:100%; padding:0.65rem; border:1px solid var(--border-color); border-radius:8px; box-sizing:border-box;">
            </div>
            <div style="display:flex; justify-content:flex-end; gap:0.5rem;">
EOF;

$newModal = <<<'EOF'
        <form id="formEditKode" method="POST">
            @csrf @method('PUT')
            <div style="display:flex; gap:1rem; margin-bottom:1rem;">
                <div style="flex:1;">
                    <label style="display:block; margin-bottom:0.4rem; font-size:0.85rem; font-weight:500;">Prefix (A-Z) *</label>
                    <input type="text" id="editKodePrefix" name="prefix_kode" required maxlength="1" style="text-transform:uppercase;"
                           style="width:100%; padding:0.65rem; border:1px solid var(--border-color); border-radius:8px; box-sizing:border-box;">
                </div>
                <div style="flex:1;">
                    <label style="display:block; margin-bottom:0.4rem; font-size:0.85rem; font-weight:500;">Nomor Urut *</label>
                    <input type="number" id="editKodeNomor" name="nomor_urut" required min="1"
                           style="width:100%; padding:0.65rem; border:1px solid var(--border-color); border-radius:8px; box-sizing:border-box;">
                </div>
            </div>
            <div style="margin-bottom:1.25rem;">
                <label style="display:block; margin-bottom:0.4rem; font-size:0.85rem; font-weight:500;">Label Posisi</label>
                <input type="text" id="editKodeLabelPosisi" name="label_posisi" maxlength="50" placeholder="Atas"
                       style="width:100%; padding:0.65rem; border:1px solid var(--border-color); border-radius:8px; box-sizing:border-box;">
            </div>
            <div style="display:flex; justify-content:flex-end; gap:0.5rem;">
EOF;

$vc = str_replace($oldModal, $newModal, $vc);

$oldScript = <<<'EOF'
function openEditKode(id, nomor, labelPosisi) {
    document.getElementById('editKodeNomor').value         = nomor;
    document.getElementById('editKodeLabelPosisi').value   = labelPosisi;
    document.getElementById('formEditKode').action         = '/konvensional/v2/kode/' + id;
    document.getElementById('modalEditKode').style.display = 'flex';
}
EOF;

$newScript = <<<'EOF'
function openEditKode(id, prefix, nomor, labelPosisi) {
    document.getElementById('editKodePrefix').value        = prefix;
    document.getElementById('editKodeNomor').value         = nomor;
    document.getElementById('editKodeLabelPosisi').value   = labelPosisi;
    document.getElementById('formEditKode').action         = '/konvensional/v2/kode/' + id;
    document.getElementById('modalEditKode').style.display = 'flex';
}
EOF;

$vc = str_replace($oldScript, $newScript, $vc);

// Also need to update the button onclick in the table
$oldButton = "<button onclick=\"openEditKode({{ \$kode->id }}, '{{ preg_replace('/[0-9]/','',\$kode->kode) }}', '{{ \$kode->nomor_urut }}', '{{ addslashes(\$kode->label_posisi ?? '') }}')\"";
$newButton = "<button onclick=\"openEditKode({{ \$kode->id }}, '{{ preg_replace('/[0-9]/','',\$kode->kode) }}', '{{ \$kode->nomor_urut }}', '{{ addslashes(\$kode->label_posisi ?? '') }}')\"";
// Wait, the old button passed preg_replace() as the second parameter which is the prefix! But it only had 3 parameters in the JS definition: function openEditKode(id, nomor, labelPosisi).
// Let's check what I originally generated for the table:
// `<button onclick="openEditKode({{ $item->id }}, '{{ preg_replace('/[0-9]/','',$item->kode) }}', '{{ $item->nomor_urut }}', '{{ addslashes($item->label_posisi ?? '') }}')"`
// Oh, the button already passed 4 parameters! But the JS function only had 3, so `labelPosisi` was getting `nomor_urut` and `nomor` was getting `prefix`. Wow!
file_put_contents($v, $vc);
echo "View kode updated.\n";
