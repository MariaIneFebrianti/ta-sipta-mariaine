<?php

namespace App\Http\Controllers;

use App\Imports\JadwalSidangTugasAkhirImport;
use App\Models\Dosen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\JadwalSidangTugasAkhir;
use App\Models\ProgramStudi;
use App\Models\RuanganSidang;
use App\Models\TahunAjaran;
use Maatwebsite\Excel\Facades\Excel;

\Carbon\Carbon::setLocale('id');

class JadwalSidangTugasAkhirController extends Controller
{
    // public function index()
    // {
    //     if (!Auth::check()) {
    //         return redirect('/login')->with('message', 'Please log in to continue.');
    //     }

    //     // Mengambil data jadwal sidang tugas akhir beserta relasi terkait
    //     $jadwals = JadwalSidangTugasAkhir::with([
    //         'mahasiswa',
    //         'pembimbingUtama',
    //         'pembimbingPendamping',
    //         'pengujiUtama',
    //         'pengujiPendamping',
    //         'ruanganSidang'
    //     ])->paginate(10);

    //     $pembimbingUtama = Dosen::all();
    //     $pembimbingPendamping = Dosen::all();
    //     $pengujiUtama = Dosen::all();
    //     $pengujiPendamping = Dosen::all();
    //     $ruanganSidang = RuanganSidang::all();



    //     // Menampilkan data ke view
    //     return view('jadwal_sidang.jadwal_sidang_tugas_akhir', compact('jadwals', 'ruanganSidang', 'pembimbingUtama', 'pembimbingPendamping', 'pengujiUtama', 'pengujiPendamping'));
    // }

    public function index()
    {
        if (!Auth::check()) {
            return redirect('/login')->with('message', 'Please log in to continue.');
        }

        // Ambil semua jadwal sidang tugas akhir lengkap dengan relasi
        $jadwals = JadwalSidangTugasAkhir::with([
            'mahasiswa.programStudi',      // pastikan relasi programStudi ada di model Mahasiswa
            'pembimbingUtama',
            'pembimbingPendamping',
            'pengujiUtama',
            'pengujiPendamping',
            'ruanganSidang',
        ])->get(); // Tanpa paginate supaya mudah groupBy

        // Group berdasarkan nama program studi mahasiswa
        $jadwalsGrouped = $jadwals->groupBy(function ($item) {
            return $item->mahasiswa->programStudi->nama_prodi ?? 'Tidak Diketahui';
        });

        // Ambil data dosen dan ruangan untuk kebutuhan dropdown / filter di view
        $programStudi = ProgramStudi::all();
        $pembimbingUtama = Dosen::all();
        $pembimbingPendamping = Dosen::all();
        $pengujiUtama = Dosen::all();
        $pengujiPendamping = Dosen::all();
        $ruanganSidang = RuanganSidang::all();
        $tahunAjaranList = TahunAjaran::all();


        // Kirim data ke view
        return view('jadwal_sidang.jadwal_sidang_tugas_akhir', compact(
            'jadwals',
            'jadwalsGrouped',
            'pembimbingUtama',
            'pembimbingPendamping',
            'pengujiUtama',
            'pengujiPendamping',
            'ruanganSidang',
            'programStudi',
            'tahunAjaranList',
        ));
    }


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

    //         $jadwals = JadwalSidangTugasAkhir::whereHas('mahasiswa', function ($query) use ($dosen) {
    //             $query->where('program_studi_id', $dosen->program_studi_id);
    //         })
    //             ->with([
    //                 'mahasiswa',
    //                 'pembimbingUtama',
    //                 'pembimbingPendamping',
    //                 'pengujiUtama',
    //                 'pengujiPendamping',
    //                 'ruanganSidang'
    //             ])
    //             ->paginate(10);
    //     } elseif ($user->role === 'Dosen' || $user->role === 'Mahasiswa') {
    //         // Mengambil data jadwal sidang tugas akhir beserta relasi terkait
    //         $jadwals = JadwalSidangTugasAkhir::with([
    //             'mahasiswa',
    //             'pembimbingUtama',
    //             'pembimbingPendamping',
    //             'pengujiUtama',
    //             'pengujiPendamping',
    //             'ruanganSidang'
    //         ])->paginate(10);
    //     } else {
    //         abort(403);
    //     }

    // $pengujiPendamping = Dosen::all();
    // $ruanganSidang = RuanganSidang::all();    // $ruanganSidang = RuanganSidang::all();


