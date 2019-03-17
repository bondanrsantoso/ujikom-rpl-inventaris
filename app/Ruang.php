<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ruang extends Model
{
    use SoftDeletes;

    protected $primaryKey = "id_ruang";

    protected $fillable = ["nama_ruang", "kode_ruang", "keterangan"];

    public function inventaris()
    {
        return $this->hasMany("App\Inventaris", "id_ruang", $primaryKey);
    }
}
