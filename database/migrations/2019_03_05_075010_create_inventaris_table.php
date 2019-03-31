<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInventarisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventaris', function (Blueprint $table) {
            $table->bigIncrements('id_inventaris');
            $table->string('nama');
            $table->enum('kondisi', ["baik", "rusak", "tidak_ada"]);
            $table->text("keterangan");
            $table->integer("jumlah");
            $table->unsignedBigInteger("id_jenis");
            $table->date("tanggal_register");
            $table->unsignedBigInteger("id_ruang");
            $table->string("kode_inventaris");
            $table->unsignedBigInteger("id_petugas");
            $table->string("url_photo")->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inventaris');
    }
}
