<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KonvenTanaman extends Model
{
    protected $table = 'konven_tanaman';
    protected $fillable = ['nama', 'varietas', 'lama_hari_ke_panen', 'satuan_hasil', 'rata2_hasil_per_tanaman', 'catatan'];

    public function tanam()
    {
        return $this->hasMany(KonvenTanam::class, 'tanaman_id');
    }

    /**
     * Nama lengkap termasuk varietas jika ada.
     */
    public function getNamaLengkapAttribute()
    {
        return $this->varietas ? "{$this->nama} ({$this->varietas})" : $this->nama;
    }
}
