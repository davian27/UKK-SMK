<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            "name" => "Muizzz",
            "email" => "admin@gmail.com",
            "phone" => "0897654321",
            "password" => "12345678",
        ])->assignRole('admin');
        User::create([
            "name" => "Raissa",
            "email" => "raissa@gmail.com",
            "phone" => "08978187187",
            "password" => "12345678",
        ])->assignRole('customer');

       
    }
}
