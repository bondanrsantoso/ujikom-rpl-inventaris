<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DefaultAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => "Admin",
            'email' => "admin@here.com",
            'username' => "admin",
            'password' => bcrypt("admin"),
            'id_level' => 1
        ]);
    }
}
