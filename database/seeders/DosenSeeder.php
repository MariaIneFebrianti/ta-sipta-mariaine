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
                'user_id' => 11,
                'nama_dosen' => 'Prih Diantono',
                'nip' => 1001,
                'tempat_lahir' => 'Jakarta',
                'tanggal_lahir' => '1980-01-01',
                'jenis_kelamin' => 'Laki-laki',
                'jabatan' => 'Dosen Biasa',
                'program_studi_id' => null,
                'ttd_dosen' => 'ttd_dosen/ttd_prih.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 12,
                'nama_dosen' => 'Cahya Vikasari',
                'nip' => 1002,
                'tempat_lahir' => 'Bandung',
                'tanggal_lahir' => '1985-02-02',
                'jenis_kelamin' => 'Perempuan',
                'jabatan' => 'Koordinator Program Studi',
                'program_studi_id' => 1,
                'ttd_dosen' => 'ttd_dosen/ttd_cahya.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 13,
                'nama_dosen' => 'Lutfi Syafirullah',
                'nip' => 1003,
                'tempat_lahir' => 'Surabaya',
                'tanggal_lahir' => '1990-03-03',
                'jenis_kelamin' => 'Laki-laki',
                'jabatan' => 'Dosen Biasa',
                'program_studi_id' => null,
                'ttd_dosen' => 'ttd_dosen/ttd_lutfi.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 14,
                'nama_dosen' => 'Abdul Rohman',
                'nip' => 1004,
                'tempat_lahir' => 'Medan',
                'tanggal_lahir' => '1988-04-04',
                'jenis_kelamin' => 'Laki-laki',
                'jabatan' => 'Koordinator Program Studi',
                'program_studi_id' => 2,
                'ttd_dosen' => 'ttd_dosen/ttd_abdul.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 15,
                'nama_dosen' => 'Nur Wachid',
                'nip' => 1005,
                'tempat_lahir' => 'Yogyakarta',
                'tanggal_lahir' => '1992-05-05',
                'jenis_kelamin' => 'Laki-laki',
                'jabatan' => 'Koordinator Program Studi',
                'program_studi_id' => 3,
                'ttd_dosen' => 'ttd_dosen/ttd_nur.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 16,
                'nama_dosen' => 'Faidzin Firdhaus',
                'nip' => 1006,
                'tempat_lahir' => 'Semarang',
                'tanggal_lahir' => '1983-06-06',
                'jenis_kelamin' => 'Laki-laki',
                'jabatan' => 'Koordinator Program Studi',
                'program_studi_id' => 4,
                'ttd_dosen' => 'ttd_dosen/ttd_faidzin.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 17,
                'nama_dosen' => 'Antonius Agung',
                'nip' => 1007,
                'tempat_lahir' => 'Bali',
                'tanggal_lahir' => '1986-07-07',
                'jenis_kelamin' => 'Laki-laki',
                'jabatan' => 'Dosen Biasa',
                'program_studi_id' => null,
                'ttd_dosen' => 'ttd_dosen/ttd_anton.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 18,
                'nama_dosen' => 'Fajar Mahardika',
                'nip' => 1008,
                'tempat_lahir' => 'Cilacap',
                'tanggal_lahir' => '1989-07-07',
                'jenis_kelamin' => 'Laki-laki',
                'jabatan' => 'Koordinator Program Studi',
                'program_studi_id' => 5,
                'ttd_dosen' => 'ttd_dosen/ttd_fajar.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 19,
                'nama_dosen' => 'Dwi Novia',
                'nip' => 1009,
                'tempat_lahir' => 'Purwokerto',
                'tanggal_lahir' => '1991-06-01',
                'jenis_kelamin' => 'Perempuan',
                'jabatan' => 'Super Admin',
                'program_studi_id' => null,
                'ttd_dosen' => 'ttd_dosen/ttd_dwi.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 20,
                'nama_dosen' => 'Isa Bahroni',
                'nip' => 1010,
                'tempat_lahir' => 'Pekalongan',
                'tanggal_lahir' => '1987-08-08',
                'jenis_kelamin' => 'Laki-laki',
                'jabatan' => 'Dosen Biasa',
                'program_studi_id' => null,
                'ttd_dosen' => 'ttd_dosen/ttd_isa.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],

        ]);
    }
}
