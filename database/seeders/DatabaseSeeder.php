<?php

namespace Database\Seeders;

use App\Models\RuanganSidang;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call([
            ProgramStudiSeeder::class,
            UserSeeder::class,
            RuanganSidangSeeder::class,
            TahunAjaranSeeder::class,
            MahasiswaSeeder::class,
            DosenSeeder::class,
        ]);
    }
}
