<?php

namespace App\Imports;

use App\Models\Dosen;
use App\Models\JadwalSidangTugasAkhir;
use App\Models\Mahasiswa;
use App\Models\RuanganSidang;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;

class JadwalSidangTugasAkhirImport implements ToCollection
{
    /**
     * @param Collection $collection
     */

    public function collection(Collection $collection)
    {
        $skippedRows = [];
        $jadwalList = [];

        try {
            DB::transaction(function () use ($collection, &$skippedRows, &$jadwalList) {
                $now = now();
                foreach ($collection as $key => $row) {
                    if ($key === 0 || $row->filter()->isEmpty()) continue;

                    $baris = $key + 1;

                    try {
                        $namaMahasiswa = $row[0];
                        $jenisSidang = strtolower(trim($row[1]));

                        if (!in_array($jenisSidang, ['sidang reguler', 'sidang ulang'])) {
                            throw new \Exception("Jenis sidang tidak valid, isikan 'Sidang Reguler' atau 'Sidang Ulang'.");
                        }

                        $mahasiswaId = is_numeric($row[0])
                            ? Mahasiswa::find($row[0])?->id
                            : Mahasiswa::where('nama_mahasiswa', $row[0])->value('id');

                        if (!$mahasiswaId) {
                            throw new \Exception("Mahasiswa '{$row[0]}' tidak ditemukan.");
                        }

                        $exists = JadwalSidangTugasAkhir::where('mahasiswa_id', $mahasiswaId)
                            ->where('jenis_sidang', $jenisSidang)
                            ->exists();

                        if ($exists) {
                            throw new \Exception("Mahasiswa '{$namaMahasiswa}' sudah memiliki jadwal untuk jenis sidang '{$jenisSidang}'.");
                        }

                        // Ambil ID dosen
                        $pembimbingUtamaId = is_numeric($row[2]) ? Dosen::find($row[2])?->id : Dosen::where('nama_dosen', $row[2])->value('id');
                        $pembimbingPendampingId = is_numeric($row[3]) ? Dosen::find($row[3])?->id : Dosen::where('nama_dosen', $row[3])->value('id');
                        $pengujiUtamaId = is_numeric($row[4]) ? Dosen::find($row[4])?->id : Dosen::where('nama_dosen', $row[4])->value('id');
                        $pengujiPendampingId = is_numeric($row[5]) ? Dosen::find($row[5])?->id : Dosen::where('nama_dosen', $row[5])->value('id');

                        if (count([$pembimbingUtamaId, $pembimbingPendampingId, $pengujiUtamaId, $pengujiPendampingId]) !== count(array_unique([$pembimbingUtamaId, $pembimbingPendampingId, $pengujiUtamaId, $pengujiPendampingId]))) {
                            throw new \Exception("Dosen tidak boleh sama di kategori berbeda.");
                        }

                        // Konversi tanggal
                        try {
                            $tanggal = is_numeric($row[6])
                                ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[6])->format('Y-m-d')
                                : \Carbon\Carbon::parse($row[6])->format('Y-m-d');
                        } catch (\Exception $e) {
                            throw new \Exception("Format tanggal tidak valid: {$row[6]}");
                        }

                        // Konversi waktu
                        try {
                            $waktuMulai = is_numeric($row[7])
                                ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[7])->format('H:i:s')
                                : \Carbon\Carbon::parse($row[7])->format('H:i:s');

                            $waktuSelesai = is_numeric($row[8])
                                ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[8])->format('H:i:s')
                                : \Carbon\Carbon::parse($row[8])->format('H:i:s');
                        } catch (\Exception $e) {
                            throw new \Exception("Format waktu tidak valid: {$row[7]} - {$row[8]}");
                        }

                        // Ruangan
                        $ruanganSidangId = is_numeric($row[9])
                            ? RuanganSidang::find($row[9])?->id
                            : RuanganSidang::where('nama_ruangan', $row[9])->value('id');

                        if (!$ruanganSidangId) {
                            throw new \Exception("Ruangan '{$row[9]}' tidak ditemukan.");
                        }

                        // Cek bentrok antar baris file
                        foreach ($jadwalList as $prev) {
                            if (
                                $prev['tanggal'] === $tanggal &&
                                $prev['waktu_mulai'] === $waktuMulai &&
                                $prev['jenis_sidang'] === $jenisSidang
                            ) {
                                if ($prev['ruangan_sidang_id'] === $ruanganSidangId) {
                                    throw new \Exception("Bentrok ruangan dengan '{$prev['nama_mahasiswa']}' pada {$tanggal} {$waktuMulai}");
                                }

                                $dosen1 = [$pembimbingUtamaId, $pembimbingPendampingId, $pengujiUtamaId, $pengujiPendampingId];
                                $dosen2 = [$prev['pembimbing_utama_id'], $prev['pembimbing_pendamping_id'], $prev['penguji_utama_id'], $prev['penguji_pendamping_id']];
                                if (array_intersect($dosen1, $dosen2)) {
                                    throw new \Exception("Bentrok dosen dengan '{$prev['nama_mahasiswa']}' pada {$tanggal} {$waktuMulai}");
                                }
                            }
                        }

                        $jadwalList[] = [
                            'baris_ke' => $baris,
                            'mahasiswa_id' => $mahasiswaId,
                            'nama_mahasiswa' => $namaMahasiswa,
                            'pembimbing_utama_id' => $pembimbingUtamaId,
                            'pembimbing_pendamping_id' => $pembimbingPendampingId,
                            'penguji_utama_id' => $pengujiUtamaId,
                            'penguji_pendamping_id' => $pengujiPendampingId,
                            'tanggal' => $tanggal,
                            'waktu_mulai' => $waktuMulai,
                            'waktu_selesai' => $waktuSelesai,
                            'ruangan_sidang_id' => $ruanganSidangId,
                            'jenis_sidang' => $jenisSidang,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    } catch (\Exception $e) {
                        $skippedRows[] = "Baris {$baris}: " . $e->getMessage();
                        continue;
                    }
                }

                foreach ($jadwalList as $jadwal) {
                    unset($jadwal['baris_ke'], $jadwal['nama_mahasiswa']);
                    JadwalSidangTugasAkhir::create($jadwal);
                }
            });

