<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KonvenBedeng extends Model
{
    protected $table = 'konven_bedeng';
    protected $fillable = ['pola_id', 'kode', 'nomor', 'luas_m2', 'aktif'];

    protected $casts = [
        'aktif' => 'boolean',
    ];

    public function pola()
    {
        return $this->belongsTo(KonvenPola::class, 'pola_id');
    }

    public function tanam()
    {
        return $this->hasMany(KonvenTanam::class, 'bedeng_id');
    }

    /**
     * Tanam aktif (tumbuh atau panen_sebagian) - hanya boleh 1 pada satu waktu.
     */
    public function tanamAktif()
    {
        return $this->hasOne(KonvenTanam::class, 'bedeng_id')
            ->whereIn('status', ['tumbuh', 'panen_sebagian'])
            ->latest('tanggal_tanam');
    }

    /**
     * Status tampilan diturunkan dari data tanam aktif.
     */
    public function getStatusTampilanAttribute()
    {
        $tanamAktif = $this->relationLoaded('tanamAktif') ? $this->tanamAktif : $this->tanamAktif()->first();

        if (!$tanamAktif) {
            return 'kosong';
        }

        $today = now()->startOfDay();
        $estPanen = \Carbon\Carbon::parse($tanamAktif->estimasi_tanggal_panen)->startOfDay();
        $diff = $today->diffInDays($estPanen, false); // negative = lewat

        if ($diff < -7) {
            return 'terlambat'; // > 7 hari lewat estimasi
        } elseif ($diff <= 0) {
            return 'siap_panen'; // tanggal estimasi tercapai
        } elseif ($diff <= 7) {
            return 'mendekati_panen'; // <= 7 hari
        }

        return 'tumbuh';
    }
}
