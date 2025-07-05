<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PendaftaranBimbinganSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // Untuk dosen_id 1 (jadwal_bimbingan_id 1-5)
        for ($i = 1; $i <= 5; $i++) {
            DB::table('pendaftaran_bimbingan')->insert([
                'mahasiswa_id' => 1,
                'jadwal_bimbingan_id' => $i,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // Untuk dosen_id 2 (jadwal_bimbingan_id 6-10)
        for ($i = 6; $i <= 10; $i++) {
            DB::table('pendaftaran_bimbingan')->insert([
                'mahasiswa_id' => 1,
                'jadwal_bimbingan_id' => $i,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
