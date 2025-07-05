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
    //  benar tanpa validasi jadwal
    // public function collection(Collection $collection)
    // {
    //     try {
    //         DB::transaction(function () use ($collection) {
    //             foreach ($collection as $key => $row) {
    //                 // Skip header row
    //                 if ($key === 0) continue;

    //                 // Cek ID mahasiswa
    //                 $mahasiswaId = null;
    //                 if (is_numeric($row[0])) {
    //                     $mahasiswaId = Mahasiswa::find($row[0]) ? $row[0] : null;
    //                 } else {
    //                     $mahasiswa = Mahasiswa::where('nama_mahasiswa', $row[0])->first();
    //                     $mahasiswaId = $mahasiswa ? $mahasiswa->id : null;
    //                 }

    //                 // Cek ID pembimbing utama
    //                 $pembimbingUtamaId = null;
    //                 if (is_numeric($row[1])) {
    //                     $pembimbingUtamaId = Dosen::find($row[1]) ? $row[1] : null;
    //                 } else {
    //                     $pembimbingUtama = Dosen::where('nama_dosen', $row[1])->first();
    //                     $pembimbingUtamaId = $pembimbingUtama ? $pembimbingUtama->id : null;
    //                 }

    //                 // Cek ID pembimbing pendamping
    //                 $pembimbingPendampingId = null;
    //                 if (is_numeric($row[2])) {
    //                     $pembimbingPendampingId = Dosen::find($row[2]) ? $row[2] : null;
    //                 } else {
    //                     $pembimbingPendamping = Dosen::where('nama_dosen', $row[2])->first();
    //                     $pembimbingPendampingId = $pembimbingPendamping ? $pembimbingPendamping->id : null;
    //                 }

    //                 // Cek ID penguji utama
    //                 $pengujiUtamaId = null;
    //                 if (is_numeric($row[3])) {
    //                     $pengujiUtamaId = Dosen::find($row[3]) ? $row[3] : null;
    //                 } else {
    //                     $pengujiUtama = Dosen::where('nama_dosen', $row[3])->first();
    //                     $pengujiUtamaId = $pengujiUtama ? $pengujiUtama->id : null;
    //                 }

    //                 // Cek ID penguji pendamping
    //                 $pengujiPendampingId = null;
    //                 if (is_numeric($row[4])) {
    //                     $pengujiPendampingId = Dosen::find($row[4]) ? $row[4] : null;
    //                 } else {
    //                     $pengujiPendamping = Dosen::where('nama_dosen', $row[4])->first();
    //                     $pengujiPendampingId = $pengujiPendamping ? $pengujiPendamping->id : null;
    //                 }

    //                 // Cek ID ruangan sidang
    //                 $ruanganSidangId = null;
    //                 if (is_numeric($row[8])) {
    //                     $ruanganSidangId = RuanganSidang::find($row[8]) ? $row[8] : null;
    //                 } else {
    //                     $ruanganSidang = RuanganSidang::where('nama_ruangan', $row[8])->first();
    //                     $ruanganSidangId = $ruanganSidang ? $ruanganSidang->id : null;
    //                 }

    //                 // Simpan data jadwal sidang hanya jika semua ID valid
    //                 if ($mahasiswaId && $pembimbingUtamaId && $pembimbingPendampingId && $pengujiUtamaId && $pengujiPendampingId && $ruanganSidangId) {
    //                     JadwalSidangTugasAkhir::create([
    //                         'mahasiswa_id' => $mahasiswaId,
    //                         'pembimbing_utama_id' => $pembimbingUtamaId,
    //                         'pembimbing_pendamping_id' => $pembimbingPendampingId,
    //                         'penguji_utama_id' => $pengujiUtamaId,
    //                         'penguji_pendamping_id' => $pengujiPendampingId,
    //                         'tanggal' => $row[5],
    //                         'waktu_mulai' => $row[6],
    //                         'waktu_selesai' => $row[7],
    //                         'ruangan_sidang_id' => $ruanganSidangId,
    //                     ]);
    //                 }
    //             }
    //         });

    //         session()->flash('success', 'Data jadwal sidang tugas akhir berhasil diimpor.');
    //     } catch (\Exception $e) {
    //         session()->flash('error', 'Gagal mengimpor data jadwal sidang tugas akhir: ' . $e->getMessage());
    //     }
    // }

    // public function collection(Collection $collection)
    // {
    //     try {
    //         DB::transaction(function () use ($collection) {
    //             $jadwalList = [];
    //             $baris = 1;

    //             foreach ($collection as $key => $row) {
    //                 $baris++;

    //                 // Lewati header dan baris kosong
    //                 if ($key === 0 || $row->filter()->isEmpty()) continue;

    //                 $namaMahasiswa = $row[0];
    //                 $jenisSidang = trim($row[1]);

    //                 // Validasi jenis sidang
    //                 if (!in_array($jenisSidang, ['Sidang Reguler', 'Sidang Ulang'])) {
    //                     throw new \Exception("Jenis sidang tidak valid di baris {$baris}.");
    //                 }

    //                 // Ambil ID mahasiswa
    //                 $mahasiswaId = null;
    //                 if (is_numeric($row[0])) {
    //                     $mahasiswaId = Mahasiswa::find($row[0]) ? $row[0] : null;
    //                 } else {
    //                     $mahasiswa = Mahasiswa::where('nama_mahasiswa', $row[0])->first();
    //                     $mahasiswaId = $mahasiswa ? $mahasiswa->id : null;
    //                 }

    //                 if (!$mahasiswaId) {
    //                     throw new \Exception("Mahasiswa tidak ditemukan di baris {$baris}.");
    //                 }

    //                 // Cek apakah mahasiswa sudah punya jadwal untuk jenis sidang ini
    //                 $exists = JadwalSidangTugasAkhir::where('mahasiswa_id', $mahasiswaId)
    //                     ->where('jenis_sidang', strtolower($jenisSidang))
    //                     ->exists();

    //                 if ($exists) {
    //                     throw new \Exception("Mahasiswa '{$namaMahasiswa}' sudah memiliki jadwal untuk jenis sidang '{$jenisSidang}' (baris {$baris}).");
    //                 }

    //                 // Ambil ID dosen dan ruangan
    //                 $pembimbingUtamaId = is_numeric($row[2]) ? Dosen::find($row[2])?->id : Dosen::where('nama_dosen', $row[2])->first()?->id;
    //                 $pembimbingPendampingId = is_numeric($row[3]) ? Dosen::find($row[3])?->id : Dosen::where('nama_dosen', $row[3])->first()?->id;
    //                 $pengujiUtamaId = is_numeric($row[4]) ? Dosen::find($row[4])?->id : Dosen::where('nama_dosen', $row[4])->first()?->id;
    //                 $pengujiPendampingId = is_numeric($row[5]) ? Dosen::find($row[5])?->id : Dosen::where('nama_dosen', $row[5])->first()?->id;
    //                 $ruanganSidangId = is_numeric($row[9]) ? RuanganSidang::find($row[9])?->id : RuanganSidang::where('nama_ruangan', $row[9])->first()?->id;

    //                 $jadwalList[] = [
    //                     'mahasiswa_id' => $mahasiswaId,
    //                     'pembimbing_utama_id' => $pembimbingUtamaId,
    //                     'pembimbing_pendamping_id' => $pembimbingPendampingId,
    //                     'penguji_utama_id' => $pengujiUtamaId,
    //                     'penguji_pendamping_id' => $pengujiPendampingId,
    //                     'tanggal' => $row[6],
    //                     'waktu_mulai' => $row[7],
    //                     'waktu_selesai' => $row[8],
    //                     'ruangan_sidang_id' => $ruanganSidangId,
    //                     'jenis_sidang' => strtolower($jenisSidang),
    //                     'baris' => $baris,
    //                     'nama_mahasiswa' => $namaMahasiswa,
    //                 ];
    //             }

    //             // Validasi poin 1, 2, 3
    //             $validasiPoin1 = true;
    //             $validasiPoin2 = true;
    //             $validasiPoin3 = true;

    //             $errorMessages = [];

    //             foreach ($jadwalList as $index1 => $jadwal1) {
    //                 // Poin 1: dosen dalam 1 jadwal tidak boleh sama
    //                 $dosenUnik = [
    //                     $jadwal1['pembimbing_utama_id'],
    //                     $jadwal1['pembimbing_pendamping_id'],
    //                     $jadwal1['penguji_utama_id'],
    //                     $jadwal1['penguji_pendamping_id'],
    //                 ];

    //                 if (count($dosenUnik) !== count(array_unique($dosenUnik))) {
    //                     $validasiPoin1 = false;
    //                     $errorMessages[] = "Poin 1 gagal di baris {$jadwal1['baris']}: Dosen tidak boleh sama dalam kategori yang berbeda.";
    //                 }

    //                 foreach ($jadwalList as $index2 => $jadwal2) {
    //                     if ($index1 === $index2) continue;

    //                     // Poin 2 dan 3 hanya jika jenis sidang sama
    //                     if (
    //                         $jadwal1['jenis_sidang'] === $jadwal2['jenis_sidang'] &&
    //                         $jadwal1['tanggal'] === $jadwal2['tanggal'] &&
    //                         $jadwal1['waktu_mulai'] === $jadwal2['waktu_mulai']
    //                     ) {
    //                         // Poin 2: bentrok ruangan
    //                         if ($jadwal1['ruangan_sidang_id'] === $jadwal2['ruangan_sidang_id']) {
    //                             $validasiPoin2 = false;
    //                             $errorMessages[] = "Poin 2 gagal: Mahasiswa {$jadwal1['nama_mahasiswa']} dan {$jadwal2['nama_mahasiswa']} memiliki tanggal, waktu, dan ruangan yang sama.";
    //                         }

    //                         // Poin 3: dosen bentrok di waktu sama meskipun ruang berbeda
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
    //                             $errorMessages[] = "Poin 3 gagal: Dosen dari mahasiswa {$jadwal1['nama_mahasiswa']} dan {$jadwal2['nama_mahasiswa']} bentrok di tanggal dan waktu yang sama.";
    //                         }
    //                     }
    //                 }
    //             }

    //             if ($validasiPoin1 && $validasiPoin2 && $validasiPoin3) {
    //                 foreach ($jadwalList as $jadwal) {
    //                     unset($jadwal['baris'], $jadwal['nama_mahasiswa']); // hapus info tambahan
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


    public function collection(Collection $collection)
    {
        try {
            DB::transaction(function () use ($collection) {
                $jadwalList = [];

                foreach ($collection as $key => $row) {
                    // Lewati baris header dan kosong
                    if ($key === 0 || $row->filter()->isEmpty()) continue;

                    // $barisExcel = $key + 1; // Ini baris sebenarnya di Excel
                    $namaMahasiswa = $row[0];
                    $jenisSidang = trim($row[1]);

                    // Validasi jenis sidang
                    if (!in_array($jenisSidang, ['Sidang Reguler', 'Sidang Ulang'])) {
                        throw new \Exception("Jenis sidang tidak valid, isikan 'Sidang Reguler' atau 'Sidang Ulang'.");
                    }

                    // Ambil ID mahasiswa
                    $mahasiswaId = null;
                    if (is_numeric($row[0])) {
                        $mahasiswaId = Mahasiswa::find($row[0])?->id;
                    } else {
                        $mahasiswa = Mahasiswa::where('nama_mahasiswa', $row[0])->first();
                        $mahasiswaId = $mahasiswa?->id;
                    }

                    if (!$mahasiswaId) {
                        throw new \Exception("Mahasiswa '{$row[0]}' tidak ditemukan, pastikan mahasiswa sudah ada didatabase");
                    }

                    // ❗ Cek apakah mahasiswa sudah punya jadwal dengan jenis sidang ini
                    $exists = JadwalSidangTugasAkhir::where('mahasiswa_id', $mahasiswaId)
                        ->where('jenis_sidang', strtolower($jenisSidang))
                        ->exists();

                    if ($exists) {
                        throw new \Exception("Mahasiswa '{$namaMahasiswa}' sudah memiliki jadwal untuk jenis sidang '{$jenisSidang}'.");
                    }

                    // Ambil ID dosen dan ruangan
                    $pembimbingUtamaId = is_numeric($row[2]) ? Dosen::find($row[2])?->id : Dosen::where('nama_dosen', $row[2])->first()?->id;
                    $pembimbingPendampingId = is_numeric($row[3]) ? Dosen::find($row[3])?->id : Dosen::where('nama_dosen', $row[3])->first()?->id;
                    $pengujiUtamaId = is_numeric($row[4]) ? Dosen::find($row[4])?->id : Dosen::where('nama_dosen', $row[4])->first()?->id;
                    $pengujiPendampingId = is_numeric($row[5]) ? Dosen::find($row[5])?->id : Dosen::where('nama_dosen', $row[5])->first()?->id;
                    $ruanganSidangId = is_numeric($row[9]) ? RuanganSidang::find($row[9])?->id : RuanganSidang::where('nama_ruangan', $row[9])->first()?->id;

                    $jadwalList[] = [
                        'mahasiswa_id' => $mahasiswaId,
                        'nama_mahasiswa' => $namaMahasiswa,
                        'pembimbing_utama_id' => $pembimbingUtamaId,
                        'pembimbing_pendamping_id' => $pembimbingPendampingId,
                        'penguji_utama_id' => $pengujiUtamaId,
                        'penguji_pendamping_id' => $pengujiPendampingId,
                        'tanggal' => $row[6],
                        'waktu_mulai' => $row[7],
                        'waktu_selesai' => $row[8],
                        'ruangan_sidang_id' => $ruanganSidangId,
                        'jenis_sidang' => strtolower($jenisSidang),
                    ];
                }

                // VALIDASI 1-2-3
                $validasiPoin1 = true;
                $validasiPoin2 = true;
                $validasiPoin3 = true;
                $errorMessages = [];

                foreach ($jadwalList as $index1 => $jadwal1) {
                    // ✅ POIN 1
                    $dosenUnik = [
                        $jadwal1['pembimbing_utama_id'],
                        $jadwal1['pembimbing_pendamping_id'],
                        $jadwal1['penguji_utama_id'],
                        $jadwal1['penguji_pendamping_id'],
                    ];
                    if (count($dosenUnik) !== count(array_unique($dosenUnik))) {
                        $validasiPoin1 = false;
                        $errorMessages[] = "Poin 1 gagal di baris {$jadwal1['nama_mahasiswa']}: Dosen tidak boleh sama dalam kategori berbeda.";
                    }

                    foreach ($jadwalList as $index2 => $jadwal2) {
                        if ($index1 === $index2) continue;

                        // ✅ POIN 2 & 3 jika jenis_sidang, tanggal, waktu sama
                        if (
                            $jadwal1['jenis_sidang'] === $jadwal2['jenis_sidang'] &&
                            $jadwal1['tanggal'] === $jadwal2['tanggal'] &&
                            $jadwal1['waktu_mulai'] === $jadwal2['waktu_mulai']
                        ) {
                            // ✅ POIN 2: Tidak boleh ruangan sama
                            if ($jadwal1['ruangan_sidang_id'] === $jadwal2['ruangan_sidang_id']) {
                                $validasiPoin2 = false;
                                $errorMessages[] = "Poin 2 gagal: Mahasiswa {$jadwal1['nama_mahasiswa']} dan {$jadwal2['nama_mahasiswa']} memiliki ruangan, tanggal, dan waktu yang sama.";
                            }

                            // ✅ POIN 3: Dosen tidak boleh bentrok di waktu yang sama
                            $dosen1 = [
                                $jadwal1['pembimbing_utama_id'],
                                $jadwal1['pembimbing_pendamping_id'],
                                $jadwal1['penguji_utama_id'],
                                $jadwal1['penguji_pendamping_id'],
                            ];
                            $dosen2 = [
                                $jadwal2['pembimbing_utama_id'],
                                $jadwal2['pembimbing_pendamping_id'],
                                $jadwal2['penguji_utama_id'],
                                $jadwal2['penguji_pendamping_id'],
                            ];
                            if (array_intersect($dosen1, $dosen2)) {
                                $validasiPoin3 = false;
                                $errorMessages[] = "Poin 3 gagal: Dosen dari {$jadwal1['nama_mahasiswa']} dan {$jadwal2['nama_mahasiswa']} bentrok di tanggal dan waktu yang sama.";
                            }
                        }
                    }
                }

                if ($validasiPoin1 && $validasiPoin2 && $validasiPoin3) {
                    foreach ($jadwalList as $jadwal) {
                        unset($jadwal['nama_mahasiswa']);
                        JadwalSidangTugasAkhir::create($jadwal);
                    }
                    session()->flash('success', 'Data jadwal sidang tugas akhir berhasil diimpor.');
                } else {
                    throw new \Exception(implode(' ', array_unique($errorMessages)));
                }
            });
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal mengimpor: ' . $e->getMessage());
        }
    }
}
