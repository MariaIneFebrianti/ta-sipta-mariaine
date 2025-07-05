<?php

namespace App\Http\Controllers;

use App\Models\HasilSidang;
use Illuminate\Http\Request;
use App\Models\RiwayatSidang;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use App\Models\{Dosen, HasilAkhirTA, PenilaianTA, Mahasiswa, RubrikNilai, JadwalSeminarProposal, JadwalSidangTugasAkhir};

\Carbon\Carbon::setLocale('id');


class PenilaianController extends Controller
{
    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'mahasiswa_id' => ['required', 'exists:mahasiswa,id'],
    //         'dosen_id' => ['required', 'exists:dosen,id'],
    //         'nilai' => ['required', 'array'],
    //         'nilai.*' => ['nullable', 'numeric'],
    //     ]);

    //     $total = 0;
    //     $jenisDosen = null;

    //     foreach ($request->nilai as $rubrikId => $nilai) {
    //         $rubrik = RubrikNilai::find($rubrikId);
    //         if (!$rubrik) continue;

    //         // Ambil jenis_dosen dari rubrik (harus konsisten per dosen)
    //         if (!$jenisDosen) {
    //             $jenisDosen = $rubrik->jenis_dosen;
    //         }

    //         // Simpan nilai ke penilaian_ta
    //         PenilaianTA::updateOrCreate(
    //             [
    //                 // Cari berdasarkan kombinasi ini
    //                 'mahasiswa_id' => $request->mahasiswa_id,
    //                 'dosen_id' => $request->dosen_id,
    //                 'rubrik_id' => $rubrikId,
    //             ],
    //             [
    //                 // Jika pencarian berhasil, update nilai kalau ada, kalau belum tambahkan data
    //                 'nilai' => $nilai,
    //             ]
    //         );

    //         $total += ($nilai * $rubrik->persentase / 100);
    //     }

    //     if (!$jenisDosen) {
    //         return back()->with('error', 'Rubrik tidak ditemukan atau tidak valid.');
    //     }

    //     // Hitung ulang semua total per jenis_dosen
    //     $penilaian = PenilaianTA::where('mahasiswa_id', $request->mahasiswa_id)
    //         ->with('rubrik')
    //         ->get()
    //         ->groupBy(fn($item) => $item->rubrik->jenis_dosen);

    //     $nilai = [
    //         'pembimbing_utama' => optional($penilaian->get('pembimbing_utama'))->sum(fn($p) => $p->nilai * $p->rubrik->persentase / 100),
    //         'pembimbing_pendamping' => optional($penilaian->get('pembimbing_pendamping'))->sum(fn($p) => $p->nilai * $p->rubrik->persentase / 100),
    //         'penguji_utama' => optional($penilaian->get('penguji_utama'))->sum(fn($p) => $p->nilai * $p->rubrik->persentase / 100),
    //         'penguji_pendamping' => optional($penilaian->get('penguji_pendamping'))->sum(fn($p) => $p->nilai * $p->rubrik->persentase / 100),
    //     ];

    //     $hasil = HasilAkhirTA::firstOrNew(['mahasiswa_id' => $request->mahasiswa_id]);
    //     $hasil->nilai_pembimbing_utama = $nilai['pembimbing_utama'];
    //     $hasil->nilai_pembimbing_pendamping = $nilai['pembimbing_pendamping'];
    //     $hasil->nilai_penguji_utama = $nilai['penguji_utama'];
    //     $hasil->nilai_penguji_pendamping = $nilai['penguji_pendamping'];

    //     // Hitung total_akhir hanya jika semua peran sudah memberi nilai
    //     $semuaAda = collect($nilai)->every(fn($n) => $n !== null);

    //     if ($semuaAda) {
    //         $hasil->total_akhir =
    //             ($nilai['pembimbing_utama'] * 0.3) +
    //             ($nilai['pembimbing_pendamping'] * 0.3) +
    //             ($nilai['penguji_utama'] * 0.2) +
    //             ($nilai['penguji_pendamping'] * 0.2);
    //     } else {
    //         $hasil->total_akhir = null;
    //     }

