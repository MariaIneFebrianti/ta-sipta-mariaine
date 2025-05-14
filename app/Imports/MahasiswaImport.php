<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Mahasiswa;
use App\Models\ProgramStudi;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\ToCollection;

class MahasiswaImport implements ToCollection
{
    /**
     * @param Collection $collection
     */

    public function collection(Collection $collection)
    {
        try {
            DB::transaction(function () use ($collection) {
                foreach ($collection as $key => $row) {
                    // Skip header row
                    if ($key === 0) continue;

                    // Cek apakah nilai program_studi adalah angka (ID) atau nama
                    $programStudiId = null;

                    // Jika nilai di kolom $row[6] adalah angka, anggap itu adalah ID
                    if (is_numeric($row[6])) {
                        // Cek apakah ID yang diberikan ada di database
                        $programStudiId = ProgramStudi::find($row[6]) ? $row[6] : null;
                    } else {
                        // Jika tidak, anggap itu adalah nama program studi dan cari ID-nya
                        $programStudi = ProgramStudi::where('nama_prodi', $row[6])->first();
                        $programStudiId = $programStudi ? $programStudi->id : null;
                    }

                    // Buat user baru
                    $user = User::create([
                        'name' => $row[0],
                        'email' => $row[1],
                        'email_verified_at' => now(),
                        'password' => Hash::make('11111111'),
                        'role' => 'Mahasiswa',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    // Simpan data mahasiswa dengan user_id dari user yang baru dibuat
                    Mahasiswa::create([
                        'user_id' => $user->id,
                        'nama_mahasiswa' => $row[0],
                        'nim' => $row[2],
                        'tempat_lahir' => $row[3],
                        'tanggal_lahir' => $row[4],
                        'jenis_kelamin' => $row[5],
                        'program_studi_id' => $programStudiId,  // Simpan program_studi_id
                        'tahun_ajaran_id' => $row[7],
                    ]);
                }
            });

            session()->flash('success', 'Data mahasiswa berhasil diimpor.');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal mengimpor data mahasiswa: ' . $e->getMessage());
        }
    }


    // public function collection(Collection $collection)
    // {
    //     try {
    //         DB::transaction(function () use ($collection) {
    //             foreach ($collection as $key => $row) {
    //                 // Skip header row
    //                 if ($key === 0) continue;

    //                 // Buat user baru
    //                 $user = User::create([
    //                     'name' => $row[0],
    //                     'email' => $row[1],
    //                     'email_verified_at' => now(),
    //                     'password' => Hash::make('11111111'),
    //                     'role' => 'Mahasiswa',
    //                     'created_at' => now(),
    //                     'updated_at' => now(),
    //                 ]);

    //                 // Simpan data mahasiswa dengan user_id dari user yang baru dibuat
    //                 Mahasiswa::create([
    //                     'user_id' => $user->id,
    //                     'nama_mahasiswa' => $row[0],
    //                     'nim' => $row[2],
    //                     'tempat_lahir' => $row[3],
    //                     'tanggal_lahir' => $row[4],
    //                     'jenis_kelamin' => $row[5],
    //                     'program_studi_id' => $row[6],
    //                     'tahun_ajaran_id' => $row[7],
    //                 ]);
    //             }
    //         });

    //         session()->flash('success', 'Data mahasiswa berhasil diimpor.');
    //     } catch (\Exception $e) {
    //         session()->flash('error', 'Gagal mengimpor data mahasiswa: ' . $e->getMessage());
    //     }
    // }


    // public function collection(Collection $collection)
    // {
    //     foreach ($collection as $key => $row) {
    //         // Skip header row
    //         if ($key === 0) continue;

    //         try {
    //             // Buat user baru
    //             $user = User::create([
    //                 'name' => $row[0],
    //                 'email' => $row[1],
    //                 'email_verified_at' => now(),
    //                 'password' => Hash::make('11111111'),
    //                 'role' => 'Mahasiswa',
    //                 'created_at' => now(),
    //                 'updated_at' => now(),
    //             ]);

    //             // Simpan data mahasiswa dengan user_id dari user yang baru dibuat
    //             Mahasiswa::create([
    //                 'user_id' => $user->id,
    //                 'nama_mahasiswa' => $row[0],
    //                 'nim' => $row[2],
    //                 'tempat_lahir' => $row[3],
    //                 'tanggal_lahir' => $row[4],
    //                 'jenis_kelamin' => $row[5],
    //                 'program_studi_id' => $row[6],
    //                 'tahun_ajaran_id' => $row[7],
    //             ]);
    //         } catch (\Exception $e) {
    //             // Jika terjadi kesalahan, tampilkan pesan error
    //             session()->flash('error', 'Gagal mengimpor data mahasiswa: ' . $e->getMessage());
    //         }
    //     }

    //     session()->flash('success', 'Data mahasiswa berhasil diimpor.');
    // }
}
