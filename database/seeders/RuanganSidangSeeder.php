<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RuanganSidangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('ruangan_sidang')->insert([
            [
                'program_studi_id' => '1',
                'nama_ruangan' => 'Lab. Jaringan Komputer'
            ],
            [
                'program_studi_id' => '1',
                'nama_ruangan' => 'Lab. Sistem Informasi'
            ],
            [
                'program_studi_id' => '1',
                'nama_ruangan' => 'Lab. Pemrograman Dasar'
            ],
            [
                'program_studi_id' => '2',
                'nama_ruangan' => 'Lab. Keamanan Jaringan'
            ],
            [
                'program_studi_id' => '3',
                'nama_ruangan' => 'Lab. Multimedia'
            ],
            [
                'program_studi_id' => '3',
                'nama_ruangan' => 'Lab. Desain Komunikasi Visual'
            ],
        ]);
    }
}
