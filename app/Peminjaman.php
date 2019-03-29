<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Peminjaman extends Model
{
    protected $table = "peminjaman";
    protected $primaryKey = "id_peminjaman";

    public function pegawai()
    {
        return $this->belongsTo("App\Pegawai", "id_pegawai", "id_pegawai");
    }

    public function detailPinjam()
    {
        return $this->hasMany("App\DetailPinjam", "id_peminjaman", $this->primaryKey);
    }
}
