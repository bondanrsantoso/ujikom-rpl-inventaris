<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TableRelationship extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("users", function(Blueprint $table){
            $table->foreign("id_level")->references("id_level")->on("levels");
        });
        Schema::table("petugas", function(Blueprint $table){
            $table->foreign("id_level")->references("id_level")->on("levels");
        });
        Schema::table("inventaris", function(Blueprint $table){
            $table->foreign("id_jenis")->references("id_jenis")->on("jenis");
            $table->foreign("id_ruang")->references("id_ruang")->on("ruangs");
            $table->foreign("id_petugas")->references("id_petugas")->on("petugas");
        });
        Schema::table("peminjaman", function(Blueprint $table){
            $table->foreign("id_pegawai")->references("id_pegawai")->on("pegawais");
        });
        Schema::table("detail_pinjams", function(Blueprint $table){
            $table->foreign("id_inventaris")->references("id_inventaris")->on("inventaris");
            $table->foreign("id_peminjaman")->references("id_peminjaman")->on("peminjaman");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
