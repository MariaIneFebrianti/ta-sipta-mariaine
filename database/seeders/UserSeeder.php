<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'name' => 'Maria Ine',
                'email' => 'maria@gmail.com',
                'email_verified_at' => now(),
                'password' => Hash::make('11111111'),
                'role' => 'Mahasiswa',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Puput Era',
                'email' => 'puput@gmail.com',
                'email_verified_at' => now(),
                'password' => Hash::make('11111111'),
                'role' => 'Mahasiswa',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Rayhan Afrizal',
                'email' => 'rayhan@gmail.com',
                'email_verified_at' => now(),
                'password' => Hash::make('11111111'),
                'role' => 'Mahasiswa',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Yefta Charand',
                'email' => 'yefta@gmail.com',
                'email_verified_at' => now(),
                'password' => Hash::make('11111111'),
                'role' => 'Mahasiswa',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Gita Listiani',
                'email' => 'gita@gmail.com',
                'email_verified_at' => now(),
                'password' => Hash::make('11111111'),
                'role' => 'Mahasiswa',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Prih Diantono',
                'email' => 'prih@gmail.com',
                'email_verified_at' => now(),
                'password' => Hash::make('11111111'),
                'role' => 'Dosen',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Cahya Vikasari',
                'email' => 'cahya@gmail.com',
                'email_verified_at' => now(),
                'password' => Hash::make('11111111'),
                'role' => 'Dosen',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Lutfi Syafirullah',
                'email' => 'lutfi@gmail.com',
                'email_verified_at' => now(),
                'password' => Hash::make('11111111'),
                'role' => 'Dosen',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Nur Wahyu',
                'email' => 'nur@gmail.com',
                'email_verified_at' => now(),
                'password' => Hash::make('11111111'),
                'role' => 'Dosen',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Laura Sari',
                'email' => 'laura@gmail.com',
                'email_verified_at' => now(),
                'password' => Hash::make('11111111'),
                'role' => 'Dosen',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Annas Setiawan',
                'email' => 'annas@gmail.com',
                'email_verified_at' => now(),
                'password' => Hash::make('11111111'),
                'role' => 'Dosen',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Antonius Agung',
                'email' => 'antonius@gmail.com',
                'email_verified_at' => now(),
                'password' => Hash::make('11111111'),
                'role' => 'Koordinator Program Studi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
