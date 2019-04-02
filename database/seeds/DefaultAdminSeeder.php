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
        // Admin
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

        $defaultToken = Str::random(60);
        // Petugas
        DB::table('users')->insert([
            'name' => "Petugas",
            'email' => "petugas@here.com",
            'username' => "petugas",
            'password' => Hash::make("petugas"),
            'api_token' => hash('sha256', $defaultToken),
            'id_level' => 2
        ]);
        DB::table('petugas')->insert([
            'nama_petugas' => "Petugas",
            'username' => "petugas",
            'password' => Hash::make("petugas"),
            'id_level' => 2
        ]);

        $defaultToken = Str::random(60);
        // Pegawai
        DB::table('users')->insert([
            'name' => "Pegawai",
            'email' => "pegawai@here.com",
            'username' => "12345678234561123",
            'password' => Hash::make("pegawai"),
            'api_token' => hash('sha256', $defaultToken),
            'id_level' => 3
        ]);
        DB::table('pegawais')->insert([
            'id_pegawai' => 3,
            'nama_pegawai' => "Pegawai",
            'nip' => "12345678234561123",
            'alamat' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit.
                Officia rem non nesciunt quae a reiciendis ab cum tempore quas possimus neque ad minus blanditiis corporis,
                ipsam tenetur maxime magnam ducimus.'
        ]);
    }
}