    //     $hasil->save();

    //     return back()->with('success', 'Nilai berhasil disimpan.');
    // }



    public function store(Request $request)
    {
        $mahasiswaId = $request->mahasiswa_id;
        $mahasiswa = Mahasiswa::findOrFail($mahasiswaId);
        $programStudiId = $mahasiswa->program_studi_id;

        $request->validate([
            'mahasiswa_id' => ['required', 'exists:mahasiswa,id'],
            'dosen_id' => ['required', 'exists:dosen,id'],
            'jadwal_sidang_tugas_akhir_id' => ['required', 'exists:jadwal_sidang_tugas_akhir,id'],
            'nilai' => ['required', 'array'],
            'nilai.*' => ['nullable', 'numeric'],
        ]);

        $mahasiswaId = $request->mahasiswa_id;
        $dosenId = $request->dosen_id;
        $jadwalId = $request->jadwal_sidang_tugas_akhir_id;
        $total = 0;
        $jenisDosen = null;

        foreach ($request->nilai as $rubrikId => $nilai) {
            // $rubrik = RubrikNilai::find($rubrikId);
            // if (!$rubrik) continue;

            $rubrik = RubrikNilai::where('id', $rubrikId)
                ->where('program_studi_id', $programStudiId)
                ->first();

            if (!$rubrik) continue;


            if (!$jenisDosen) {
                $jenisDosen = $rubrik->jenis_dosen;
            }

            $penilaian = PenilaianTA::firstOrNew([
                'mahasiswa_id' => $mahasiswaId,
                'dosen_id' => $dosenId,
                'rubrik_id' => $rubrikId,
                'jadwal_sidang_tugas_akhir_id' => $jadwalId,
            ]);

            $penilaian->nilai = $nilai;
            $penilaian->save();

            $total += ($nilai * $rubrik->persentase / 100);
        }

        if (!$jenisDosen) {
            return back()->with('error', 'Rubrik tidak ditemukan atau tidak valid.');
        }

        // Hitung total per jenis dosen
        $penilaian = PenilaianTA::where('mahasiswa_id', $mahasiswaId)
            ->where('jadwal_sidang_tugas_akhir_id', $jadwalId)
            ->with('rubrik')
            ->get()
            ->groupBy(fn($item) => $item->rubrik->jenis_dosen);

        $nilai = [
            'Pembimbing Utama' => optional($penilaian->get('Pembimbing Utama'))->sum(fn($p) => $p->nilai * $p->rubrik->persentase / 100),
            'Pembimbing Pendamping' => optional($penilaian->get('Pembimbing Pendamping'))->sum(fn($p) => $p->nilai * $p->rubrik->persentase / 100),
            'Penguji Utama' => optional($penilaian->get('Penguji Utama'))->sum(fn($p) => $p->nilai * $p->rubrik->persentase / 100),
            'Penguji Pendamping' => optional($penilaian->get('Penguji Pendamping'))->sum(fn($p) => $p->nilai * $p->rubrik->persentase / 100),
        ];

        $hasil = HasilAkhirTA::firstOrNew([
            'mahasiswa_id' => $mahasiswaId,
            'jadwal_sidang_tugas_akhir_id' => $jadwalId,
        ]);

        $hasil->nilai_pembimbing_utama = $nilai['Pembimbing Utama'];
        $hasil->nilai_pembimbing_pendamping = $nilai['Pembimbing Pendamping'];
        $hasil->nilai_penguji_utama = $nilai['Penguji Utama'];
        $hasil->nilai_penguji_pendamping = $nilai['Penguji Pendamping'];

        $semuaAda = collect($nilai)->every(fn($n) => $n !== null);

        $hasil->total_akhir = $semuaAda
            ? ($nilai['Pembimbing Utama'] * 0.3) +
            ($nilai['Pembimbing Pendamping'] * 0.3) +
            ($nilai['Penguji Utama'] * 0.2) +
            ($nilai['Penguji Pendamping'] * 0.2)
            : null;


        $hasil->save();

        // Simpan riwayat jika nilai lengkap
        if ($hasil->total_akhir !== null) {
            $tahunLulus = now()->format('Y');

            $hasilSidang = HasilSidang::updateOrCreate([
                'mahasiswa_id' => $mahasiswaId,
            ], [
                'status_kelulusan' => 'Belum Ada Status',
                'tahun_lulus' => $tahunLulus,
            ]);

            $statusSidang = $hasil->total_akhir >= 60 ? 'Lulus' : 'Sidang Ulang';

            RiwayatSidang::create([
                'hasil_sidang_id' => $hasilSidang->id,
                'jadwal_sidang_tugas_akhir_id' => $jadwalId,
                'status_sidang' => $statusSidang,
            ]);

            $riwayatTerakhir = RiwayatSidang::where('hasil_sidang_id', $hasilSidang->id)->latest()->first();

            if ($riwayatTerakhir) {
                $hasilSidang->update(['status_kelulusan' => $riwayatTerakhir->status_sidang]);
            }
        }

        return back()->with('success', 'Nilai berhasil disimpan.');
    }



