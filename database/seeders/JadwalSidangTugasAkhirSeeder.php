<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JadwalSidangTugasAkhirSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('jadwal_sidang_tugas_akhir')->insert([
            [
                'mahasiswa_id' => 1,
                'jenis_sidang' => 'Sidang Reguler',
                'pembimbing_utama_id' => 1,
                'pembimbing_pendamping_id' => 2,
                'penguji_utama_id' => 5,
                'penguji_pendamping_id' => 4,
                'tanggal' => '2025-05-20',
                'waktu_mulai' => '08:00:00',
                'waktu_selesai' => '10:00:00',
                'ruangan_sidang_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'mahasiswa_id' => 2,
                'jenis_sidang' => 'Sidang Reguler',
                'pembimbing_utama_id' => 6,
                'pembimbing_pendamping_id' => 7,
                'penguji_utama_id' => 2,
                'penguji_pendamping_id' => 8,
                'tanggal' => '2025-05-20',
                'waktu_mulai' => '10:00:00',
                'waktu_selesai' => '12:00:00',
                'ruangan_sidang_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'mahasiswa_id' => 3,
                'jenis_sidang' => 'Sidang Reguler',
                'pembimbing_utama_id' => 2,
                'pembimbing_pendamping_id' => 3,
                'penguji_utama_id' => 1,
                'penguji_pendamping_id' => 5,
                'tanggal' => '2025-05-21',
                'waktu_mulai' => '10:00:00',
                'waktu_selesai' => '12:00:00',
                'ruangan_sidang_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'mahasiswa_id' => 4,
                'jenis_sidang' => 'Sidang Reguler',
                'pembimbing_utama_id' => 1,
                'pembimbing_pendamping_id' => 3,
                'penguji_utama_id' => 5,
                'penguji_pendamping_id' => 4,
                'tanggal' => '2025-05-22',
                'waktu_mulai' => '08:00:00',
                'waktu_selesai' => '10:00:00',
                'ruangan_sidang_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'mahasiswa_id' => 5,
                'jenis_sidang' => 'Sidang Reguler',
                'pembimbing_utama_id' => 6,
                'pembimbing_pendamping_id' => 7,
                'penguji_utama_id' => 2,
                'penguji_pendamping_id' => 8,
                'tanggal' => '2025-05-22',
                'waktu_mulai' => '08:00:00',
                'waktu_selesai' => '10:00:00',
                'ruangan_sidang_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
