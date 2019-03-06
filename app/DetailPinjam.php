<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DetailPinjam extends Model
{
    protected $primaryKey = "id_detail_pinjam";

    public function peminjaman()
    {
        return $this->belongsTo("App\Peminjaman", "id_peminjaman", "id_peminjaman");
    }

    public function inventaris()
    {
        return $this->belongsTo("App\Inventaris", "id_inventaris", "id_inventaris");
    }
}
