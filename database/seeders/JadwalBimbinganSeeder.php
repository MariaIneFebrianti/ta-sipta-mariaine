<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class JadwalBimbinganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('jadwal_bimbingan')->insert([
            [
                'dosen_id' => 1,
                'tanggal' => '2025-05-15',
                'waktu' => '08:00:00',
                'kuota' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'dosen_id' => 1,
                'tanggal' => '2025-05-16',
                'waktu' => '10:00:00',
                'kuota' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'dosen_id' => 1,
                'tanggal' => '2025-06-06',
                'waktu' => '14:00:00',
                'kuota' => 8,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'dosen_id' => 2,
                'tanggal' => '2025-06-05',
                'waktu' => '07:00:00',
                'kuota' => 6,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'dosen_id' => 2,
                'tanggal' => '2025-06-6',
                'waktu' => '09:00:00',
                'kuota' => 6,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
