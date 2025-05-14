<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::updateOrCreate([
            'email' => 'admin@gmail.com'
        ], [
            'first_name' => 'Admin',
            'last_name' => 'admin',
            'email'=>'admin@gmail.com',
            'password' => bcrypt('admin123'),
            'role' => 'admin'
        ]);

        User::updateOrCreate([
            'email' => 'anangpraf04@gmail.com'
        ], [
            'first_name' => 'Anang',
            'last_name' => 'Praf',
            'email' => 'anangpraf04@gmail.com',
            'password' => bcrypt('anangpraf123'),
            'role' => 'admin' 
        ]);
    }
}
