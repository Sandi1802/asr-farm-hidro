import re

with open('resources/views/layouts/app.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

# Fix the notifDropdown: make it responsive on mobile
old_notif = 'id="notifDropdown" class="notif-dropdown" style="display:none; position: absolute; top: 110%; right: -10px; background: var(--card-bg); border: 1px solid var(--border-color); border-radius: var(--radius-md); min-width: 320px; max-width: 380px; box-shadow: var(--shadow-lg); z-index: 100; overflow: hidden;"'

new_notif = 'id="notifDropdown" class="notif-dropdown" style="display:none; position: fixed; top: 70px; right: 12px; left: 12px; background: var(--card-bg); border: 1px solid var(--border-color); border-radius: var(--radius-md); max-width: 400px; margin: 0 auto; box-shadow: var(--shadow-lg); z-index: 1000; overflow: hidden;"'

content = content.replace(old_notif, new_notif)

with open('resources/views/layouts/app.blade.php', 'w', encoding='utf-8') as f:
    f.write(content)

print("Patched notifDropdown responsive.")