    //     // Menampilkan data ke view
    //     return view('jadwal_sidang.jadwal_sidang_tugas_akhir', compact('jadwals', 'dosen', 'ruanganSidang', 'pengujiUtama', 'pengujiPendamping'));
    // }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,xlsx,xls|max:2048',
        ]);

        try {
            Excel::import(new JadwalSidangTugasAkhirImport, $request->file('file'));
            return redirect()->route('jadwal_sidang_tugas_akhir.index')->with('success', 'Data jadwal sidang tugas akhir berhasil diimpor.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengimpor data jadwal sidang tugas akhir: ' . $e->getMessage());
        }
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'pembimbing_utama_id' => 'required|exists:dosen,id',
            'pembimbing_pendamping_id' => 'required|exists:dosen,id',
            'penguji_utama_id' => 'required|exists:dosen,id',
            'penguji_pendamping_id' => 'required|exists:dosen,id',
            'tanggal' => 'required|date|after_or_equal:today',
            'waktu_mulai' => 'required|date_format:H:i:s',
            'waktu_selesai' => 'required|date_format:H:i:s|after:waktu_mulai',
            'ruangan_sidang_id' => 'required|exists:ruangan_sidang,id',
        ]);

        $jadwalSidangTugasAkhir = JadwalSidangTugasAkhir::findOrFail($id);
        $jadwalSidangTugasAkhir->update($request->all());

        return redirect()->route('jadwal_sidang_tugas_akhir.index')->with('success', 'Data jadwal sidang tugas akhir berhasil diperbarui');
    }

    // public function dropdownSearch(Request $request)
    // {
    //     if (!Auth::check()) {
    //         return redirect('/login')->with('message', 'Please log in to continue.');
    //     }

    //     $user = Auth::user();

    //     $programStudi = ProgramStudi::all(); // Ambil daftar prodi
    //     $pembimbingUtama = Dosen::all();
    //     $pembimbingPendamping = Dosen::all();
    //     $pengujiUtama = Dosen::all();
    //     $pengujiPendamping = Dosen::all();
    //     $ruanganSidang = RuanganSidang::all();


    //     // Ambil input filter
    //     $programStudiId = $request->input('program_studi');

    //     // Ambil semua jadwal seminar proposal dan filter jika ada pilihan program studi
    //     $jadwals = JadwalSidangTugasAkhir::with([
    //         'mahasiswa.programStudi',
    //         'pembimbingUtama',
    //         'pembimbingPendamping',
    //         'pengujiUtama',
    //         'pengujiPendamping',
    //         'ruanganSidang',
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

    //     return view('jadwal_sidang.jadwal_sidang_tugas_akhir', compact('jadwals', 'jadwalsGrouped', 'programStudi', 'user', 'pembimbingUtama', 'pembimbingPendamping', 'pengujiUtama', 'pengujiPendamping', 'ruanganSidang'));
    // }

    public function dropdownSearch(Request $request)
    {
        if (!Auth::check()) {
            return redirect('/login')->with('message', 'Please log in to continue.');
        }

        $user = Auth::user();

        $programStudi = ProgramStudi::all(); // Daftar prodi
        $pembimbingUtama = Dosen::all();
        $pembimbingPendamping = Dosen::all();
        $pengujiUtama = Dosen::all();
        $pengujiPendamping = Dosen::all();
        $ruanganSidang = RuanganSidang::all();

        // 🔍 Ambil input filter
        $programStudiId = $request->input('program_studi');
        $tahunAjaran = $request->input('tahun_ajaran');
        $dosenId = $request->input('dosen_id');

        // 🔎 Ambil dan filter jadwal
        $jadwals = JadwalSidangTugasAkhir::with([
            'mahasiswa.programStudi',
            'pembimbingUtama',
            'pembimbingPendamping',
            'pengujiUtama',
            'pengujiPendamping',
            'ruanganSidang',
        ])
            ->when($programStudiId, function ($query) use ($programStudiId) {
                $query->whereHas('mahasiswa', function ($q) use ($programStudiId) {
                    $q->where('program_studi_id', $programStudiId);
                });
            })
            ->when($tahunAjaran, function ($query) use ($tahunAjaran) {
                $query->whereHas('mahasiswa', function ($q) use ($tahunAjaran) {
                    $q->where('tahun_ajaran_id', $tahunAjaran);
                });
            })
            ->when($dosenId, function ($query) use ($dosenId) {
                $query->where(function ($q) use ($dosenId) {
                    $q->where('pembimbing_utama_id', $dosenId)
                        ->orWhere('pembimbing_pendamping_id', $dosenId)
                        ->orWhere('penguji_utama_id', $dosenId)
                        ->orWhere('penguji_pendamping_id', $dosenId);
                });
            })
            ->get();

        // 🔁 Grouping berdasarkan nama prodi
        $jadwalsGrouped = $jadwals->groupBy(function ($item) {
            return $item->mahasiswa->programStudi->nama ?? 'Tidak Diketahui';
        });

        // Ambil daftar tahun ajaran dari model TahunAjaran
        $tahunAjaranList = TahunAjaran::all();

        return view('jadwal_sidang.jadwal_sidang_tugas_akhir', compact(
            'jadwals',
            'jadwalsGrouped',
            'programStudi',
            'user',
            'pembimbingUtama',
            'pembimbingPendamping',
            'pengujiUtama',
            'pengujiPendamping',
            'ruanganSidang',
            'tahunAjaranList',
            'dosenId',
            'tahunAjaran'
        ));
    }
}
