<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HasilAkhirSempro;
use App\Models\PenilaianSempro;
use App\Models\TahunAjaran;
use Illuminate\Support\Facades\Auth;

class HasilAkhirSemproController extends Controller
{
    // public function index()
    // {
    //     if (!Auth::check()) {
    //         return redirect('/login')->with('message', 'Please log in to continue.');
    //     }
    //     $user = Auth::user();
    //     $penilaianSempro = null;
    //     $hasilAkhirSempro = null;
    //     $dosen = $user->dosen;

    //     if ($user->role === 'Dosen' && $user->dosen) {
    //         $dosen = $user->dosen;

    //         if ($dosen->jabatan === 'Koordinator Program Studi') {
    //             $hasilAkhirSempro = HasilAkhirSempro::whereHas('mahasiswa', function ($query) use ($dosen) {
    //                 $query->where('program_studi_id', $dosen->program_studi_id);
    //             })->with('mahasiswa')->paginate(10);

    //             $penilaianTA = PenilaianSempro::whereHas('mahasiswa', function ($query) use ($dosen) {
    //                 $query->where('program_studi_id', $dosen->program_studi_id);
    //             })->with('mahasiswa')->get();
    //         } elseif ($dosen->jabatan === 'Super Admin') {
    //             $hasilAkhirSempro = HasilAkhirSempro::with('mahasiswa')->paginate(10);
    //             $penilaianTA = PenilaianSempro::with('mahasiswa')->get();
    //         }
    //     } elseif ($user->role === 'Mahasiswa' && $user->mahasiswa) {
    //         $mahasiswa = $user->mahasiswa;

    //         $hasilAkhirSempro = HasilAkhirSempro::where('mahasiswa_id', $mahasiswa->id)
    //             ->with('mahasiswa')
    //             ->paginate(10);

    //         $penilaianTA = PenilaianSempro::where('mahasiswa_id', $mahasiswa->id)
    //             ->with('mahasiswa')
    //             ->first(); // satu data saja untuk mahasiswa
    //     } else {
    //         abort(403, 'Unauthorized');
    //     }


    //     // Ambil list status & tahun untuk dropdown
    //     $statusList = HasilAkhirSempro::select('status_sidang')->distinct()->pluck('status_sidang')->filter()->unique()->values();

    //     // Tambahkan data penilaianTA ke setiap hasilAkhir
    //     foreach ($hasilAkhirSempro as $hasil) {
    //         $penilaian = PenilaianSempro::where('mahasiswa_id', $hasil->mahasiswa_id)->first();
    //         $hasil->penilaianTA = $penilaian; // tambahkan properti baru di objek
    //     }

    //     return view('hasil_akhir_sempro.index', compact('hasilAkhirSempro', 'statusList', 'user', 'penilaianTA'));
    // }


    public function index()
    {
        if (!Auth::check()) {
            return redirect('/login')->with('message', 'Please log in to continue.');
        }

        $user = Auth::user();
        $penilaianSempro = null;
        $hasilAkhirSempro = null;

        if ($user->role === 'Dosen' && $user->dosen) {
            $dosen = $user->dosen;

            if ($dosen->jabatan === 'Koordinator Program Studi') {
                $hasilAkhirSempro = HasilAkhirSempro::whereHas('mahasiswa', function ($query) use ($dosen) {
                    $query->where('program_studi_id', $dosen->program_studi_id);
                })
                    ->with('mahasiswa')
                    ->paginate(10);

                $penilaianSempro = PenilaianSempro::whereHas('mahasiswa', function ($query) use ($dosen) {
                    $query->where('program_studi_id', $dosen->program_studi_id);
                })->with('mahasiswa')->get();
            } elseif ($dosen->jabatan === 'Super Admin') {
                $hasilAkhirSempro = HasilAkhirSempro::with('mahasiswa')->paginate(10);
                $penilaianSempro = PenilaianSempro::with('mahasiswa')->get();
            }
        } elseif ($user->role === 'Mahasiswa' && $user->mahasiswa) {
            $mahasiswa = $user->mahasiswa;

            $hasilAkhirSempro = HasilAkhirSempro::where('mahasiswa_id', $mahasiswa->id)
                ->with('mahasiswa')
                ->paginate(10);

            $penilaianSempro = PenilaianSempro::where('mahasiswa_id', $mahasiswa->id)
                ->with('mahasiswa')
                ->first(); // satu data saja untuk mahasiswa
        } else {
            abort(403, 'Unauthorized');
        }

        // Ambil list status untuk dropdown
        $statusList = HasilAkhirSempro::select('status_sidang')->distinct()->pluck('status_sidang')->filter()->unique()->values();
        $tahunAjaranList = TahunAjaran::all();

        // Tambahkan penilaian ke masing-masing hasil
        foreach ($hasilAkhirSempro as $hasil) {
            $hasil->penilaianSempro = PenilaianSempro::where('mahasiswa_id', $hasil->mahasiswa_id)->first();
        }

        return view('hasil_akhir_sempro.index', compact('hasilAkhirSempro', 'statusList', 'tahunAjaranList', 'user', 'penilaianSempro'));
    }

    public function dropdownSearch(Request $request)
    {
        if (!Auth::check()) {
            return redirect('/login')->with('message', 'Please log in to continue.');
        }

        $user = Auth::user();
        $status = $request->input('status_sidang');
        $tahunAjaran = $request->input('tahun_ajaran'); // ✅ Ambil dari request

        $hasilAkhirSempro = HasilAkhirSempro::with('mahasiswa')
            ->when($status, fn($q) => $q->where('status_sidang', $status))
            ->when($tahunAjaran, function ($query) use ($tahunAjaran) {
                $query->whereHas('mahasiswa', function ($q) use ($tahunAjaran) {
                    $q->where('tahun_ajaran_id', $tahunAjaran);
                });
            })
            ->paginate(10);

        // Ambil daftar status sidang unik dari database
        $statusList = HasilAkhirSempro::select('status_sidang')
            ->distinct()
            ->pluck('status_sidang')
            ->filter()
            ->unique()
            ->values();

        // ✅ Ambil semua data tahun ajaran dari model TahunAjaran
        $tahunAjaranList = TahunAjaran::all();

        return view('hasil_akhir_sempro.index', compact(
            'hasilAkhirSempro',
            'statusList',
            'tahunAjaranList',
            'user',
        ));
    }
}
