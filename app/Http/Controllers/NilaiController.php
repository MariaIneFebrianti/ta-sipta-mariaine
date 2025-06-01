<?php

namespace App\Http\Controllers;

use App\Models\JadwalSeminarProposal;
use App\Models\JadwalSidangTugasAkhir;
use App\Models\LogbookBimbingan;
use App\Models\Mahasiswa;
use App\Models\Nilai;
use App\Models\Proposal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Dosen;

class NilaiController extends Controller

{
    public function indexMahasiswa()
    {
        if (!Auth::check()) {
            return redirect('/login')->with('message', 'Please log in to continue.');
        }
        $user = Auth::user();

        if ($user->role === 'Mahasiswa') {

            // Cari data mahasiswa berdasarkan user yang login
            $mahasiswa = Mahasiswa::where('user_id', Auth::id())->first();

            if (!$mahasiswa) {
                return redirect()->back()->with('error', 'Data mahasiswa tidak ditemukan.');
            }

            // Ambil nilai berdasarkan mahasiswa_id
            $nilai = Nilai::where('mahasiswa_id', $mahasiswa->id)->get();
        } else {
            abort(403);
        }

        return view('nilai.index', compact('nilai', 'user'));
    }
    public function indexProposalDosen()
    {
        if (!Auth::check()) {
            return redirect('/login')->with('message', 'Please log in to continue.');
        }
        $user = Auth::user();

        if ($user->role === 'Dosen' && $user->dosen->jabatan === 'Koordinator Program Studi') {
            $dosenId = auth()->user()->dosen->id;

            $mahasiswa = Mahasiswa::with('jadwalSeminarProposal')->get();

            $seminar = JadwalSeminarProposal::with([
                'mahasiswa',
                'mahasiswa.nilai',
                'mahasiswa.proposal'
            ])
                ->where(function ($query) use ($dosenId) {
                    $query->where('penguji_utama_id', $dosenId)
                        ->orWhere('penguji_pendamping_id', $dosenId);
                })
                ->get();
        } elseif ($user->role === 'Dosen') {
            $dosenId = auth()->user()->dosen->id;

            $mahasiswa = Mahasiswa::with('jadwalSeminarProposal')->get();

            $seminar = JadwalSeminarProposal::with([
                'mahasiswa',
                'mahasiswa.nilai',
                'mahasiswa.proposal'
            ])
                ->where(function ($query) use ($dosenId) {
                    $query->where('penguji_utama_id', $dosenId)
                        ->orWhere('penguji_pendamping_id', $dosenId);
                })
                ->get();
        }

        return view('nilai.proposal', compact('seminar', 'dosenId', 'mahasiswa', 'user'));
    }

    public function indexTugasAkhirDosen()
    {
        if (!Auth::check()) {
            return redirect('/login')->with('message', 'Please log in to continue.');
        }
        $user = Auth::user();

        $dosenId = auth()->user()->dosen->id;

        $mahasiswa = Mahasiswa::with('jadwalSidangTugasAkhir')->get();

        $sidang = JadwalSidangTugasAkhir::with(['mahasiswa', 'mahasiswa.nilai', 'mahasiswa.pendaftaranSidang'])
            ->where(function ($query) use ($dosenId) {
                $query->where('pembimbing_utama_id', $dosenId)
                    ->orWhere('pembimbing_pendamping_id', $dosenId)
                    ->orWhere('penguji_utama_id', $dosenId)
                    ->orWhere('penguji_pendamping_id', $dosenId);
            })
            ->get();

        return view('nilai.tugas_akhir', compact('sidang', 'dosenId', 'user', 'mahasiswa'));
    }

    public function daftarNilai()
    {
        if (!Auth::check()) {
            return redirect('/login')->with('message', 'Please log in to continue.');
        }
        $user = Auth::user();

        $nilai = Nilai::with('mahasiswa')->get();
        return view('nilai.index', compact('nilai', 'user'));
    }

