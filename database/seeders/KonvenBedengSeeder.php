<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KonvenBedengSeeder extends Seeder
{
    public function run()
    {
        // ============================================================
        // LAHAN ATAS: 1 zona, 10 pola (A-J), 52 bedeng
        // Pola A-I: 5 bedeng, Pola J: 7 bedeng
        // ============================================================
        $lahanAtas = DB::table('konven_lahan')->insertGetId([
            'nama' => 'Atas',
            'created_at' => now(), 'updated_at' => now()
        ]);

        $zonaAtas = DB::table('konven_zona')->insertGetId([
            'lahan_id' => $lahanAtas,
            'nama' => 'Atas',
            'created_at' => now(), 'updated_at' => now()
        ]);

        $polaLetters = range('A', 'J');
        foreach ($polaLetters as $idx => $letter) {
            $polaId = DB::table('konven_pola')->insertGetId([
                'zona_id' => $zonaAtas,
                'nama' => $letter,
                'urutan' => $idx + 1,
                'created_at' => now(), 'updated_at' => now()
            ]);

            $jumlahBedeng = ($letter === 'J') ? 7 : 5;
            for ($i = 1; $i <= $jumlahBedeng; $i++) {
                $kode = sprintf('ATAS-%s-%02d', $letter, $i);
                DB::table('konven_bedeng')->insert([
                    'pola_id' => $polaId,
                    'kode' => $kode,
                    'nomor' => $i,
                    'aktif' => true,
                    'created_at' => now(), 'updated_at' => now()
                ]);
            }
        }

        // ============================================================
        // LAHAN BAWAH: 2 zona (A & B), masing-masing 5 pola, 5 bedeng
        // Total: 50 bedeng
        // ============================================================
        $lahanBawah = DB::table('konven_lahan')->insertGetId([
            'nama' => 'Bawah',
            'created_at' => now(), 'updated_at' => now()
        ]);

        $zonaNames = ['Zona A' => 'ZA', 'Zona B' => 'ZB'];
        foreach ($zonaNames as $zonaName => $zonaCode) {
            $zonaId = DB::table('konven_zona')->insertGetId([
                'lahan_id' => $lahanBawah,
                'nama' => $zonaName,
                'created_at' => now(), 'updated_at' => now()
            ]);

            $polaLettersBawah = range('A', 'E');
            foreach ($polaLettersBawah as $idx => $letter) {
                $polaId = DB::table('konven_pola')->insertGetId([
                    'zona_id' => $zonaId,
                    'nama' => $letter,
                    'urutan' => $idx + 1,
                    'created_at' => now(), 'updated_at' => now()
                ]);

                for ($i = 1; $i <= 5; $i++) {
                    $kode = sprintf('BAWAH-%s-%s-%02d', $zonaCode, $letter, $i);
                    DB::table('konven_bedeng')->insert([
                        'pola_id' => $polaId,
                        'kode' => $kode,
                        'nomor' => $i,
                        'aktif' => true,
                        'created_at' => now(), 'updated_at' => now()
                    ]);
                }
            }
        }

        // ============================================================
        // SEED MASTER TANAMAN (data awal)
        // ============================================================
        $tanaman = [
            ['nama' => 'Kangkung',      'varietas' => null,        'lama_hari_ke_panen' => 25, 'satuan_hasil' => 'ikat', 'rata2_hasil_per_tanaman' => 0.5],
            ['nama' => 'Bayam',          'varietas' => null,        'lama_hari_ke_panen' => 25, 'satuan_hasil' => 'ikat', 'rata2_hasil_per_tanaman' => 0.3],
            ['nama' => 'Pakcoy',         'varietas' => null,        'lama_hari_ke_panen' => 30, 'satuan_hasil' => 'kg',   'rata2_hasil_per_tanaman' => 0.2],
            ['nama' => 'Caisim',         'varietas' => null,        'lama_hari_ke_panen' => 30, 'satuan_hasil' => 'kg',   'rata2_hasil_per_tanaman' => 0.25],
            ['nama' => 'Selada',         'varietas' => 'Keriting',  'lama_hari_ke_panen' => 35, 'satuan_hasil' => 'kg',   'rata2_hasil_per_tanaman' => 0.15],
            ['nama' => 'Tomat',          'varietas' => null,        'lama_hari_ke_panen' => 70, 'satuan_hasil' => 'kg',   'rata2_hasil_per_tanaman' => 2.0],
            ['nama' => 'Cabai Rawit',    'varietas' => null,        'lama_hari_ke_panen' => 80, 'satuan_hasil' => 'kg',   'rata2_hasil_per_tanaman' => 0.5],
            ['nama' => 'Cabai Merah',    'varietas' => 'Keriting',  'lama_hari_ke_panen' => 85, 'satuan_hasil' => 'kg',   'rata2_hasil_per_tanaman' => 0.8],
            ['nama' => 'Terong',         'varietas' => null,        'lama_hari_ke_panen' => 60, 'satuan_hasil' => 'kg',   'rata2_hasil_per_tanaman' => 1.5],
            ['nama' => 'Timun',          'varietas' => null,        'lama_hari_ke_panen' => 40, 'satuan_hasil' => 'kg',   'rata2_hasil_per_tanaman' => 1.0],
        ];

        foreach ($tanaman as $t) {
            DB::table('konven_tanaman')->insert(array_merge($t, [
                'catatan' => null,
                'created_at' => now(), 'updated_at' => now()
            ]));
        }

        $this->command->info('✅ Seeded: 2 Lahan, 3 Zona, 20 Pola, 102 Bedeng, 10 Jenis Tanaman');
    }
}
