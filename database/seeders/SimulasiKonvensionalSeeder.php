<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Lahan;
use App\Models\Bedengan;
use App\Models\TitikTanam;
use App\Models\RiwayatPanenKonvensional;

class SimulasiKonvensionalSeeder extends Seeder
{
    public function run()
    {
        // Bersihkan data lama (opsional, karena ini simulasi)
        // Hati-hati jangan jalankan truncate jika data real
        
        
        
        
        Lahan::query()->delete();
        

        // Buat Lahan 1
        $lahan1 = Lahan::create([
            'nama_lahan' => 'Lahan A (Blok Utara)',
            'deskripsi' => 'Fokus tanaman tumpang sari sayur daun dan buah.',
            'status' => 'aktif'
        ]);

        // Buat Lahan 2
        $lahan2 = Lahan::create([
            'nama_lahan' => 'Lahan B (Blok Selatan)',
            'deskripsi' => 'Lahan khusus rotasi tanaman umbi-umbian.',
            'status' => 'aktif'
        ]);

        // Buat Bedengan untuk Lahan 1
        for ($i = 1; $i <= 3; $i++) {
            $bedengan = Bedengan::create([
                'lahan_id' => $lahan1->id,
                'nama_bedengan' => 'Bedengan A' . $i,
                'pakai_mulsa' => true
            ]);

            // Buat 10 Titik per bedengan
            for ($j = 1; $j <= 10; $j++) {
                $status = 'kosong';
                $tanamanUtama = null;
                $tanamanSekunder = null;
                $kosongSejak = null;

                // Simulasi Tumpang Sari (Bedengan 1)
                if ($i == 1 && $j <= 5) {
                    $status = 'ditanam';
                    $tanamanUtama = 'Cabai Merah';
                    $tanamanSekunder = 'Bawang Merah'; // Tumpang Sari
                }
                
                // Simulasi Lahan Menganggur (Bedengan 2, > 5 hari)
                if ($i == 2 && $j <= 3) {
                    $kosongSejak = Carbon::now()->subDays(6);
                }

                // Simulasi Normal Kosong (Bedengan 2, < 3 hari)
                if ($i == 2 && $j > 3 && $j <= 6) {
                    $kosongSejak = Carbon::now()->subDays(1);
                }

                // Simulasi Panen Petik (Bedengan 3)
                if ($i == 3 && $j <= 2) {
                    $status = 'ditanam';
                    $tanamanUtama = 'Brokoli';
                }

                $titik = TitikTanam::create([
                    'bedengan_id' => $bedengan->id,
                    'nama_titik' => 'Titik ' . $j,
                    'nama_tanaman' => $tanamanUtama,
                    'tanaman_sekunder' => $tanamanSekunder,
                    'status' => $status,
                    'tanggal_tanam' => $status == 'ditanam' ? Carbon::now()->subDays(20) : null,
                    'kosong_sejak' => $kosongSejak
                ]);

                // Generate riwayat panen petik untuk Brokoli
                if ($tanamanUtama == 'Brokoli') {
                    RiwayatPanenKonvensional::create([
                        'titik_tanam_id' => $titik->id,
                        'jenis_panen' => 'petik',
                        'jumlah_kg' => rand(1, 3) . ' Kg',
                        'catatan' => 'Panen Petik Pertama',
                        'created_at' => Carbon::now()->subDays(2)
                    ]);
                }
            }
        }

        // Buat Bedengan untuk Lahan 2 (Semua kosong)
        for ($i = 1; $i <= 2; $i++) {
            $bedengan = Bedengan::create([
                'lahan_id' => $lahan2->id,
                'nama_bedengan' => 'Bedengan B' . $i,
                'pakai_mulsa' => false
            ]);

            for ($j = 1; $j <= 5; $j++) {
                TitikTanam::create([
                    'bedengan_id' => $bedengan->id,
                    'nama_titik' => 'Titik ' . $j,
                    'status' => 'kosong',
                    'kosong_sejak' => Carbon::now()->subDays(10) // Sangat terlambat
                ]);
            }
        }
    }
}
