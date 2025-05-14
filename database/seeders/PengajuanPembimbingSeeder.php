<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PengajuanPembimbingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('pengajuan_pembimbing')->insert([
            [
                'mahasiswa_id' => 1,
                'pembimbing_utama_id' => 1,
                'pembimbing_pendamping_id' => 2,
                'validasi' => 'Menunggu',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'mahasiswa_id' => 2,
                'pembimbing_utama_id' => 3,
                'pembimbing_pendamping_id' => 4,
                'validasi' => 'Acc',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
