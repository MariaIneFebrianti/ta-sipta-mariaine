<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\PengajuanPembimbing;
use Illuminate\Http\Request;

\Carbon\Carbon::setLocale('id');


class PengajuanPembimbingController extends Controller
{
    // public function index()
    // {
    //     if (!Auth::check()) {
    //         return redirect('/login')->with('message', 'Please log in to continue.');
    //     }

    //     $user = Auth::user();
    //     $dosen = $user->dosen;
    //     $pengajuanQuery = PengajuanPembimbing::with(['pembimbingUtama', 'pembimbingPendamping', 'mahasiswa']);

    //     if ($user->role === 'Mahasiswa') {
    //         $pengajuanPembimbing = $pengajuanQuery
    //             ->where('mahasiswa_id', $user->mahasiswa->id)
    //             ->paginate(5);
    //     } elseif ($user->role === 'Dosen' && $dosen) {

    //         if ($dosen->jabatan === 'Super Admin') {
    //             $pengajuanPembimbing = $pengajuanQuery->paginate(5);
    //         } else {
    //             $pengajuanPembimbing = $pengajuanQuery
    //                 ->where(function ($query) use ($dosen) {
    //                     $query->where('pembimbing_utama_id', $dosen->id)
    //                         ->orWhere('pembimbing_pendamping_id', $dosen->id);
    //                 })
    //                 ->where('validasi', 'Acc')
    //                 ->paginate(5);
    //         }
    //     } else {
    //         abort(403, 'Unauthorized access.');
    //     }

    //     $dosen = Dosen::all();
    //     return view('pengajuan_pembimbing.index', compact('pengajuanPembimbing', 'dosen', 'user'));
    // }


    public function index()
    {
        if (!Auth::check()) {
            return redirect('/login')->with('message', 'Please log in to continue.');
        }

        $user = Auth::user();
        $dosen = $user->dosen;
        $pengajuanQuery = PengajuanPembimbing::with(['pembimbingUtama', 'pembimbingPendamping', 'mahasiswa']);

        if ($user->role === 'Mahasiswa') {
            $pengajuanPembimbing = $pengajuanQuery
                ->where('mahasiswa_id', $user->mahasiswa->id)
                ->paginate(10);
        } elseif ($user->role === 'Dosen' && $dosen) {
            $pengajuanPembimbing = $pengajuanQuery
                ->where(function ($query) use ($dosen) {
                    $query->where('pembimbing_utama_id', $dosen->id)
                        ->orWhere('pembimbing_pendamping_id', $dosen->id);
                })
                ->where('validasi', 'Acc')
                ->paginate(10);
        } else {
            abort(403, 'Unauthorized access.');
        }

        $dosen = Dosen::all();
        // Ambil mahasiswa berdasarkan ID
        // $mahasiswaId = Auth::user()->mahasiswa_id;
        // $mahasiswa = Mahasiswa::find($mahasiswaId);

        // $mahasiswa = Mahasiswa::find($mahasiswaId);
        // $proposal = $mahasiswa->proposal;
        $mahasiswa = $user->mahasiswa;
        $proposal = $mahasiswa ? $mahasiswa->proposal : null;

        return view('pengajuan_pembimbing.index', compact('pengajuanPembimbing', 'dosen', 'user', 'proposal'));
    }

