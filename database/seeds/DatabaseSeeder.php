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
        //シードファイル呼び出し処理
        // $this->call(UsersTableSeeder::class);
        $this->call(SubjectsTableSeeder::class);

    }
}
