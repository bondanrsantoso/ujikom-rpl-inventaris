<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Jenis extends Model
{
    protected $primaryKey = "id_jenis";

    public function inventaris()
    {
        $this->hasMany('App\Inventaris', "id_jenis", $primaryKey);
    }
}