    public function indexKaprodi()
    {
        if (!Auth::check()) {
            return redirect('/login')->with('message', 'Please log in to continue.');
        }

        $user = Auth::user();
        $dosen = $user->dosen;
        $pengajuanQuery = PengajuanPembimbing::with(['pembimbingUtama', 'pembimbingPendamping', 'mahasiswa']);

        if ($dosen->jabatan === 'Koordinator Program Studi') {
            $pengajuanPembimbing = $pengajuanQuery
                ->whereHas('mahasiswa', function ($query) use ($dosen) {
                    $query->where('program_studi_id', $dosen->program_studi_id);
                })
                ->paginate(10);
        } elseif ($dosen->jabatan === 'Super Admin') {
            $pengajuanPembimbing = $pengajuanQuery->paginate(10);
        } else {
            abort(403, 'Unauthorized access.');
        }

        $dosen = Dosen::all();
        return view('pengajuan_pembimbing.index_kaprodi', compact('pengajuanPembimbing', 'dosen', 'user'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'pembimbing_utama_id' => 'required|exists:dosen,id',
            'pembimbing_pendamping_id' => 'required|exists:dosen,id|different:pembimbing_utama_id',
        ]);

        $mahasiswaId = Auth::user()->mahasiswa->id;

        PengajuanPembimbing::create([
            'mahasiswa_id' => $mahasiswaId,
            'pembimbing_utama_id' => $request->pembimbing_utama_id,
            'pembimbing_pendamping_id' => $request->pembimbing_pendamping_id,
        ]);

        return redirect()->route('pengajuan_pembimbing.index')->with('success', 'Pengajuan Pembimbing berhasil ditambahkan');
    }

