<?php

use Illuminate\Database\Seeder;
use Database\Seeders\{
    PermissionRoleSeeder,
    UserSeeder,
    BookSeeder,
    QuotesSeeder
};
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(BookSeeder::class);
        $this->call(PermissionRoleSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(QuotesSeeder::class);
    }

}
