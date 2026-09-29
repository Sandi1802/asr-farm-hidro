import re

with open('resources/views/hydroponics/dashboard.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

# Fix loading state reset
old_loading = """        const ids = ['card-lubang-kosong', 'card-lubang-terisi', 'card-siap-panen', 'card-sudah-panen', 'card-gagal-panen'];
        ids.forEach(id => {
            // User requested to show 0 instead of a loading spinner
            if(document.getElementById(id)) document.getElementById(id).innerHTML = '0';
        });"""
new_loading = """        const ids = ['card-lubang-kosong', 'card-lubang-terisi', 'card-siap-panen', 'card-sudah-panen', 'card-gagal-panen'];
        ids.forEach(id => {
            if(document.getElementById(id)) document.getElementById(id).style.opacity = '0.5';
        });"""
content = content.replace(old_loading, new_loading)

# Fix appending LT
old_update = """              if(document.getElementById('card-lubang-kosong')) {
                  let elKosong = document.getElementById('card-lubang-kosong'); if(elKosong) elKosong.textContent = data.lubang_kosong;
                  let elTerisi = document.getElementById('card-lubang-terisi'); if(elTerisi) elTerisi.textContent = data.lubang_terisi;
                  let elTerisiSub = document.getElementById('card-lubang-terisi-sub'); if(elTerisiSub) elTerisiSub.textContent = data.lubang_terisi_sub;
                  let elSiap = document.getElementById('card-siap-panen'); if(elSiap) elSiap.textContent = data.siap_panen;
                  let elSiapSub = document.getElementById('card-siap-panen-sub'); if(elSiapSub) elSiapSub.textContent = data.siap_panen_sub;
                  
                  let modalBody = document.querySelector('#siapPanenModal > div > div:nth-child(2)');
                  if (modalBody && data.siap_panen_html) {
                      modalBody.innerHTML = data.siap_panen_html;
                  }
                  
                  let lubangKosongBody = document.getElementById('lubangKosongModalContainer');
                  if (lubangKosongBody && data.lubang_kosong_html) {
                      lubangKosongBody.innerHTML = data.lubang_kosong_html;
                  }
                  
                  let elSudah = document.getElementById('card-sudah-panen'); if(elSudah) elSudah.textContent = data.sudah_panen;
                  let elSudahSub = document.getElementById('card-sudah-panen-sub'); if(elSudahSub) elSudahSub.textContent = data.sudah_panen_sub;
                  let elGagal = document.getElementById('card-gagal-panen'); if(elGagal) elGagal.textContent = data.gagal_panen;
                  let elGagalSub = document.getElementById('card-gagal-panen-sub'); if(elGagalSub) elGagalSub.textContent = data.gagal_panen_sub;"""
new_update = """              if(document.getElementById('card-lubang-kosong')) {
                  let elKosong = document.getElementById('card-lubang-kosong'); if(elKosong) { elKosong.textContent = data.lubang_kosong + ' LT'; elKosong.style.opacity = '1'; }
                  let elTerisi = document.getElementById('card-lubang-terisi'); if(elTerisi) { elTerisi.textContent = data.lubang_terisi; elTerisi.style.opacity = '1'; }
                  let elTerisiSub = document.getElementById('card-lubang-terisi-sub'); if(elTerisiSub) elTerisiSub.textContent = data.lubang_terisi_sub;
                  let elSiap = document.getElementById('card-siap-panen'); if(elSiap) { elSiap.textContent = data.siap_panen + ' LT'; elSiap.style.opacity = '1'; }
                  let elSiapSub = document.getElementById('card-siap-panen-sub'); if(elSiapSub) elSiapSub.textContent = data.siap_panen_sub;
                  
                  let modalBody = document.querySelector('#siapPanenModal > div > div:nth-child(2)');
                  if (modalBody && data.siap_panen_html) {
                      modalBody.innerHTML = data.siap_panen_html;
                  }
                  
                  let lubangKosongBody = document.getElementById('lubangKosongModalContainer');
                  if (lubangKosongBody && data.lubang_kosong_html) {
                      lubangKosongBody.innerHTML = data.lubang_kosong_html;
                  }
                  
                  let elSudah = document.getElementById('card-sudah-panen'); if(elSudah) { elSudah.textContent = data.sudah_panen + ' LT'; elSudah.style.opacity = '1'; }
                  let elSudahSub = document.getElementById('card-sudah-panen-sub'); if(elSudahSub) elSudahSub.textContent = data.sudah_panen_sub;
                  let elGagal = document.getElementById('card-gagal-panen'); if(elGagal) { elGagal.textContent = data.gagal_panen + ' LT'; elGagal.style.opacity = '1'; }
                  let elGagalSub = document.getElementById('card-gagal-panen-sub'); if(elGagalSub) elGagalSub.textContent = data.gagal_panen_sub;"""
content = content.replace(old_update, new_update)

with open('resources/views/hydroponics/dashboard.blade.php', 'w', encoding='utf-8') as f:
    f.write(content)

print("Dashboard UI JS fixed")
