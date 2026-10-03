<?php
$file = 'resources/views/konvensional/dashboard.blade.php';
$content = file_get_contents($file);

// 1. Remove the duplicated second script block entirely.
// Find the end of `switchPeriodKonv` function. It ends with:
/*
        .catch(err => {
            console.error('Period filter error:', err);
            loadingCards.forEach(id => {
                const el = document.getElementById(id);
                if (el) el.closest('.stat-big-card').style.opacity = '1';
            });
        });
}
*/
$marker = "        });\n}\n\n    if (typeof initCalendar === 'function') initCalendar();";
$pos = strpos($content, $marker);
if ($pos !== false) {
    // Truncate everything from the duplicated `initCalendar` to the end
    $content = substr($content, 0, $pos + 11) . "\n</script>\n\n@endsection\n";
}

// 2. Fix the syntax error in the remaining showEventDetail function.
$broken_html = <<<HTML
        <div style="display:flex; align-items:center; gap:0.5rem; color:var(--text-muted); font-size:0.9rem; margin-bottom:1.5rem;">
            <i class="ph ph-calendar-blank" style="font-size:1.1rem;"></i> 
            <span>\${dateDisplay}</span>
        </div>
        

    if (typeof initCalendar === 'function') initCalendar();
</script>
HTML;

$fixed_html = <<<HTML
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
    
    if (typeof initCalendar === 'function') initCalendar();
</script>
HTML;

// We will use str_replace for the broken HTML part
// Just to be safe with line endings, let's normalize CRLF to LF first
$content = str_replace("\r\n", "\n", $content);
$broken_html = str_replace("\r\n", "\n", $broken_html);
$fixed_html = str_replace("\r\n", "\n", $fixed_html);

$content = str_replace($broken_html, $fixed_html, $content);

file_put_contents($file, $content);
echo "File processed.\n";
