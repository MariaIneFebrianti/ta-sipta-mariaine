<?php

namespace App\Http\Controllers;

use App\Imports\JadwalSeminarProposalImport;
use App\Imports\JadwalSidangTugasAkhirImport;
use App\Models\Dosen;
use App\Models\JadwalSeminarProposal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\JadwalSidangTugasAkhir;
use App\Models\ProgramStudi;
use App\Models\RuanganSidang;
use App\Models\TahunAjaran;
use Maatwebsite\Excel\Facades\Excel;

\Carbon\Carbon::setLocale('id');


class JadwalSeminarProposalController extends Controller
{
    // KESELURUHAN
    // public function index()
    // {
    //     if (Auth::check()) {
    //         $userRole = Auth::user()->role;
    //     } else {
    //         return redirect('/login')->with('message', 'Please log in to continue.');
    //     }
    //     // Mengambil data jadwal sidang tugas akhir beserta relasi terkait
    //     $jadwals = JadwalSeminarProposal::with([
    //         'mahasiswa',
    //         'pengujiUtama',
    //         'pengujiPendamping',
    //         'ruanganSidang'
    //     ])->paginate(10);

    //     $pengujiUtama = Dosen::all();
    //     $pengujiPendamping = Dosen::all();
    //     $ruanganSidang = RuanganSidang::all();

    //     // Menampilkan data ke view
    //     return view('jadwal_sidang.jadwal_seminar_proposal', compact('jadwals', 'userRole', 'ruanganSidang', 'pengujiUtama', 'pengujiPendamping'));
    // }

    // semua perprodi
    public function index()
    {
        if (!Auth::check()) {
            return redirect('/login')->with('message', 'Please log in to continue.');
        }

        $user = Auth::user();

        // Ambil semua jadwal seminar proposal lengkap dengan relasinya
        $jadwals = JadwalSeminarProposal::with([
            'mahasiswa.programStudi', // pastikan relasi ini ada
            'pengujiUtama',
            'pengujiPendamping',
            'ruanganSidang'
        ])->get(); // TANPA paginate()


        // Group berdasarkan program studi
        $jadwalsGrouped = $jadwals->groupBy(function ($item) {
            return $item->mahasiswa->programStudi->nama_prodi ?? 'Tidak Diketahui';
        });

        $programStudi = ProgramStudi::all();
        $pengujiUtama = Dosen::all();
        $pengujiPendamping = Dosen::all();
        $ruanganSidang = RuanganSidang::all();
        $tahunAjaranList = TahunAjaran::all();

        return view('jadwal_sidang.jadwal_seminar_proposal', compact(
            'jadwals',
            'user',
            'ruanganSidang',
            'pengujiUtama',
            'pengujiPendamping',
            'jadwalsGrouped',
            'programStudi',
            'tahunAjaranList',
        ));
    }


    // KAPRODI SESUAI PRODINYA
    // LEBIH RINGKAS
    // public function index()
    // {
    //     if (!Auth::check()) {
    //         return redirect('/login')->with('message', 'Please log in to continue.');
    //     }

    //     $user = Auth::user();

    //     if ($user->role === 'Dosen' && $user->dosen->jabatan === 'Koordinator Program Studi') {
    //         $dosen = Dosen::where('user_id', Auth::id())
    //             ->where('jabatan', 'Koordinator Program Studi')
    //             ->firstOrFail();

    //         $jadwals = JadwalSeminarProposal::whereHas('mahasiswa', function ($query) use ($dosen) {
    //             $query->where('program_studi_id', $dosen->program_studi_id);
    //         })
    //             ->with(['mahasiswa', 'pengujiUtama', 'pengujiPendamping', 'ruanganSidang'])
    //             ->paginate(10);
    //     } elseif ($user->role === 'Dosen' || $user->role === 'Mahasiswa') {
    //         $jadwals = JadwalSeminarProposal::with([
    //             'mahasiswa',
    //             'pengujiUtama',
    //             'pengujiPendamping',
    //             'ruanganSidang'
    //         ])->paginate(10);
    //     } else {
    //         abort(403);
    //     }

    // $pengujiUtama = Dosen::all();
    // $pengujiPendamping = Dosen::all();
    // $ruanganSidang = RuanganSidang::all();

