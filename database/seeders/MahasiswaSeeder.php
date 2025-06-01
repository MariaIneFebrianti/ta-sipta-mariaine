<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class MahasiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('mahasiswa')->insert([
            [
                'user_id' => 1,
                'nama_mahasiswa' => 'Maria Ine',
                'nim' => 220202001,
                'tempat_lahir' => 'Kebumen',
                'tanggal_lahir' => '2000-01-01',
                'jenis_kelamin' => 'Perempuan',
                'program_studi_id' => 1,
                'tahun_ajaran_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 2,
                'nama_mahasiswa' => 'Puput Era',
                'nim' => 220202002,
                'tempat_lahir' => 'Cilacap',
                'tanggal_lahir' => '2000-02-02',
                'jenis_kelamin' => 'Perempuan',
                'program_studi_id' => 1,
                'tahun_ajaran_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 3,
                'nama_mahasiswa' => 'Rayhan Afrizal',
                'nim' => 220202003,
                'tempat_lahir' => 'Banyumas',
                'tanggal_lahir' => '2000-03-03',
                'jenis_kelamin' => 'Laki-laki',
                'program_studi_id' => 1,
                'tahun_ajaran_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 4,
                'nama_mahasiswa' => 'Yefta Charrand',
                'nim' => 220202004,
                'tempat_lahir' => 'Cilacap',
                'tanggal_lahir' => '2000-02-04',
                'jenis_kelamin' => 'Laki-laki',
                'program_studi_id' => 2,
                'tahun_ajaran_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 5,
                'nama_mahasiswa' => 'Gita Listiani',
                'nim' => 220202005,
                'tempat_lahir' => 'Cilacap',
                'tanggal_lahir' => '2000-02-05',
                'jenis_kelamin' => 'Perempuan',
                'program_studi_id' => 1,
                'tahun_ajaran_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
