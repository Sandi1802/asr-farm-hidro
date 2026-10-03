<?php
$file = 'resources/views/konvensional/dashboard.blade.php';
$content = file_get_contents($file);

// Find the first instance of document.addEventListener('DOMContentLoaded'
$pos1 = strpos($content, "document.addEventListener('DOMContentLoaded'");
if ($pos1 === false) die("Cannot find DOMContentLoaded\n");

// Find the SECOND instance of `if (typeof initCalendar === 'function') initCalendar();`
// The first one is in the first script block.
// Let's use preg_replace to remove everything from `if (typeof initCalendar === 'function') initCalendar();`
// that appears AFTER `DOMContentLoaded`.

$pattern = '/(document\.addEventListener\(\'DOMContentLoaded\'.*?\}\s*\n)\s*if \(typeof initCalendar === \'function\'\) initCalendar\(\);\s*\/\/ \n\/\/  DATA FROM BLADE.*/s';

$content = preg_replace('/(\n}\s*)if\s*\(typeof initCalendar === \'function\'\)\s*initCalendar\(\);\s*\/\/\s*â• \s*â• .*/s', "$1\n</script>\n@endsection", $content);

// Did it change?
echo "Length now: " . strlen($content) . "\n";
file_put_contents($file, $content);

// Now fix the syntax error!
$content = preg_replace('/<span>\$\{dateDisplay\}<\/span>\n\s*<\/div>\n\s*if\s*\(typeof initCalendar === \'function\'\)\s*initCalendar\(\);\n<\/script>/s', 
    "<span>\${dateDisplay}</span>\n        </div>\n        \n        <div style=\"border-top:1px dashed var(--border-color); padding-top:1.25rem;\">\n            ` + (subtitle ? `<div style=\"margin-bottom:1rem;\"><div style=\"font-size:0.75rem; color:var(--text-muted); font-weight:600; text-transform:uppercase; margin-bottom:0.25rem;\">Lokasi</div><div style=\"font-size:0.9rem; color:var(--text-main); font-weight:500;\"><i class=\"ph ph-map-pin\" style=\"color:var(--text-muted);\"></i> \${subtitle}</div></div>` : '') + `\n            ` + (ev.hole_count ? `<div style=\"margin-bottom:1rem;\"><div style=\"font-size:0.75rem; color:var(--text-muted); font-weight:600; text-transform:uppercase; margin-bottom:0.25rem;\">Jumlah Lubang</div><div style=\"font-size:0.9rem; color:var(--text-main); font-weight:500;\">\${ev.hole_count.toLocaleString('id-ID')} lubang tanam</div></div>` : '') + `\n        </div>\n    `;\n\n    document.getElementById('viewEventModalContent').innerHTML = contentHtml;\n    document.getElementById('viewEventModal').classList.add('open');\n}\n\n// Close modals on overlay click\ndocument.querySelectorAll('.modal-overlay').forEach(el => {\n    el.addEventListener('click', function(e) {\n        if (e.target === this) this.classList.remove('open');\n    });\n});\n    \n    if (typeof initCalendar === 'function') initCalendar();\n</script>", 
    $content);
file_put_contents($file, $content);
echo "Final length: " . strlen($content) . "\n";
