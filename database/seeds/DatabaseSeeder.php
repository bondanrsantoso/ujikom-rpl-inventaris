<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

        // $this->call(StoredProcedure::class);
        $this->call(UserLevelSeeder::class);
        if(env("APP_DEBUG")){
            $this->call(DefaultAdminSeeder::class);
        }
    }
}
