<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Jenis extends Model
{
    use SoftDeletes;
    
    protected $primaryKey = "id_jenis";

    public function inventaris()
    {
        $this->hasMany('App\Inventaris', "id_jenis", $primaryKey);
    }
}
