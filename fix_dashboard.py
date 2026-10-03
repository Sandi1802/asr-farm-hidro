import re
with open('resources/views/konvensional/dashboard.blade.php', 'r', encoding='utf-8') as f:
    text = f.read()

# 1. Truncate the file at the end of switchPeriodKonv
match = re.search(r'console\.error\(\'Period filter error:\', err\);\s*loadingCards\.forEach\(id => \{\s*const el = document\.getElementById\(id\);\s*if \(el\) el\.closest\(\'\.stat-big-card\'\)\.style\.opacity = \'1\';\s*\}\);\s*\}\);\s*\}', text)
if match:
    end_pos = match.end()
    text = text[:end_pos] + "\n</script>\n@endsection\n"
    print("Truncated file!")
else:
    print("Could not find truncation point")

# 2. Fix the syntax error in the remaining showEventDetail function
broken_pattern = r'<span>\$\{dateDisplay\}</span>\s*</div>\s*if\s*\(typeof initCalendar === \'function\'\)\s*initCalendar\(\);\s*</script>'
fixed = """<span>${dateDisplay}</span>
        </div>
        
        <div style="border-top:1px dashed var(--border-color); padding-top:1.25rem;">
            ` + (subtitle ? `<div style="margin-bottom:1rem;"><div style="font-size:0.75rem; color:var(--text-muted); font-weight:600; text-transform:uppercase; margin-bottom:0.25rem;">Lokasi</div><div style="font-size:0.9rem; color:var(--text-main); font-weight:500;"><i class="ph ph-map-pin" style="color:var(--text-muted);"></i> ${subtitle}</div></div>` : '') + `
            ` + (ev.hole_count ? `<div style="margin-bottom:1rem;"><div style="font-size:0.75rem; color:var(--text-muted); font-weight:600; text-transform:uppercase; margin-bottom:0.25rem;">Jumlah Lubang</div><div style="font-size:0.9rem; color:var(--text-main); font-weight:500;">${ev.hole_count.toLocaleString('id-ID')} lubang tanam</div></div>` : '') + `
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
</script>"""
text = re.sub(broken_pattern, fixed, text)

with open('resources/views/konvensional/dashboard.blade.php', 'w', encoding='utf-8') as f:
    f.write(text)
print("Done")