    public function store(Request $request, $mahasiswaId)
    {
        $dosenId = auth()->user()->dosen->id;

        $jadwalSeminar = JadwalSeminarProposal::where('mahasiswa_id', $mahasiswaId)->first();
        $jadwalSidang = JadwalSidangTugasAkhir::where('mahasiswa_id', $mahasiswaId)->first();

        $nilai = Nilai::firstOrNew(['mahasiswa_id' => $mahasiswaId]);

        // === Hak akses pengisian berdasarkan peran dosen ===
        if ($jadwalSeminar) {
            if ($jadwalSeminar->penguji_utama_id == $dosenId && $request->has('nilai_seminar_utama')) {
                $nilai->nilai_seminar_utama = $request->input('nilai_seminar_utama');
            }
            if ($jadwalSeminar->penguji_pendamping_id == $dosenId && $request->has('nilai_seminar_pendamping')) {
                $nilai->nilai_seminar_pendamping = $request->input('nilai_seminar_pendamping');
            }
        }

        if ($jadwalSidang) {
            if ($jadwalSidang->pembimbing_utama_id == $dosenId && $request->has('nilai_ta_utama')) {
                $nilai->nilai_ta_utama = $request->input('nilai_ta_utama');
            }
            if ($jadwalSidang->pembimbing_pendamping_id == $dosenId && $request->has('nilai_ta_pembimbing')) {
                $nilai->nilai_ta_pembimbing = $request->input('nilai_ta_pembimbing');
            }
            if ($jadwalSidang->penguji_utama_id == $dosenId && $request->has('nilai_ta_penguji_utama')) {
                $nilai->nilai_ta_penguji_utama = $request->input('nilai_ta_penguji_utama');
            }
            if ($jadwalSidang->penguji_pendamping_id == $dosenId && $request->has('nilai_ta_penguji_pendamping')) {
                $nilai->nilai_ta_penguji_pendamping = $request->input('nilai_ta_penguji_pendamping');
            }
        }

        // === Perhitungan akhir otomatis (misalnya 50:50 atau sesuai bobot) ===
        $nilai->nilai_seminar = $this->hitungNilaiSeminar($nilai);
        $nilai->nilai_ta = $this->hitungNilaiTA($nilai);

        $nilai->save();

        return redirect()->back()->with('success', 'Nilai berhasil disimpan.');
    }

    // private function hitungNilaiSeminar($nilai)
    // {
    //     $nilaiUtama = $nilai->nilai_seminar_utama ?? 0;
    //     $nilaiPendamping = $nilai->nilai_seminar_pendamping ?? 0;
    //     return ($nilaiUtama * 0.6) + ($nilaiPendamping * 0.4); // Misalnya: 60% penguji utama, 40% pendamping
    // }
    private function hitungNilaiSeminar($nilai)
    {
        // Cek dulu apakah dua-duanya ada
        if (is_null($nilai->nilai_seminar_utama) || is_null($nilai->nilai_seminar_pendamping)) {
            return; // Tidak melakukan apa-apa jika salah satu belum diisi
        }

        $nilaiUtama = $nilai->nilai_seminar_utama;
        $nilaiPendamping = $nilai->nilai_seminar_pendamping;

        return ($nilaiUtama * 0.6) + ($nilaiPendamping * 0.4);
    }

    // private function hitungNilaiTA($nilai)
    // {
    //     $ta1 = $nilai->nilai_ta_utama ?? 0;
    //     $ta2 = $nilai->nilai_ta_pembimbing ?? 0;
    //     $ta3 = $nilai->nilai_ta_penguji_utama ?? 0;
    //     $ta4 = $nilai->nilai_ta_penguji_pendamping ?? 0;

    //     return ($ta1 * 0.3) + ($ta2 * 0.2) + ($ta3 * 0.3) + ($ta4 * 0.2); // Contoh pembobotan
    // }

    private function hitungNilaiTA($nilai)
    {
        // Cek apakah semua nilai sudah diisi
        if (
            is_null($nilai->nilai_ta_utama) ||
            is_null($nilai->nilai_ta_pembimbing) ||
            is_null($nilai->nilai_ta_penguji_utama) ||
            is_null($nilai->nilai_ta_penguji_pendamping)
        ) {
            return; // Tidak memproses jika salah satu nilai belum diisi
        }

        $ta1 = $nilai->nilai_ta_utama;
        $ta2 = $nilai->nilai_ta_pembimbing;
        $ta3 = $nilai->nilai_ta_penguji_utama;
        $ta4 = $nilai->nilai_ta_penguji_pendamping;

        return ($ta1 * 0.3) + ($ta2 * 0.2) + ($ta3 * 0.3) + ($ta4 * 0.2);
    }

















    //JADI TAPI BINGUNGIN
    // public function index()
    // {
    //     if (!Auth::check()) {
    //         return redirect('/login')->with('message', 'Please log in to continue.');
    //     }

    //     $user = Auth::user();
    //     // Cek apakah user punya relasi dosen
    //     if ($user->dosen) {
    //         $dosenId = $user->dosen->id;
    //     } else {
    //         // Handle jika user tidak punya relasi dosen
    //         // Misalnya, redirect ke halaman lain atau tampilkan pesan error
    //         abort(403, 'Akun Anda tidak terhubung dengan data dosen.');
    //         // Atau, jika dosen_id boleh null:
    //         // $dosenId = null;
    //     }
    //     $userRole = $user->role;

    //     if ($userRole !== 'Dosen') {
    //         abort(403, 'Anda tidak memiliki akses ke halaman ini.');
    //     }




    //     // Ambil mahasiswa yang dosen ini jadi penguji di seminar proposal
    //     $seminarMahasiswa = JadwalSeminarProposal::where(function ($query) use ($dosenId) {
    //         $query->where('penguji_utama_id', $dosenId)
    //             ->orWhere('penguji_pendamping_id', $dosenId);
    //     })
    //         ->with(['mahasiswa' => function ($q) {
    //             $q->with('nilai');
    //         }])
    //         ->get();

