<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserLevelSeeder extends Seeder
{
    protected $userLevels = ["administrator", "operator", "peminjam"];
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->userLevels as $level ) {
            DB::table("levels")->insert([
                'nama_level' => $level
            ]); 
        }
    }
}
