import re

with open('resources/views/hydroponics/plants.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

old_css = """    /* Growth Stage Section */
    .stage-section {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 1.25rem;
        margin-bottom: 1.25rem;
    }
    .stage-section-title {
        font-size: 0.875rem; font-weight: 600; color: #0f172a;
        display: flex; align-items: center; gap: 0.5rem;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid #e2e8f0;
    }
    
    .stage-row {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        padding: 1rem;
        margin-bottom: 0.75rem;
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }
    .stage-row-header {
        display: flex; align-items: center; gap: 0.5rem;
        font-size: 0.875rem; font-weight: 600;
    }
    .stage-inputs {
        display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 0.75rem;
    }
    .stage-input-wrap { position: relative; }
    .stage-input-wrap label {
        display: block; font-size: 0.75rem; font-weight: 500; color: #475569;
        margin-bottom: 0.25rem;
    }
    .stage-input-wrap input {
        width: 100%; padding: 0.5rem 0.5rem 0.5rem 0.5rem;
        border: 1px solid #cbd5e1; border-radius: 4px;
        font-size: 0.8125rem;
    }"""

new_css = """    /* Growth Stage Section */
    .stage-section {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 0.75rem;
        margin-bottom: 1rem;
    }
    .stage-section-title {
        font-size: 0.85rem; font-weight: 600; color: #0f172a;
        display: flex; align-items: center; gap: 0.5rem;
        margin-bottom: 0.5rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid #e2e8f0;
    }
    
    .stage-row {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        padding: 0.5rem 0.75rem;
        margin-bottom: 0.5rem;
        display: flex;
        flex-direction: column;
        gap: 0.35rem;
    }
    .stage-row-header {
        display: flex; align-items: center; gap: 0.5rem;
        font-size: 0.8rem; font-weight: 600;
    }
    .stage-inputs {
        display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 0.5rem;
    }
    .stage-input-wrap { position: relative; }
    .stage-input-wrap label {
        display: block; font-size: 0.7rem; font-weight: 500; color: #475569;
        margin-bottom: 0.15rem;
    }
    .stage-input-wrap input {
        width: 100%; padding: 0.35rem 0.4rem;
        border: 1px solid #cbd5e1; border-radius: 4px;
        font-size: 0.75rem;
    }"""

if old_css in content:
    content = content.replace(old_css, new_css)
else:
    print("Warning: CSS not found")

with open('resources/views/hydroponics/plants.blade.php', 'w', encoding='utf-8') as f:
    f.write(content)

print("CSS updated")
