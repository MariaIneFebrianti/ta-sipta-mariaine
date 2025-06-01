<?php

namespace App\Http\Controllers;

use App\Imports\JadwalSidangTugasAkhirImport;
use App\Models\Dosen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\JadwalSidangTugasAkhir;
use Maatwebsite\Excel\Facades\Excel;

\Carbon\Carbon::setLocale('id');

class JadwalSidangTugasAkhirController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return redirect('/login')->with('message', 'Please log in to continue.');
        }

        $user = Auth::user();

        if ($user->role === 'Dosen' && $user->dosen->jabatan === 'Koordinator Program Studi') {
            $dosen = Dosen::where('user_id', Auth::id())
                ->where('jabatan', 'Koordinator Program Studi')
                ->firstOrFail();

            $jadwals = JadwalSidangTugasAkhir::whereHas('mahasiswa', function ($query) use ($dosen) {
                $query->where('program_studi_id', $dosen->program_studi_id);
            })
                ->with([
                    'mahasiswa',
                    'pembimbingUtama',
                    'pembimbingPendamping',
                    'pengujiUtama',
                    'pengujiPendamping',
                    'ruanganSidang'
                ])
                ->paginate(10);
        } elseif ($user->role === 'Dosen' || $user->role === 'Mahasiswa') {
            // Mengambil data jadwal sidang tugas akhir beserta relasi terkait
            $jadwals = JadwalSidangTugasAkhir::with([
                'mahasiswa',
                'pembimbingUtama',
                'pembimbingPendamping',
                'pengujiUtama',
                'pengujiPendamping',
                'ruanganSidang'
            ])->paginate(10);
        } else {
            abort(403);
        }

        // Menampilkan data ke view
        return view('jadwal_sidang.jadwal_sidang_tugas_akhir', compact('jadwals'));
    }

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
}
