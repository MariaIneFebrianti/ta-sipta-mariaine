<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Dosen;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;

class DosenImport implements ToCollection
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

                    // Buat user baru
                    $user = User::create([
                        'name' => $row[0],
                        'email' => $row[1],
                        'email_verified_at' => now(),
                        'password' => Hash::make('11111111'),
                        'role' => 'Dosen',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    // Simpan data dosen dengan user_id dari user yang baru dibuat
                    Dosen::create([
                        'user_id' => $user->id,
                        'nama_dosen' => $row[0],
                        'nip' => $row[2],
                        'tempat_lahir' => $row[3],
                        'tanggal_lahir' => $row[4],
                        'jenis_kelamin' => $row[5],
                    ]);
                }
            });

            session()->flash('success', 'Data dosen berhasil diimpor.');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal mengimpor data dosen: ' . $e->getMessage());
        }
    }
}
