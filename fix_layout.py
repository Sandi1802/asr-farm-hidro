import re

with open('resources/views/layouts/app.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

# The exact snippet that was wrongly inserted
wrong_snippet = """
    <!-- Global Session Alerts -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: '{{ session('success') }}',
                    timer: 3000,
                    showConfirmButton: false
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: '{{ session('error') }}'
                });
            @endif

            @if($errors->any())
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: '{{ $errors->first() }}'
                });
            @endif
        });
    </script>
</body>"""

# Replace all instances of the wrong snippet back to </body>
content = content.replace(wrong_snippet, "</body>")

# Now add it safely to the end of the file, replacing ONLY the last </body>
last_body_index = content.rfind("</body>")
if last_body_index != -1:
    content = content[:last_body_index] + wrong_snippet + content[last_body_index + 7:]

with open('resources/views/layouts/app.blade.php', 'w', encoding='utf-8') as f:
    f.write(content)

print("Fixed the duplicate </body> injection issue.")
