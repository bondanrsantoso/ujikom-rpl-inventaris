<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StoredProcedure extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('
            CREATE TRIGGER after_detail_pinjams_insert_or_update
                AFTER INSERT ON detail_pinjams
                FOR EACH ROW
            BEGIN
                IF NEW.kembali = 0 THEN
                    UPDATE inventaris SET inventaris.jumlah = (inventaris.jumlah - NEW.jumlah);
                ELSE
                    UPDATE inventaris SET inventaris.jumlah = (inventaris.jumlah + NEW.jumlah);
                END IF;
            END
        ');
    }
}