    //     // Ambil mahasiswa yang dosen ini jadi penguji atau pembimbing di sidang tugas akhir
    //     $sidangMahasiswa = JadwalSidangTugasAkhir::where(function ($query) use ($dosenId) {
    //         $query->where('penguji_utama_id', $dosenId)
    //             ->orWhere('penguji_pendamping_id', $dosenId)
    //             ->orWhere('pembimbing_utama_id', $dosenId)
    //             ->orWhere('pembimbing_pendamping_id', $dosenId);
    //     })
    //         ->with(['mahasiswa' => function ($q) {
    //             $q->with('nilai');
    //         }])
    //         ->get();

    //     return view('nilai.index', compact('seminarMahasiswa', 'sidangMahasiswa', 'userRole'));
    // }

    // public function store(Request $request)
    // {
    //     $user = Auth::user();
    //     $dosenId = $user->dosen?->id;

    //     if (!$dosenId) {
    //         abort(403, 'Data dosen tidak ditemukan.');
    //     }

    //     $mahasiswaId = $request->input('mahasiswa_id');
    //     if (!$mahasiswaId) {
    //         return back()->withErrors('Mahasiswa tidak valid.');
    //     }

    //     // Cari jadwal seminar dan sidang untuk mahasiswa ini
    //     $jadwalSeminar = JadwalSeminarProposal::where('mahasiswa_id', $mahasiswaId)->first();
    //     $jadwalSidang = JadwalSidangTugasAkhir::where('mahasiswa_id', $mahasiswaId)->first();

    //     $editableFields = [];

    //     // Tentukan field yang boleh diedit berdasarkan peran dosen di seminar
    //     if ($jadwalSeminar) {
    //         if ($jadwalSeminar->penguji_utama_id == $dosenId) {
    //             $editableFields[] = 'nilai_seminar_utama';
    //         }
    //         if ($jadwalSeminar->penguji_pendamping_id == $dosenId) {
    //             $editableFields[] = 'nilai_seminar_pendamping';
    //         }
    //     }

    //     // Tentukan field yang boleh diedit berdasarkan peran dosen di sidang
    //     if ($jadwalSidang) {
    //         if ($jadwalSidang->pembimbing_utama_id == $dosenId) {
    //             $editableFields[] = 'nilai_ta_utama';
    //         }
    //         if ($jadwalSidang->pembimbing_pendamping_id == $dosenId) {
    //             $editableFields[] = 'nilai_ta_pendamping';
    //         }
    //         if ($jadwalSidang->penguji_utama_id == $dosenId) {
    //             $editableFields[] = 'nilai_ta_penguji_utama';
    //         }
    //         if ($jadwalSidang->penguji_pendamping_id == $dosenId) {
    //             $editableFields[] = 'nilai_ta_penguji_pendamping';
    //         }
    //     }

    //     if (empty($editableFields)) {
    //         abort(403, 'Anda tidak memiliki hak mengisi nilai untuk mahasiswa ini.');
    //     }

    //     // Validasi hanya field yang boleh diedit
    //     $rules = [];
    //     foreach ($editableFields as $field) {
    //         $rules[$field] = 'nullable|numeric|min:0|max:100';
    //     }

    //     $validated = $request->validate($rules);

    //     // Ambil atau buat data nilai mahasiswa
    //     $nilai = Nilai::firstOrNew(['mahasiswa_id' => $mahasiswaId]);

    //     // Update hanya field yang boleh diedit
    //     foreach ($editableFields as $field) {
    //         if (array_key_exists($field, $validated)) {
    //             $nilai->$field = $validated[$field];
    //         }
    //     }

    //     // Hitung nilai seminar jika ada nilai seminar utama dan pendamping
    //     if (in_array('nilai_seminar_utama', $editableFields) || in_array('nilai_seminar_pendamping', $editableFields)) {
    //         $nilai->nilai_seminar =
    //             ($nilai->nilai_seminar_utama ?? 0) * 0.6 +
    //             ($nilai->nilai_seminar_pendamping ?? 0) * 0.4;
    //     }

    //     // Hitung nilai tugas akhir jika ada nilai terkait
    //     if (
    //         in_array('nilai_ta_utama', $editableFields) ||
    //         in_array('nilai_ta_pendamping', $editableFields) ||
    //         in_array('nilai_ta_penguji_utama', $editableFields) ||
    //         in_array('nilai_ta_penguji_pendamping', $editableFields)
    //     ) {
    //         $nilai->nilai_ta =
    //             ($nilai->nilai_ta_utama ?? 0) * 0.3 +
    //             ($nilai->nilai_ta_pendamping ?? 0) * 0.2 +
    //             ($nilai->nilai_ta_penguji_utama ?? 0) * 0.3 +
    //             ($nilai->nilai_ta_penguji_pendamping ?? 0) * 0.2;
    //     }

    //     $nilai->save();

    //     return redirect()->route('nilai.index')->with('success', 'Nilai berhasil disimpan!');
    // }
}
