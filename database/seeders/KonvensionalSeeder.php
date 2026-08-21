<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Lahan;
use App\Models\Bedengan;
use App\Models\TitikTanam;

class KonvensionalSeeder extends Seeder
{
    public function run()
    {
        $lahan1 = Lahan::firstOrCreate(["nama_lahan" => "Lahan Sayur Buah"], ["deskripsi" => "Blok A Utara", "status" => "aktif"]);
        for ($i = 1; $i <= 3; $i++) {
            $bedeng = Bedengan::firstOrCreate(["nama_bedengan" => "Bedeng $i", "lahan_id" => $lahan1->id], ["pakai_mulsa" => true, "status" => "aktif"]);
            if ($bedeng->titik_tanam()->count() == 0) {
                for ($t = 1; $t <= 25; $t++) {
                    $isTanam = ($t % 5 == 0);
                    TitikTanam::create(["bedengan_id" => $bedeng->id, "nama_titik" => "TT-$lahan1->id-$bedeng->id-$t", "nama_tanaman" => $isTanam ? "Tomat" : null, "status" => $isTanam ? "ditanam" : "kosong"]);
                }
            }
        }
        $lahan2 = Lahan::firstOrCreate(["nama_lahan" => "Lahan Sayur Daun"], ["deskripsi" => "Blok B Selatan", "status" => "aktif"]);
        for ($i = 1; $i <= 2; $i++) {
            $bedeng = Bedengan::firstOrCreate(["nama_bedengan" => "Bedeng B$i", "lahan_id" => $lahan2->id], ["pakai_mulsa" => false, "status" => "aktif"]);
            if ($bedeng->titik_tanam()->count() == 0) {
                for ($t = 1; $t <= 40; $t++) {
                    TitikTanam::create(["bedengan_id" => $bedeng->id, "nama_titik" => "TT-B-$bedeng->id-$t", "status" => "kosong"]);
                }
            }
        }
    }
}
