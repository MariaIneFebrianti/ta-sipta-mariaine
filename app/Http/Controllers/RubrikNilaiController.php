<?php

namespace App\Http\Controllers;

use App\Models\ProgramStudi;
use App\Models\RubrikNilai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

\Carbon\Carbon::setLocale('id');

class RubrikNilaiController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return redirect('/login')->with('message', 'Please log in to continue.');
        }
        $user = Auth::user();
        $dosen = $user->dosen;

        if ($user->role === 'Dosen' && ($user->dosen->jabatan === 'Koordinator Program Studi')) {
            // $rubrikNilai = RubrikNilai::paginate(10);
            $rubrikNilai = RubrikNilai::where('program_studi_id', $dosen->program_studi_id)
                ->orderByRaw("FIELD(jenis_dosen, 'Penguji Utama', 'Penguji Pendamping', 'Pembimbing Utama', 'Pembimbing Pendamping')")
                ->paginate(10);
        } elseif ($user->role === 'Dosen' && ($user->dosen->jabatan === 'Super Admin')) {
            $rubrikNilai = RubrikNilai::with('programStudi')
                ->orderBy('program_studi_id')
                ->orderByRaw("FIELD(jenis_dosen, 'Penguji Utama', 'Penguji Pendamping', 'Pembimbing Utama', 'Pembimbing Pendamping')")
                ->paginate(10);
        } else {
            abort(403);
        }

        $totalPerKategori = RubrikNilai::selectRaw('jenis_dosen, SUM(persentase) as total')
            ->where('program_studi_id', $dosen->program_studi_id)
            ->groupBy('jenis_dosen')
            ->pluck('total', 'jenis_dosen');

        $programStudiList = ProgramStudi::all();


        return view('rubrik_nilai.index', compact('rubrikNilai', 'user', 'totalPerKategori', 'programStudiList'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $programStudiId = $user->dosen->program_studi_id;

        $request->validate([
            'jenis_dosen' => 'required|string|max:255',
            'kelompok' => 'nullable|string|max:50',
            'kategori' => 'required|string|max:100',
            'persentase' => 'required|integer|min:1|max:100',
        ]);


        $existingTotal = RubrikNilai::where('jenis_dosen', $request->jenis_dosen)
            ->where('program_studi_id', $programStudiId)
            ->sum('persentase');

        // Jika total + persentase baru > 100, tolak
        if ($existingTotal + $request->persentase > 100) {
            return back()->withInput()->withErrors([
                'persentase' => 'Total persentase untuk jenis dosen ini sudah mencapai batas 100%.'
            ]);
        }

        // Simpan rubrik
        RubrikNilai::create([
            'program_studi_id' => $programStudiId,
            'jenis_dosen' => $request->jenis_dosen,
            'kelompok' => $request->kelompok,
            'kategori' => $request->kategori,
            'persentase' => $request->persentase,
        ]);

        return redirect()->route('rubrik_nilai.index')->with('success', 'Rubrik Nilai berhasil ditambahkan');
    }


    public function update(Request $request, string $id)
    {
        $request->validate([
            'jenis_dosen' => 'required|string|max:255',
            'kelompok' => 'nullable|string|max:50',
            'kategori' => 'required|string|max:100' . $id,
            'persentase' => 'required|integer|min:1|max:100',
        ]);

        $user = Auth::user();

        $rubrikNilai = RubrikNilai::findOrFail($id);

        $programStudiId = $user->dosen->program_studi_id;

        $existingTotal = RubrikNilai::where('jenis_dosen', $request->jenis_dosen)
            ->where('program_studi_id', $programStudiId)
            ->where('id', '!=', $id)
            ->sum('persentase');

        $newTotal = $existingTotal + $request->persentase;

        if ($newTotal > 100) {
            return back()->withInput()->withErrors([
                'persentase' => 'Total persentase untuk jenis dosen ini melebihi 100%.'
            ]);
        }

        // Lanjut update
        $rubrikNilai->update([
            'jenis_dosen' => $request->jenis_dosen,
            'kelompok' => $request->kelompok,
            'kategori' => $request->kategori,
            'persentase' => $request->persentase,
        ]);
        return redirect()->route('rubrik_nilai.index')->with('success', 'Rubrik Nilai berhasil diperbarui');
    }

    public function dropdownSearch(Request $request)
    {
        if (!Auth::check()) {
            return redirect('/login')->with('message', 'Please log in to continue.');
        }

        $user = Auth::user();
        $dosen = $user->dosen;
        $programStudiList = ProgramStudi::all();


        // $programStudiId = $user->dosen->program_studi_id;

        if ($user->role !== 'Dosen' || !in_array($user->dosen->jabatan, ['Koordinator Program Studi', 'Super Admin'])) {
            abort(403);
        }

        // Ambil nilai filter dari request
        $jenisDosen = $request->input('jenis_dosen');
        $programStudiId = $request->input('program_studi_id');

        // Siapkan query dasar
        $query = RubrikNilai::query();

        // Filter berdasarkan program studi
        if ($dosen->jabatan !== 'Super Admin') {
            // Kaprodi: hanya boleh melihat prodi miliknya
            $query->where('program_studi_id', $dosen->program_studi_id);
        } elseif ($programStudiId) {
            // Super Admin: jika memilih prodi tertentu
            $query->where('program_studi_id', $programStudiId);
        }

        if ($jenisDosen) {
            $query->where('jenis_dosen', $jenisDosen);
        }

        $rubrikNilai = $query->orderBy('jenis_dosen')->paginate(10);

        $totalPerKategori = RubrikNilai::selectRaw('jenis_dosen, SUM(persentase) as total')
            ->where('program_studi_id', $dosen->program_studi_id)
            ->groupBy('jenis_dosen')
            ->pluck('total', 'jenis_dosen');

        return view('rubrik_nilai.index', compact('rubrikNilai', 'jenisDosen', 'user', 'totalPerKategori', 'programStudiList'));
    }



    // public function search(Request $request)
    // {
    //     if (!Auth::check()) {
    //         return redirect('/login')->with('message', 'Please log in to continue.');
    //     }

    //     $user = Auth::user();

    //     $search = $request->input('search');;

    //     if ($user->role === 'Dosen' && ($user->dosen->jabatan === 'Koordinator Rubrik Nilai' || $user->dosen->jabatan === 'Super Admin')) {

    //         // Mengambil data pengguna berdasarkan pencarian kode prodi atau nama prodi
    //         $rubrikNilai = RubrikNilai::when($search, function ($query) use ($search) {
    //             return $query->where(function ($query) use ($search) {
    //                 $query->where('kode_prodi', 'like', "%$search%")
    //                     ->orWhere('nama_prodi', 'like', "%$search%");
    //             });
    //         })
    //             ->paginate(5);
    //     } else {
    //         abort(403);
    //     }

    //     return view('rubrik_nilai.index', compact('rubrikNilai', 'user'));
    // }

    public function destroy(string $id)
    {
        $rubrikNilai = RubrikNilai::findOrFail($id);
        $rubrikNilai->delete();
        return redirect()->route('rubrik_nilai.index')->with('success', 'Rubrik Nilai berhasil dihapus');
    }
}