    public function indexProposalDosen()
    {
        if (!Auth::check()) {
            return redirect('/login')->with('message', 'Please log in to continue.');
        }

        $user = Auth::user();
        $dosenId = $user->dosen->id;

        $mahasiswa = Mahasiswa::with('jadwalSeminarProposal')->get();

        $seminar = JadwalSeminarProposal::with([
            'mahasiswa',
            'mahasiswa.nilai',
            'mahasiswa.proposal'
        ])->where(function ($query) use ($dosenId) {
            $query->where('penguji_utama_id', $dosenId)
                ->orWhere('penguji_pendamping_id', $dosenId);
        })->get();

        return view('penilaian.proposal', compact('seminar', 'dosenId', 'mahasiswa', 'user'));
    }

    public function indexTugasAkhirDosen()
    {
        if (!Auth::check()) {
            return redirect('/login')->with('message', 'Please log in to continue.');
        }

        $user = Auth::user();
        $dosen = $user->dosen;
        $dosenId = $dosen->id;

        $mahasiswa = Mahasiswa::with('jadwalSidangTugasAkhir')->get();

        $sidang = JadwalSidangTugasAkhir::with([
            'mahasiswa',
            'mahasiswa.nilai',
            'mahasiswa.pendaftaranSidang'
        ])
            ->where(function ($query) use ($dosenId) {
                $query->where('pembimbing_utama_id', $dosenId)
                    ->orWhere('pembimbing_pendamping_id', $dosenId)
                    ->orWhere('penguji_utama_id', $dosenId)
                    ->orWhere('penguji_pendamping_id', $dosenId);
            })
            ->get();

        foreach ($sidang as $item) {
            // Tentukan peran dosen
            $peran = null;
            if ($item->pembimbing_utama_id === $dosenId) $peran = 'pembimbing_utama';
            elseif ($item->pembimbing_pendamping_id === $dosenId) $peran = 'pembimbing_pendamping';
            elseif ($item->penguji_utama_id === $dosenId) $peran = 'penguji_utama';
            elseif ($item->penguji_pendamping_id === $dosenId) $peran = 'penguji_pendamping';

            // Ambil rubrik berdasarkan peran
            // $rubrik = RubrikNilai::where('jenis_dosen', $peran)
            //     ->orderBy('id')
            //     ->get();

            $programStudiId = $item->mahasiswa->program_studi_id;

            $rubrik = RubrikNilai::where('jenis_dosen', $peran)
                ->where('program_studi_id', $programStudiId)
                ->orderBy('id')
                ->get();


            // Ambil nilai existing dari penilaian_ta
            $existing = PenilaianTA::where('mahasiswa_id', $item->mahasiswa_id)
                ->where('dosen_id', $dosenId)
                ->get()
                ->keyBy('rubrik_id');

            // Tandai apakah kelompok perlu ditampilkan, isi nilai dan readonly
            $kelompokLalu = null;
            foreach ($rubrik as $r) {
                $r->nilai = $existing[$r->id]->nilai ?? null;
                $r->readonly = $r->nilai !== null;
                $r->show_kelompok = $r->kelompok && $r->kelompok !== $kelompokLalu;
                $kelompokLalu = $r->kelompok;
            }

            $item->peran = $peran;
            $item->rubrik = $rubrik;
            $item->nilai_eksisting = $existing;
            $item->sudah_dinilai_semua = $rubrik->every(fn($r) => $r->nilai !== null);
            $item->dosen_id = $dosenId;
        }

        return view('penilaian.tugas_akhir', compact('sidang', 'dosen', 'dosenId', 'mahasiswa', 'user'));
    }


