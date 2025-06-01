<?php

namespace App\Imports;

use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\RuanganSidang;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Models\JadwalSeminarProposal;
use Maatwebsite\Excel\Concerns\ToCollection;

class JadwalSeminarProposalImport implements ToCollection
{
    /**
     * @param Collection $collection
     */
    // public function collection(Collection $collection)
    // {
    //     //
    // }

    public function collection(Collection $collection)
    {
        try {
            DB::transaction(function () use ($collection) {
                $jadwalList = [];

                foreach ($collection as $key => $row) {
                    // Skip header row
                    if ($key === 0) continue;

                    // Cek ID mahasiswa, pembimbing, penguji, ruangan
                    // Cek ID mahasiswa
                    $mahasiswaId = null;
                    if (is_numeric($row[0])) {
                        $mahasiswaId = Mahasiswa::find($row[0]) ? $row[0] : null;
                    } else {
                        $mahasiswa = Mahasiswa::where('nama_mahasiswa', $row[0])->first();
                        $mahasiswaId = $mahasiswa ? $mahasiswa->id : null;
                    }

                    // Cek ID penguji utama
                    $pengujiUtamaId = null;
                    if (is_numeric($row[3])) {
                        $pengujiUtamaId = Dosen::find($row[1]) ? $row[1] : null;
                    } else {
                        $pengujiUtama = Dosen::where('nama_dosen', $row[1])->first();
                        $pengujiUtamaId = $pengujiUtama ? $pengujiUtama->id : null;
                    }

                    // Cek ID penguji pendamping
                    $pengujiPendampingId = null;
                    if (is_numeric($row[4])) {
                        $pengujiPendampingId = Dosen::find($row[2]) ? $row[2] : null;
                    } else {
                        $pengujiPendamping = Dosen::where('nama_dosen', $row[2])->first();
                        $pengujiPendampingId = $pengujiPendamping ? $pengujiPendamping->id : null;
                    }

                    // Cek ID ruangan sidang
                    $ruanganSidangId = null;
                    if (is_numeric($row[6])) {
                        $ruanganSidangId = RuanganSidang::find($row[6]) ? $row[6] : null;
                    } else {
                        $ruanganSidang = RuanganSidang::where('nama_ruangan', $row[6])->first();
                        $ruanganSidangId = $ruanganSidang ? $ruanganSidang->id : null;
                    }

                    // Cek dan simpan jadwal untuk validasi
                    $jadwalList[] = [
                        'mahasiswa_id' => $mahasiswaId,
                        'penguji_utama_id' => $pengujiUtamaId,
                        'penguji_pendamping_id' => $pengujiPendampingId,
                        'tanggal' => $row[3],
                        'waktu_mulai' => $row[4],
                        'waktu_selesai' => $row[5],
                        'ruangan_sidang_id' => $ruanganSidangId,
                    ];
                }

                $validasiPoin1 = true;
                $validasiPoin2 = true;
                $validasiPoin3 = true;

                // Poin 1: Cek setiap mahasiswa tidak boleh memiliki dosen yang sama dalam kategori yang sama
                foreach ($jadwalList as $jadwal) {
                    $dosenIds = [
                        $jadwal['penguji_utama_id'],
                        $jadwal['penguji_pendamping_id'],
                    ];

                    if (count($dosenIds) !== count(array_unique($dosenIds))) {
                        $validasiPoin1 = false; // Ada dosen yang sama
                        break;
                    }
                }

                // Poin 2 dan 3: Cek tanggal, waktu, dan ruangan
                foreach ($jadwalList as $index1 => $jadwal1) {
                    foreach ($jadwalList as $index2 => $jadwal2) {
                        if ($index1 !== $index2) {
                            if ($jadwal1['tanggal'] === $jadwal2['tanggal'] && $jadwal1['waktu_mulai'] === $jadwal2['waktu_mulai']) {
                                if ($jadwal1['ruangan_sidang_id'] === $jadwal2['ruangan_sidang_id']) {
                                    $validasiPoin2 = false; // Ruangan sama
                                }

                                // Cek apakah ada dosen yang sama
                                if (
                                    $jadwal1['penguji_utama_id'] === $jadwal2['penguji_utama_id'] ||
                                    $jadwal1['penguji_pendamping_id'] === $jadwal2['penguji_pendamping_id']
                                ) {
                                    $validasiPoin3 = false; // Dosen sama pada jadwal yang sama
                                }
                            }
                        }
                    }
                }

                // Menyimpan data jika semua poin valid
                if ($validasiPoin1 && $validasiPoin2 && $validasiPoin3) {
                    foreach ($jadwalList as $jadwal) {
                        JadwalSeminarProposal::create($jadwal);
                    }
                    session()->flash('success', 'Data jadwal seminar proposal berhasil diimpor.');
                } else {
                    // Menentukan pesan kesalahan
                    $errorMessages = [];
                    if (!$validasiPoin1) {
                        $errorMessages[] = 'Mahasiswa tidak boleh memiliki dosen yang sama dalam kategori yang sama.';
                    }
                    if (!$validasiPoin2) {
                        $errorMessages[] = 'Tanggal, waktu, dan ruangan tidak boleh sama.';
                    }
                    if (!$validasiPoin3) {
                        $errorMessages[] = 'Dosen penguji tidak boleh sama pada jadwal yang sama.';
                    }
                    throw new \Exception(implode(' ', $errorMessages));
                }
            });
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal mengimpor data jadwal seminar proposal: ' . $e->getMessage());
        }
    }
}
