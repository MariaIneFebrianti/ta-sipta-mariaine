<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class DosenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('dosen')->insert([
            [
                'user_id' => 6,
                'nama_dosen' => 'Prih Diantono',
                'nip' => 1001,
                'tempat_lahir' => 'Jakarta',
                'tanggal_lahir' => '1980-01-01',
                'jenis_kelamin' => 'Laki-laki',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 7,
                'nama_dosen' => 'Cahya Vikasari',
                'nip' => 1002,
                'tempat_lahir' => 'Bandung',
                'tanggal_lahir' => '1985-02-02',
                'jenis_kelamin' => 'Perempuan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 8,
                'nama_dosen' => 'Lutfi Syafirullah',
                'nip' => 1003,
                'tempat_lahir' => 'Surabaya',
                'tanggal_lahir' => '1990-03-03',
                'jenis_kelamin' => 'Laki-laki',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 9,
                'nama_dosen' => 'Nur Wahyu',
                'nip' => 1004,
                'tempat_lahir' => 'Medan',
                'tanggal_lahir' => '1988-04-04',
                'jenis_kelamin' => 'Laki-laki',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 10,
                'nama_dosen' => 'Laura Sari',
                'nip' => 1005,
                'tempat_lahir' => 'Yogyakarta',
                'tanggal_lahir' => '1992-05-05',
                'jenis_kelamin' => 'Perempuan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 11,
                'nama_dosen' => 'Annas Setiawan',
                'nip' => 1006,
                'tempat_lahir' => 'Semarang',
                'tanggal_lahir' => '1983-06-06',
                'jenis_kelamin' => 'Laki-laki',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 12,
                'nama_dosen' => 'Antonius Agung',
                'nip' => 1007,
                'tempat_lahir' => 'Bali',
                'tanggal_lahir' => '1986-07-07',
                'jenis_kelamin' => 'Laki-laki',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 13,
                'nama_dosen' => 'Fajar Mahardika',
                'nip' => 1008,
                'tempat_lahir' => 'Cilacap',
                'tanggal_lahir' => '1989-07-07',
                'jenis_kelamin' => 'Laki-laki',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