    public function form($sidangId)
    {
        if (!Auth::check()) {
            return redirect('/login')->with('message', 'Please log in to continue.');
        }

        $user = Auth::user();
        $dosen = $user->dosen;
        $dosenId = $dosen->id;

        // Cari data sidang berdasarkan ID + validasi dosen login
        $sidang = JadwalSidangTugasAkhir::with([
            'mahasiswa',
            'mahasiswa.nilai',
            'mahasiswa.pendaftaranSidang',
            'ruanganSidang',
        ])
            ->where('id', $sidangId)
            ->where(function ($query) use ($dosenId) {
                $query->where('pembimbing_utama_id', $dosenId)
                    ->orWhere('pembimbing_pendamping_id', $dosenId)
                    ->orWhere('penguji_utama_id', $dosenId)
                    ->orWhere('penguji_pendamping_id', $dosenId);
            })
            ->first();

        if (!$sidang) {
            return redirect()->back()->with('error', 'Data sidang tidak ditemukan atau Anda tidak memiliki akses.');
        }

        // Tentukan peran dosen dalam sidang ini
        $peran = match (true) {
            $sidang->pembimbing_utama_id === $dosenId => 'Pembimbing Utama',
            $sidang->pembimbing_pendamping_id === $dosenId => 'Pembimbing Pendamping',
            $sidang->penguji_utama_id === $dosenId => 'Penguji Utama',
            $sidang->penguji_pendamping_id === $dosenId => 'Penguji Pendamping',
            default => null,
        };

        // Ini belum berdasarkan program studi rubrik
        // Ambil rubrik sesuai peran
        // $rubrik = RubrikNilai::where('jenis_dosen', $peran)->orderBy('id')->get();

        $programStudiId = $sidang->mahasiswa->program_studi_id;

        $rubrik = RubrikNilai::where('jenis_dosen', $peran)
            ->where('program_studi_id', $programStudiId)
            ->orderBy('id')
            ->get();


        $totalPersentase = $rubrik->sum('persentase');
        $sidang->total_persentase = $totalPersentase;
        $sidang->rubrik_valid = $totalPersentase === 100;

        // Ambil nilai yang sudah diisi oleh dosen ini untuk sidang ini
        $existing = PenilaianTA::where('mahasiswa_id', $sidang->mahasiswa_id)
            ->where('dosen_id', $dosenId)
            ->where('jadwal_sidang_tugas_akhir_id', $sidang->id)
            ->get()
            ->keyBy('rubrik_id');

        $kelompokLalu = null;
        foreach ($rubrik as $r) {
            $r->nilai = $existing[$r->id]->nilai ?? null;
            $r->readonly = $r->nilai !== null;
            $r->show_kelompok = $r->kelompok && $r->kelompok !== $kelompokLalu;
            $kelompokLalu = $r->kelompok;
        }

        // Ambil nilai akhir mahasiswa
        $hasilAkhir = HasilAkhirTA::where('mahasiswa_id', $sidang->mahasiswa_id)->first();

        $sidang->total_nilai_akhir = match ($peran) {
            'Pembimbing Utama' => $hasilAkhir->nilai_pembimbing_utama ?? null,
            'Pembimbing Pendamping' => $hasilAkhir->nilai_pembimbing_pendamping ?? null,
            'Penguji Utama' => $hasilAkhir->nilai_penguji_utama ?? null,
            'Penguji Pendamping' => $hasilAkhir->nilai_penguji_pendamping ?? null,
            default => null,
        };

        $sidang->peran = $peran;
        $sidang->rubrik = $rubrik;
        $sidang->nilai_eksisting = $existing;
        $sidang->sudah_dinilai_semua = $rubrik->every(fn($r) => $r->nilai !== null);
        $sidang->dosen_id = $dosenId;

        return view('penilaian.form', compact('sidang', 'dosen'));
    }