    public function validasi($id)
    {
        $pengajuan = PengajuanPembimbing::findOrFail($id);
        $pengajuan->validasi = 'Acc';
        $pengajuan->save();

        return redirect()->back()->with('success', 'Status validasi berhasil diperbarui menjadi Acc.');
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'pembimbing_utama_id' => 'required|exists:dosen,id',
            'pembimbing_pendamping_id' => 'required|exists:dosen,id|different:pembimbing_utama_id',
        ]);

        $pengajuanPembimbing = PengajuanPembimbing::findOrFail($id);
        $pengajuanPembimbing->update($request->all());

        // Update field validasi
        $pengajuanPembimbing->validasi = 'Acc';
        $pengajuanPembimbing->save();
        return redirect()->route('pengajuan_pembimbing.index_kaprodi')->with('success', 'Pengajuan Pembimbing berhasil diperbarui');
    }

    public function search(Request $request)
    {
        $search = $request->input('search'); // Ambil input pencarian
        $user = Auth::user();

        // Pastikan user adalah dosen
        if (!$user || !$user->dosen) {
            return redirect('/login')->with('message', 'Unauthorized');
        }

        $jabatan = $user->dosen->jabatan;
        $programStudiId = $user->dosen->program_studi_id;

        // Query awal
        $pengajuanPembimbing = PengajuanPembimbing::with(['mahasiswa', 'tahunAjaran', 'pembimbingUtama', 'pembimbingPendamping'])
            ->when($search, function ($query) use ($search) {
                $query->whereHas('mahasiswa', function ($q) use ($search) {
                    $q->where('nama_mahasiswa', 'like', "%$search%");
                })->orWhereHas('tahunAjaran', function ($q) use ($search) {
                    $q->where('tahun_ajaran', 'like', "%$search%");
                });
            });

        // Jika Kaprodi, filter berdasarkan program studi
        if ($jabatan === 'Koordinator Program Studi') {
            $pengajuanPembimbing->whereHas('mahasiswa', function ($query) use ($programStudiId) {
                $query->where('program_studi_id', $programStudiId);
            });
        }

        $pengajuanPembimbing = $pengajuanPembimbing->paginate(10);

        return view('pengajuan_pembimbing.index', compact('pengajuanPembimbing'));
    }

    public function dropdownSearch(Request $request)
    {
        $user = Auth::user();

        if (!$user || !$user->dosen) {
            return redirect('/login')->with('message', 'Unauthorized');
        }

        $dosen = $user->dosen;
        $dosenId = $dosen->id;
        $jabatan = $dosen->jabatan;
        $programStudiId = $dosen->program_studi_id;

        $dosen = Dosen::all(); // untuk dropdown

        // Ambil nilai dari form
        $pembimbingUtamaId = $request->input('pembimbing_utama_id');
        $pembimbingPendampingId = $request->input('pembimbing_pendamping_id');
        $validasi = $request->input('validasi');
        $searchType = $request->input('search_type');

        // Query dasar
        $pengajuanPembimbing = PengajuanPembimbing::with(['pembimbingUtama', 'pembimbingPendamping', 'mahasiswa']);

        if ($jabatan === 'Koordinator Program Studi') {
            $pengajuanPembimbing
                ->when($pembimbingUtamaId, fn($query) => $query->where('pembimbing_utama_id', $pembimbingUtamaId))
                ->when($pembimbingPendampingId, fn($query) => $query->where('pembimbing_pendamping_id', $pembimbingPendampingId))
                ->when($validasi, fn($query) => $query->where('validasi', $validasi))
                ->whereHas('mahasiswa', function ($query) use ($programStudiId) {
                    $query->where('program_studi_id', $programStudiId);
                });
        } elseif ($jabatan === 'Super Admin') {
            $pengajuanPembimbing
                ->when($pembimbingUtamaId, fn($query) => $query->where('pembimbing_utama_id', $pembimbingUtamaId))
                ->when($pembimbingPendampingId, fn($query) => $query->where('pembimbing_pendamping_id', $pembimbingPendampingId))
                ->when($validasi, fn($query) => $query->where('validasi', $validasi));
        } else {
            if ($searchType === 'Utama') {
                $pengajuanPembimbing->where('pembimbing_utama_id', $dosenId);
            } elseif ($searchType === 'Pendamping') {
                $pengajuanPembimbing->where('pembimbing_pendamping_id', $dosenId);
            } else {
                $pengajuanPembimbing = PengajuanPembimbing::where(function ($query) use ($dosenId) {
                    $query->where('pembimbing_utama_id', $dosenId)
                        ->orWhere('pembimbing_pendamping_id', $dosenId);
                })
                    ->where('validasi', 'Acc')
                    ->with(['pembimbingUtama', 'pembimbingPendamping', 'mahasiswa']);
            }
        }

        $pengajuanPembimbing = $pengajuanPembimbing->paginate(10);

        return view('pengajuan_pembimbing.index_kaprodi', compact('pengajuanPembimbing', 'dosen', 'user'));
    }

    public function dropdownSearchDosen(Request $request)
    {
        $user = Auth::user();

        if (!$user || !$user->dosen) {
            return redirect('/login')->with('message', 'Unauthorized');
        }

        $dosen = $user->dosen;
        $dosenId = $dosen->id;
        $jabatan = $dosen->jabatan;
        $programStudiId = $dosen->program_studi_id;

        $dosen = Dosen::all(); // untuk dropdown

        // Ambil nilai dari form
        $pembimbingUtamaId = $request->input('pembimbing_utama_id');
        $pembimbingPendampingId = $request->input('pembimbing_pendamping_id');
        $validasi = $request->input('validasi');
        $searchType = $request->input('search_type');

        // Query dasar
        $pengajuanPembimbing = PengajuanPembimbing::with(['pembimbingUtama', 'pembimbingPendamping', 'mahasiswa']);


        if ($searchType === 'Utama') {
            $pengajuanPembimbing->where('pembimbing_utama_id', $dosenId);
        } elseif ($searchType === 'Pendamping') {
            $pengajuanPembimbing->where('pembimbing_pendamping_id', $dosenId);
        } else {
            $pengajuanPembimbing = PengajuanPembimbing::where(function ($query) use ($dosenId) {
                $query->where('pembimbing_utama_id', $dosenId)
                    ->orWhere('pembimbing_pendamping_id', $dosenId);
            })
                ->where('validasi', 'Acc')
                ->with(['pembimbingUtama', 'pembimbingPendamping', 'mahasiswa']);
        }


        $pengajuanPembimbing = $pengajuanPembimbing->paginate(10);

        return view('pengajuan_pembimbing.index', compact('pengajuanPembimbing', 'dosen', 'user'));
    }


    public function destroy(string $id)
    {
        $pengajuanPembimbing = PengajuanPembimbing::findOrFail($id);
        $pengajuanPembimbing->delete();
        return redirect()->route('pengajuan_pembimbing.index')->with('success', 'Pengajuan Pembimbing berhasil dihapus');
    }
}
