<?php
$file = 'resources/views/konvensional/dashboard.blade.php';
$content = file_get_contents($file);

$target = <<<HTML
        <div style="display:flex; align-items:center; gap:0.5rem; color:var(--text-muted); font-size:0.9rem; margin-bottom:1.5rem;">
            <i class="ph ph-calendar-blank" style="font-size:1.1rem;"></i> 
            <span>\${dateDisplay}</span>
        </div>
HTML;

$replacement = <<<HTML
        <div style="display:flex; align-items:center; gap:0.5rem; color:var(--text-muted); font-size:0.9rem; margin-bottom:1.5rem;">
            <i class="ph ph-calendar-blank" style="font-size:1.1rem;"></i> 
            <span>\${dateDisplay}</span>
        </div>
        <div style="border-top:1px dashed var(--border-color); padding-top:1.25rem;">
            ` + (subtitle ? `<div style="margin-bottom:1rem;"><div style="font-size:0.75rem; color:var(--text-muted); font-weight:600; text-transform:uppercase; margin-bottom:0.25rem;">Lokasi</div><div style="font-size:0.9rem; color:var(--text-main); font-weight:500;"><i class="ph ph-map-pin" style="color:var(--text-muted);"></i> \${subtitle}</div></div>` : '') + `
            ` + (ev.hole_count ? `<div style="margin-bottom:1rem;"><div style="font-size:0.75rem; color:var(--text-muted); font-weight:600; text-transform:uppercase; margin-bottom:0.25rem;">Jumlah Lubang</div><div style="font-size:0.9rem; color:var(--text-main); font-weight:500;">\${ev.hole_count.toLocaleString('id-ID')} lubang tanam</div></div>` : '') + `
        </div>
    `;

    document.getElementById('viewEventModalContent').innerHTML = contentHtml;
    document.getElementById('viewEventModal').classList.add('open');
}

// Close modals on overlay click
document.querySelectorAll('.modal-overlay').forEach(el => {
    el.addEventListener('click', function(e) {
        if (e.target === this) this.classList.remove('open');
    });
});
HTML;

$content = str_replace($target, $replacement, $content);
file_put_contents($file, $content);
