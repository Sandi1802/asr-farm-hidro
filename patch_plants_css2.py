import re

with open('resources/views/hydroponics/plants.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

# Replace padding and margins using regex
content = re.sub(r'\.stage-section\s*\{[^}]*\}', """.stage-section {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 0.75rem;
        margin-bottom: 1rem;
    }""", content)

content = re.sub(r'\.stage-section-title\s*\{[^}]*\}', """.stage-section-title {
        font-size: 0.85rem; font-weight: 600; color: #0f172a;
        display: flex; align-items: center; gap: 0.5rem;
        margin-bottom: 0.5rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid #e2e8f0;
    }""", content)

content = re.sub(r'\.stage-row\s*\{[^}]*\}', """.stage-row {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        padding: 0.5rem 0.75rem;
        margin-bottom: 0.5rem;
        display: flex;
        flex-direction: column;
        gap: 0.35rem;
    }""", content)

content = re.sub(r'\.stage-row-header\s*\{[^}]*\}', """.stage-row-header {
        display: flex; align-items: center; gap: 0.5rem;
        font-size: 0.8rem; font-weight: 600;
    }""", content)

content = re.sub(r'\.stage-inputs\s*\{[^}]*\}', """.stage-inputs {
        display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 0.5rem;
    }""", content)

content = re.sub(r'\.stage-input-wrap label\s*\{[^}]*\}', """.stage-input-wrap label {
        display: block; font-size: 0.7rem; font-weight: 500; color: #475569;
        margin-bottom: 0.15rem;
    }""", content)

content = re.sub(r'\.stage-input-wrap input\s*\{[^}]*\}', """.stage-input-wrap input {
        width: 100%; padding: 0.35rem 0.4rem;
        border: 1px solid #cbd5e1; border-radius: 4px;
        font-size: 0.75rem;
    }""", content)

# I should also adjust `.form-group` to be smaller in this file
content = re.sub(r'\.form-group\s*\{\s*margin-bottom:\s*1\.25rem;\s*\}', ".form-group { margin-bottom: 0.75rem; }", content)

with open('resources/views/hydroponics/plants.blade.php', 'w', encoding='utf-8') as f:
    f.write(content)

print("CSS updated")
