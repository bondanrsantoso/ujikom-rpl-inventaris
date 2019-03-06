<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Level extends Model
{
    protected $fillable = [
        'nama_level'
    ];

    protected $primaryKey = "id_level";

    public function users()
    {
        return $this->hasMany('App\User', "id", $primaryKey);
    }

    public function petugas(){
        return $this->hasMany('App\Petugas', "id_level", $primaryKey);
    }
}
