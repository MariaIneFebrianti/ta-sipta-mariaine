<?php

namespace App\Http\Controllers;

use App\Imports\JadwalSeminarProposalImport;
use App\Imports\JadwalSidangTugasAkhirImport;
use App\Models\Dosen;
use App\Models\JadwalSeminarProposal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\JadwalSidangTugasAkhir;
use Maatwebsite\Excel\Facades\Excel;

\Carbon\Carbon::setLocale('id');


class JadwalSeminarProposalController extends Controller
{
    // KESELURUHAN
    public function index()
    {
        if (Auth::check()) {
            $userRole = Auth::user()->role;
        } else {
            return redirect('/login')->with('message', 'Please log in to continue.');
        }
        // Mengambil data jadwal sidang tugas akhir beserta relasi terkait
        $jadwals = JadwalSeminarProposal::with([
            'mahasiswa',
            'pengujiUtama',
            'pengujiPendamping',
            'ruanganSidang'
        ])->paginate(10);

        // Menampilkan data ke view
        return view('jadwal_sidang.jadwal_seminar_proposal', compact('jadwals', 'userRole'));
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

    //     return view('jadwal_sidang.jadwal_seminar_proposal', compact('jadwals'));
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
}