    public function cetakPDF($id)
    {
        if (!Auth::check()) {
            return redirect('/login')->with('message', 'Silakan login terlebih dahulu.');
        }

        $user = Auth::user();
        $dosen = $user->dosen;
        $dosenId = $dosen->id;

        $sidang = JadwalSidangTugasAkhir::with([
            'mahasiswa',
            'mahasiswa.nilai',
            'mahasiswa.pendaftaranSidang',
            'ruanganSidang'
        ])
            ->where('mahasiswa_id', $id)
            ->where(function ($query) use ($dosenId) {
                $query->where('pembimbing_utama_id', $dosenId)
                    ->orWhere('pembimbing_pendamping_id', $dosenId)
                    ->orWhere('penguji_utama_id', $dosenId)
                    ->orWhere('penguji_pendamping_id', $dosenId);
            })
            ->first();

        if (!$sidang) {
            return redirect()->back()->with('error', 'Data sidang tidak ditemukan.');
        }

        // Tentukan peran dosen
        $peran = match (true) {
            $sidang->pembimbing_utama_id === $dosenId => 'Pembimbing Utama',
            $sidang->pembimbing_pendamping_id === $dosenId => 'Pembimbing Pendamping',
            $sidang->penguji_utama_id === $dosenId => 'Penguji Utama',
            $sidang->penguji_pendamping_id === $dosenId => 'Penguji Pendamping',
            default => null,
        };

        // // Ambil rubrik berdasarkan peran
        // $rubrik = RubrikNilai::where('jenis_dosen', $peran)
        //     ->orderBy('id')
        //     ->get();

        $programStudiId = $sidang->mahasiswa->program_studi_id;

        $rubrik = RubrikNilai::where('jenis_dosen', $peran)
            ->where('program_studi_id', $programStudiId)
            ->orderBy('id')
            ->get();


        // Ambil nilai existing
        $existing = PenilaianTA::where('mahasiswa_id', $sidang->mahasiswa_id)
            ->where('dosen_id', $dosenId)
            ->get()
            ->keyBy('rubrik_id');

        // Kelompok & nilai
        $kelompokLalu = null;
        foreach ($rubrik as $r) {
            $r->nilai = $existing[$r->id]->nilai ?? null;
            $r->readonly = $r->nilai !== null;
            $r->show_kelompok = $r->kelompok && $r->kelompok !== $kelompokLalu;
            $kelompokLalu = $r->kelompok;
        }

        $hasilAkhir = HasilAkhirTA::where('mahasiswa_id', $sidang->mahasiswa_id)->first();

        $sidang->total_nilai_akhir = match ($peran) {
            'Pembimbing Utama' => $hasilAkhir->nilai_pembimbing_utama ?? null,
            'Pembimbing Pendamping' => $hasilAkhir->nilai_pembimbing_pendamping ?? null,
            'Penguji Utama' => $hasilAkhir->nilai_penguji_utama ?? null,
            'Penguji Pendamping' => $hasilAkhir->nilai_penguji_pendamping ?? null,
            default => null,
        };

        $sidang->peran = $peran;
        $sidang->rubrik = $rubrik;
        $sidang->nilai_eksisting = $existing;
        $sidang->sudah_dinilai_semua = $rubrik->every(fn($r) => $r->nilai !== null);
        $sidang->dosen_id = $dosenId;
        $dosenPenilai = Dosen::find($dosenId);

        $pdf = Pdf::loadView('penilaian.cetak', [
            'sidang' => $sidang,
            'dosen' => $dosenPenilai
        ])->setPaper('A4', 'portrait');

        return $pdf->download('Form Penilaian ' . $sidang->mahasiswa->nama_mahasiswa . ' oleh ' . $dosen->nama_dosen . '.pdf');
    }
    public function lihatNilaiKaprodi($id)
    {
        if (!Auth::check()) {
            return redirect('/login')->with('message', 'Silakan login terlebih dahulu.');
        }

        $user = Auth::user();
        $dosen = $user->dosen;
        $dosenId = $dosen->id;

        $sidang = JadwalSidangTugasAkhir::with([
            'mahasiswa',
            'mahasiswa.nilai',
            'mahasiswa.pendaftaranSidang',
            'ruanganSidang'
        ])
            ->where('mahasiswa_id', $id)
            ->where(function ($query) use ($dosenId) {
                $query->where('pembimbing_utama_id', $dosenId)
                    ->orWhere('pembimbing_pendamping_id', $dosenId)
                    ->orWhere('penguji_utama_id', $dosenId)
                    ->orWhere('penguji_pendamping_id', $dosenId);
            })
            ->first();

        if (!$sidang) {
            return redirect()->back()->with('error', 'Data sidang tidak ditemukan.');
        }

        // Tentukan peran dosen
        $peran = match (true) {
            $sidang->pembimbing_utama_id === $dosenId => 'Pembimbing Utama',
            $sidang->pembimbing_pendamping_id === $dosenId => 'Pembimbing Pendamping',
            $sidang->penguji_utama_id === $dosenId => 'Penguji Utama',
            $sidang->penguji_pendamping_id === $dosenId => 'Penguji Pendamping',
            default => null,
        };

        $programStudiId = $sidang->mahasiswa->program_studi_id;

        $rubrik = RubrikNilai::where('jenis_dosen', $peran)
            ->where('program_studi_id', $programStudiId)
            ->orderBy('id')
            ->get();


        // Ambil nilai existing
        $existing = PenilaianTA::where('mahasiswa_id', $sidang->mahasiswa_id)
            ->where('dosen_id', $dosenId)
            ->get()
            ->keyBy('rubrik_id');

        // Kelompok & nilai
        $kelompokLalu = null;
        foreach ($rubrik as $r) {
            $r->nilai = $existing[$r->id]->nilai ?? null;
            $r->readonly = $r->nilai !== null;
            $r->show_kelompok = $r->kelompok && $r->kelompok !== $kelompokLalu;
            $kelompokLalu = $r->kelompok;
        }

        $hasilAkhir = HasilAkhirTA::where('mahasiswa_id', $sidang->mahasiswa_id)->first();

        $sidang->total_nilai_akhir = match ($peran) {
            'Pembimbing Utama' => $hasilAkhir->nilai_pembimbing_utama ?? null,
            'Pembimbing Pendamping' => $hasilAkhir->nilai_pembimbing_pendamping ?? null,
            'Penguji Utama' => $hasilAkhir->nilai_penguji_utama ?? null,
            'Penguji Pendamping' => $hasilAkhir->nilai_penguji_pendamping ?? null,
            default => null,
        };

        $sidang->peran = $peran;
        $sidang->rubrik = $rubrik;
        $sidang->nilai_eksisting = $existing;
        $sidang->sudah_dinilai_semua = $rubrik->every(fn($r) => $r->nilai !== null);
        $sidang->dosen_id = $dosenId;
        $dosenPenilai = Dosen::find($dosenId);

        $pdf = Pdf::loadView('penilaian.cetak', [
            'sidang' => $sidang,
            'dosen' => $dosenPenilai
        ])->setPaper('A4', 'portrait');

        return $pdf->stream('Form Penilaian ' . $sidang->mahasiswa->nama_mahasiswa . ' oleh ' . $dosen->nama_dosen . '.pdf');
    }
}
