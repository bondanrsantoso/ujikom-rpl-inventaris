<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Petugas extends Model
{
    protected $primaryKey = "id_petugas";

    public function level()
    {
        return $this->belongsTo('App\Level', 'id_level', 'id_level');
    }

    public function inventaris(){
        return $this->hasMany('App\Inventaris', "id_petugas", $primaryKey);
    }
}
