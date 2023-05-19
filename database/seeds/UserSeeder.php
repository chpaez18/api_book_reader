<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $userAdmin = User::create([
            'first_name' => 'Administrator',
            'username' => 'admin',
            'email' => 'admin@testing.com',
            'password' => bcrypt('clave123'),
            'status' => 1,
        ]);

        $userAdmin->assignRole('Admin');
    }
}
