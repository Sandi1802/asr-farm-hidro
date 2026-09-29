<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class KonvenTanam extends Model
{
    protected $table = 'konven_tanam';
    protected $fillable = [
        'bedeng_id', 'tanaman_id', 'batch_id', 'tanggal_tanam', 'jumlah_tanam',
        'jarak_tanam', 'sumber_benih', 'estimasi_tanggal_panen', 'estimasi_hasil',
        'status', 'catatan', 'alasan_gagal', 'dibuat_oleh'
    ];

    protected $casts = [
        'tanggal_tanam' => 'date',
        'estimasi_tanggal_panen' => 'date',
    ];

    public function bedeng()
    {
        return $this->belongsTo(KonvenBedeng::class, 'bedeng_id');
    }

    public function tanaman()
    {
        return $this->belongsTo(KonvenTanaman::class, 'tanaman_id');
    }

    public function panen()
    {
        return $this->hasMany(KonvenPanen::class, 'tanam_id');
    }

    public function pembuat()
    {
        return $this->belongsTo(\App\Models\User::class, 'dibuat_oleh');
    }

    /**
     * Umur tanaman dalam hari.
     */
    public function getUmurHariAttribute()
    {
        return Carbon::parse($this->tanggal_tanam)->diffInDays(now());
    }

    /**
     * Sisa hari ke estimasi panen.
     */
    public function getSisaHariAttribute()
    {
        return (int) now()->startOfDay()->diffInDays(Carbon::parse($this->estimasi_tanggal_panen)->startOfDay(), false);
    }

    /**
     * Total hasil panen yang sudah tercatat.
     */
    public function getTotalPanenAttribute()
    {
        return $this->panen()->sum('jumlah_hasil');
    }

    /**
     * Selisih realisasi vs estimasi.
     */
    public function getSelisihHasilAttribute()
    {
        return $this->total_panen - $this->estimasi_hasil;
    }

    /**
     * Scope: hanya tanam aktif.
     */
    public function scopeAktif($query)
    {
        return $query->whereIn('status', ['tumbuh', 'panen_sebagian']);
    }

    /**
     * Scope: siap panen (estimasi <= hari ini).
     */
    public function scopeSiapPanen($query)
    {
        return $query->aktif()->whereDate('estimasi_tanggal_panen', '<=', now());
    }

    /**
     * Scope: mendekati panen (7 hari ke depan).
     */
    public function scopeMendekatiPanen($query)
    {
        return $query->aktif()
            ->whereDate('estimasi_tanggal_panen', '>', now())
            ->whereDate('estimasi_tanggal_panen', '<=', now()->addDays(7));
    }
}
