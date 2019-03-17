<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DefaultAdminSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $defaultToken = Str::random(60);
        
        DB::table('users')->insert([
            'name' => "Admin",
            'email' => "admin@here.com",
            'username' => "admin",
            'password' => Hash::make("admin"),
            'api_token' => hash('sha256', $defaultToken),
            'id_level' => 1
        ]);
        DB::table('petugas')->insert([
            'nama_petugas' => "Admin",
            'username' => "admin",
            'password' => Hash::make("admin"),
            'id_level' => 1
        ]);
    }
}