            if (!empty($skippedRows)) {
                session()->flash('error', 'Beberapa baris gagal diimpor:<br>' . implode('<br>', $skippedRows));
            } else {
                session()->flash('success', 'Semua data jadwal sidang tugas akhir berhasil diimpor.');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal mengimpor data: ' . $e->getMessage());
        }
    }


    // public function collection(Collection $collection)
    // {
    //     try {
    //         DB::transaction(function () use ($collection) {
    //             $jadwalList = [];

    //             foreach ($collection as $key => $row) {
    //                 // Lewati baris header dan kosong
    //                 if ($key === 0 || $row->filter()->isEmpty()) continue;

    //                 // $barisExcel = $key + 1; // Ini baris sebenarnya di Excel
    //                 $namaMahasiswa = $row[0];
    //                 $jenisSidang = trim($row[1]);

    //                 // Validasi jenis sidang
    //                 if (!in_array($jenisSidang, ['Sidang Reguler', 'Sidang Ulang'])) {
    //                     throw new \Exception("Jenis sidang tidak valid, isikan 'Sidang Reguler' atau 'Sidang Ulang'.");
    //                 }

    //                 // Ambil ID mahasiswa
    //                 $mahasiswaId = null;
    //                 if (is_numeric($row[0])) {
    //                     $mahasiswaId = Mahasiswa::find($row[0])?->id;
    //                 } else {
    //                     $mahasiswa = Mahasiswa::where('nama_mahasiswa', $row[0])->first();
    //                     $mahasiswaId = $mahasiswa?->id;
    //                 }

    //                 if (!$mahasiswaId) {
    //                     throw new \Exception("Mahasiswa '{$row[0]}' tidak ditemukan, pastikan mahasiswa sudah ada didatabase");
    //                 }

    //                 // ❗ Cek apakah mahasiswa sudah punya jadwal dengan jenis sidang ini
    //                 $exists = JadwalSidangTugasAkhir::where('mahasiswa_id', $mahasiswaId)
    //                     ->where('jenis_sidang', strtolower($jenisSidang))
    //                     ->exists();

    //                 if ($exists) {
    //                     throw new \Exception("Mahasiswa '{$namaMahasiswa}' sudah memiliki jadwal untuk jenis sidang '{$jenisSidang}'.");
    //                 }

    //                 // Ambil ID dosen dan ruangan
    //                 $pembimbingUtamaId = is_numeric($row[2]) ? Dosen::find($row[2])?->id : Dosen::where('nama_dosen', $row[2])->first()?->id;
    //                 $pembimbingPendampingId = is_numeric($row[3]) ? Dosen::find($row[3])?->id : Dosen::where('nama_dosen', $row[3])->first()?->id;
    //                 $pengujiUtamaId = is_numeric($row[4]) ? Dosen::find($row[4])?->id : Dosen::where('nama_dosen', $row[4])->first()?->id;
    //                 $pengujiPendampingId = is_numeric($row[5]) ? Dosen::find($row[5])?->id : Dosen::where('nama_dosen', $row[5])->first()?->id;
    //                 $ruanganSidangId = is_numeric($row[9]) ? RuanganSidang::find($row[9])?->id : RuanganSidang::where('nama_ruangan', $row[9])->first()?->id;

    //                 $jadwalList[] = [
    //                     'mahasiswa_id' => $mahasiswaId,
    //                     'nama_mahasiswa' => $namaMahasiswa,
    //                     'pembimbing_utama_id' => $pembimbingUtamaId,
    //                     'pembimbing_pendamping_id' => $pembimbingPendampingId,
    //                     'penguji_utama_id' => $pengujiUtamaId,
    //                     'penguji_pendamping_id' => $pengujiPendampingId,
    //                     'tanggal' => $row[6],
    //                     'waktu_mulai' => $row[7],
    //                     'waktu_selesai' => $row[8],
    //                     'ruangan_sidang_id' => $ruanganSidangId,
    //                     'jenis_sidang' => strtolower($jenisSidang),
    //                 ];
    //             }

    //             // VALIDASI 1-2-3
    //             $validasiPoin1 = true;
    //             $validasiPoin2 = true;
    //             $validasiPoin3 = true;
    //             $errorMessages = [];

    //             foreach ($jadwalList as $index1 => $jadwal1) {
    //                 // ✅ POIN 1
    //                 $dosenUnik = [
    //                     $jadwal1['pembimbing_utama_id'],
    //                     $jadwal1['pembimbing_pendamping_id'],
    //                     $jadwal1['penguji_utama_id'],
    //                     $jadwal1['penguji_pendamping_id'],
    //                 ];
    //                 if (count($dosenUnik) !== count(array_unique($dosenUnik))) {
    //                     $validasiPoin1 = false;
    //                     $errorMessages[] = "Poin 1 gagal di baris {$jadwal1['nama_mahasiswa']}: Dosen tidak boleh sama dalam kategori berbeda.";
    //                 }

    //                 foreach ($jadwalList as $index2 => $jadwal2) {
    //                     if ($index1 === $index2) continue;

    //                     // ✅ POIN 2 & 3 jika jenis_sidang, tanggal, waktu sama
    //                     if (
    //                         $jadwal1['jenis_sidang'] === $jadwal2['jenis_sidang'] &&
    //                         $jadwal1['tanggal'] === $jadwal2['tanggal'] &&
    //                         $jadwal1['waktu_mulai'] === $jadwal2['waktu_mulai']
    //                     ) {
    //                         // ✅ POIN 2: Tidak boleh ruangan sama
    //                         if ($jadwal1['ruangan_sidang_id'] === $jadwal2['ruangan_sidang_id']) {
    //                             $validasiPoin2 = false;
    //                             $errorMessages[] = "Poin 2 gagal: Mahasiswa {$jadwal1['nama_mahasiswa']} dan {$jadwal2['nama_mahasiswa']} memiliki ruangan, tanggal, dan waktu yang sama.";
    //                         }

    //                         // ✅ POIN 3: Dosen tidak boleh bentrok di waktu yang sama
    //                         $dosen1 = [
    //                             $jadwal1['pembimbing_utama_id'],
    //                             $jadwal1['pembimbing_pendamping_id'],
    //                             $jadwal1['penguji_utama_id'],
    //                             $jadwal1['penguji_pendamping_id'],
    //                         ];
    //                         $dosen2 = [
    //                             $jadwal2['pembimbing_utama_id'],
    //                             $jadwal2['pembimbing_pendamping_id'],
    //                             $jadwal2['penguji_utama_id'],
    //                             $jadwal2['penguji_pendamping_id'],
    //                         ];
    //                         if (array_intersect($dosen1, $dosen2)) {
    //                             $validasiPoin3 = false;
    //                             $errorMessages[] = "Poin 3 gagal: Dosen dari {$jadwal1['nama_mahasiswa']} dan {$jadwal2['nama_mahasiswa']} bentrok di tanggal dan waktu yang sama.";
    //                         }
    //                     }
    //                 }
    //             }

    //             if ($validasiPoin1 && $validasiPoin2 && $validasiPoin3) {
    //                 foreach ($jadwalList as $jadwal) {
    //                     unset($jadwal['nama_mahasiswa']);
    //                     JadwalSidangTugasAkhir::create($jadwal);
    //                 }
    //                 session()->flash('success', 'Data jadwal sidang tugas akhir berhasil diimpor.');
    //             } else {
    //                 throw new \Exception(implode(' ', array_unique($errorMessages)));
    //             }
    //         });
    //     } catch (\Exception $e) {
    //         session()->flash('error', 'Gagal mengimpor: ' . $e->getMessage());
    //     }
    // }
}