    //     return view('jadwal_sidang.jadwal_seminar_proposal', compact('jadwals', 'dosen', 'ruanganSidang', 'pengujiUtama', 'pengujiPendamping'));
    // }


    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,xlsx,xls|max:2048',
        ]);

        try {
            Excel::import(new JadwalSeminarProposalImport, $request->file('file'));
            return redirect()->route('jadwal_seminar_proposal.index')->with('success', 'Data jadwal seminar proposal berhasil diimpor.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengimpor data jadwal seminar proposal: ' . $e->getMessage());
        }
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'penguji_utama_id' => 'required|exists:dosen,id',
            'penguji_pendamping_id' => 'required|exists:dosen,id',
            'tanggal' => 'required|date|after_or_equal:today',
            'waktu_mulai' => 'required|date_format:H:i:s',
            'waktu_selesai' => 'required|date_format:H:i:s|after:waktu_mulai',
            'ruangan_sidang_id' => 'required|exists:ruangan_sidang,id',
        ]);


        $jadwalSeminarProposal = JadwalSeminarProposal::findOrFail($id);
        $jadwalSeminarProposal->update($request->all());

        return redirect()->route('jadwal_seminar_proposal.index')->with('success', 'Data jadwal seminar berhasil diperbarui');
    }

    public function dropdownSearch(Request $request)
    {
        if (!Auth::check()) {
            return redirect('/login')->with('message', 'Please log in to continue.');
        }

        $user = Auth::user();

        // Data pendukung untuk dropdown
        $programStudi = ProgramStudi::all();
        $pengujiUtama = Dosen::all();
        $pengujiPendamping = Dosen::all();
        $ruanganSidang = RuanganSidang::all();
        $tahunAjaranList = TahunAjaran::all();

        // Ambil input filter
        $programStudiId = $request->input('program_studi');
        $tahunAjaranId = $request->input('tahun_ajaran');
        $dosenId = $request->input('dosen_id');

        // Ambil semua jadwal seminar proposal dan filter sesuai input
        $jadwals = JadwalSeminarProposal::with([
            'mahasiswa.programStudi',
            'pengujiUtama',
            'pengujiPendamping',
            'ruanganSidang'
        ])
            ->when($programStudiId, function ($query) use ($programStudiId) {
                $query->whereHas('mahasiswa', function ($subQuery) use ($programStudiId) {
                    $subQuery->where('program_studi_id', $programStudiId);
                });
            })
            ->when($tahunAjaranId, function ($query) use ($tahunAjaranId) {
                $query->whereHas('mahasiswa', function ($subQuery) use ($tahunAjaranId) {
                    $subQuery->where('tahun_ajaran_id', $tahunAjaranId);
                });
            })
            ->when($dosenId, function ($query) use ($dosenId) {
                $query->where(function ($q) use ($dosenId) {
                    $q->where('penguji_utama_id', $dosenId)
                        ->orWhere('penguji_pendamping_id', $dosenId);
                });
            })
            ->get();

        // Grouping berdasarkan nama prodi
        $jadwalsGrouped = $jadwals->groupBy(function ($item) {
            return $item->mahasiswa->programStudi->nama ?? 'Tidak Diketahui';
        });

        return view('jadwal_sidang.jadwal_seminar_proposal', compact(
            'jadwals',
            'jadwalsGrouped',
            'programStudi',
            'tahunAjaranList',
            'user',
            'pengujiUtama',
            'pengujiPendamping',
            'ruanganSidang'
        ));
    }


    // public function dropdownSearch(Request $request)
    // {
    //     if (!Auth::check()) {
    //         return redirect('/login')->with('message', 'Please log in to continue.');
    //     }

    //     $user = Auth::user();

    //     $programStudi = ProgramStudi::all(); // Ambil daftar prodi
    //     $pengujiUtama = Dosen::all();
    //     $pengujiPendamping = Dosen::all();
    //     $ruanganSidang = RuanganSidang::all();


    //     // Ambil input filter
    //     $programStudiId = $request->input('program_studi');

    //     // Ambil semua jadwal seminar proposal dan filter jika ada pilihan program studi
    //     $jadwals = JadwalSeminarProposal::with([
    //         'mahasiswa.programStudi',
    //         'pengujiUtama',
    //         'pengujiPendamping',
    //         'ruanganSidang'
    //     ])
    //         ->when($programStudiId, function ($query) use ($programStudiId) {
    //             $query->whereHas('mahasiswa', function ($subQuery) use ($programStudiId) {
    //                 $subQuery->where('program_studi_id', $programStudiId);
    //             });
    //         })
    //         ->get();

    //     // Grouping berdasarkan nama prodi (tetap untuk tampilan per prodi)
    //     $jadwalsGrouped = $jadwals->groupBy(function ($item) {
    //         return $item->mahasiswa->programStudi->nama ?? 'Tidak Diketahui';
    //     });

    //     return view('jadwal_sidang.jadwal_seminar_proposal', compact('jadwals', 'jadwalsGrouped', 'programStudi', 'user', 'pengujiUtama', 'pengujiPendamping', 'ruanganSidang'));
    // }
}
