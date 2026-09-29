import re

with open('resources/views/layouts/app.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

script_to_add = """
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

content = content.replace("</body>", script_to_add)

with open('resources/views/layouts/app.blade.php', 'w', encoding='utf-8') as f:
    f.write(content)

print("Added SweetAlert alerts for session/errors to app layout.")
