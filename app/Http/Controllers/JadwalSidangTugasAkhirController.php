<?php

namespace App\Http\Controllers;

use App\Imports\JadwalSidangTugasAkhirImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\JadwalSidangTugasAkhir;
use Maatwebsite\Excel\Facades\Excel;

class JadwalSidangTugasAkhirController extends Controller
{
    public function index()
    {
        $userRole = Auth::user()->role;
        // Mengambil data jadwal sidang tugas akhir beserta relasi terkait
        $jadwals = JadwalSidangTugasAkhir::with([
            'mahasiswa',
            'pembimbingUtama',
            'pembimbingPendamping',
            'pengujiUtama',
            'pengujiPendamping',
            'ruanganSidang'
        ])->paginate(10);

        // Menampilkan data ke view
        return view('jadwal_sidang.jadwal_sidang_tugas_akhir', compact('jadwals', 'userRole'));
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
