<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\PengajuanPembimbing;
use Illuminate\Support\Facades\Auth;

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
        $tahunAjaranList = TahunAjaran::orderBy('tahun_ajaran', 'desc')->get();
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

        return view('pengajuan_pembimbing.index', compact('pengajuanPembimbing', 'dosen', 'user', 'proposal', 'tahunAjaranList'));
    }

    public function indexKaprodi()
    {
        if (!Auth::check()) {
            return redirect('/login')->with('message', 'Please log in to continue.');
        }

        $user = Auth::user();
        $dosen = $user->dosen;
        $tahunAjaranList = TahunAjaran::orderBy('tahun_ajaran', 'desc')->get();
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
        return view('pengajuan_pembimbing.index_kaprodi', compact('pengajuanPembimbing', 'tahunAjaranList', 'dosen', 'user'));
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

    // public function dropdownSearch(Request $request)
    // {
    //     $user = Auth::user();

    //     if (!$user || !$user->dosen) {
    //         return redirect('/login')->with('message', 'Unauthorized');
    //     }

    //     $dosen = $user->dosen;
    //     $dosenId = $dosen->id;
    //     $jabatan = $dosen->jabatan;
    //     $programStudiId = $dosen->program_studi_id;

    //     $dosen = Dosen::all(); // untuk dropdown
    //     $tahunAjaranList = Mahasiswa::select('tahun_ajaran_id')->distinct()->pluck('tahun_ajaran_id');

    //     // Ambil nilai dari form
    //     $pembimbingUtamaId = $request->input('pembimbing_utama_id');
    //     $pembimbingPendampingId = $request->input('pembimbing_pendamping_id');
    //     $tahunAjaranId = $request->input('tahun_ajaran_id');
    //     $validasi = $request->input('validasi');
    //     $searchType = $request->input('search_type');

    //     // Query dasar
    //     $pengajuanPembimbing = PengajuanPembimbing::with(['pembimbingUtama', 'pembimbingPendamping', 'mahasiswa']);

    //     if ($jabatan === 'Koordinator Program Studi') {
    //         $pengajuanPembimbing
    //             ->when($pembimbingUtamaId, fn($query) => $query->where('pembimbing_utama_id', $pembimbingUtamaId))
    //             ->when($pembimbingPendampingId, fn($query) => $query->where('pembimbing_pendamping_id', $pembimbingPendampingId))
    //             ->when($validasi, fn($query) => $query->where('validasi', $validasi))
    //             ->whereHas('mahasiswa', function ($query) use ($programStudiId, $tahunAjaranId) {
    //                 $query->where('program_studi_id', $programStudiId);
    //                 if ($tahunAjaranId) {
    //                     $query->where('tahun_ajaran_id', $tahunAjaranId);
    //                 }
    //             });
    //     } elseif ($jabatan === 'Super Admin') {
    //         $pengajuanPembimbing
    //             ->when($pembimbingUtamaId, fn($query) => $query->where('pembimbing_utama_id', $pembimbingUtamaId))
    //             ->when($pembimbingPendampingId, fn($query) => $query->where('pembimbing_pendamping_id', $pembimbingPendampingId))
    //             ->when($validasi, fn($query) => $query->where('validasi', $validasi))
    //             ->when($tahunAjaranId, function ($query) use ($tahunAjaranId) {
    //                 $query->whereHas('mahasiswa', function ($q) use ($tahunAjaranId) {
    //                     $q->where('tahun_ajaran_id', $tahunAjaranId);
    //                 });
    //             });
    //     } else {
    //         if ($searchType === 'Utama') {
    //             $pengajuanPembimbing->where('pembimbing_utama_id', $dosenId);
    //         } elseif ($searchType === 'Pendamping') {
    //             $pengajuanPembimbing->where('pembimbing_pendamping_id', $dosenId);
    //         } else {
    //             $pengajuanPembimbing = PengajuanPembimbing::where(function ($query) use ($dosenId) {
    //                 $query->where('pembimbing_utama_id', $dosenId)
    //                     ->orWhere('pembimbing_pendamping_id', $dosenId);
    //             })
    //                 ->where('validasi', 'Acc')
    //                 ->with(['pembimbingUtama', 'pembimbingPendamping', 'mahasiswa']);
    //         }

    //         if ($tahunAjaranId) {
    //             $pengajuanPembimbing->whereHas('mahasiswa', function ($query) use ($tahunAjaranId) {
    //                 $query->where('tahun_ajaran_id', $tahunAjaranId);
    //             });
    //         }
    //     }

    //     $pengajuanPembimbing = $pengajuanPembimbing->paginate(10);

    //     return view('pengajuan_pembimbing.index_kaprodi', compact('pengajuanPembimbing', 'dosen', 'user', 'tahunAjaranList'));
    // }


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
        $tahunAjaranList = TahunAjaran::orderBy('tahun_ajaran', 'desc')->get();

        // Ambil nilai dari form
        $pembimbingUtamaId = $request->input('pembimbing_utama_id');
        $pembimbingPendampingId = $request->input('pembimbing_pendamping_id');
        $tahunAjaranId = $request->input('tahun_ajaran_id');
        $validasi = $request->input('validasi');
        $searchType = $request->input('search_type');

        // Query dasar
        $pengajuanPembimbing = PengajuanPembimbing::with(['pembimbingUtama', 'pembimbingPendamping', 'mahasiswa']);

        if ($jabatan === 'Koordinator Program Studi') {
            $pengajuanPembimbing
                ->when($pembimbingUtamaId, fn($query) => $query->where('pembimbing_utama_id', $pembimbingUtamaId))
                ->when($pembimbingPendampingId, fn($query) => $query->where('pembimbing_pendamping_id', $pembimbingPendampingId))
                ->when($validasi, fn($query) => $query->where('validasi', $validasi))
                ->whereHas('mahasiswa', function ($query) use ($programStudiId, $tahunAjaranId) {
                    $query->where('program_studi_id', $programStudiId);
                    if ($tahunAjaranId) {
                        $query->where('tahun_ajaran_id', $tahunAjaranId);
                    }
                });
        } elseif ($jabatan === 'Super Admin') {
            $pengajuanPembimbing
                ->when($pembimbingUtamaId, fn($query) => $query->where('pembimbing_utama_id', $pembimbingUtamaId))
                ->when($pembimbingPendampingId, fn($query) => $query->where('pembimbing_pendamping_id', $pembimbingPendampingId))
                ->when($validasi, fn($query) => $query->where('validasi', $validasi))
                ->when($tahunAjaranId, function ($query) use ($tahunAjaranId) {
                    $query->whereHas('mahasiswa', function ($q) use ($tahunAjaranId) {
                        $q->where('tahun_ajaran_id', $tahunAjaranId);
                    });
                });
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

            if ($tahunAjaranId) {
                $pengajuanPembimbing->whereHas('mahasiswa', function ($query) use ($tahunAjaranId) {
                    $query->where('tahun_ajaran_id', $tahunAjaranId);
                });
            }
        }

        $pengajuanPembimbing = $pengajuanPembimbing->paginate(10);

        return view('pengajuan_pembimbing.index_kaprodi', compact('pengajuanPembimbing', 'dosen', 'user', 'tahunAjaranList'));
    }

    public function dropdownSearchDosen(Request $request)
    {
        $user = Auth::user();

        if (!$user || !$user->dosen) {
            return redirect('/login')->with('message', 'Unauthorized');
        }

        $dosenLogin = $user->dosen;
        $dosenId = $dosenLogin->id;
        $jabatan = $dosenLogin->jabatan;
        $programStudiId = $dosenLogin->program_studi_id;

        $dosen = Dosen::all(); // untuk dropdown dosen
        $tahunAjaranList = TahunAjaran::orderBy('tahun_ajaran', 'desc')->get();

        // Ambil nilai dari form
        $pembimbingUtamaId = $request->input('pembimbing_utama_id');
        $pembimbingPendampingId = $request->input('pembimbing_pendamping_id');
        $tahunAjaranId = $request->input('tahun_ajaran_id');
        $validasi = $request->input('validasi');
        $searchType = $request->input('search_type');

        // Query dasar
        $pengajuanPembimbing = PengajuanPembimbing::with(['pembimbingUtama', 'pembimbingPendamping', 'mahasiswa']);

        // Filter berdasarkan role
        if ($user->role === 'Dosen') {
            if ($searchType === 'Utama') {
                $pengajuanPembimbing->where('pembimbing_utama_id', $dosenId);
            } elseif ($searchType === 'Pendamping') {
                $pengajuanPembimbing->where('pembimbing_pendamping_id', $dosenId);
            } else {
                $pengajuanPembimbing->where(function ($query) use ($dosenId) {
                    $query->where('pembimbing_utama_id', $dosenId)
                        ->orWhere('pembimbing_pendamping_id', $dosenId);
                })->where('validasi', 'Acc');
            }
        } elseif ($user->role === 'Super Admin' || ($user->role === 'Dosen' && $jabatan === 'Koordinator Program Studi')) {
            // Filter berdasarkan dosen jika dipilih
            if ($pembimbingUtamaId) {
                $pengajuanPembimbing->where('pembimbing_utama_id', $pembimbingUtamaId);
            }
            if ($pembimbingPendampingId) {
                $pengajuanPembimbing->where('pembimbing_pendamping_id', $pembimbingPendampingId);
            }

            // Filter tahun ajaran (via relasi mahasiswa)
            if ($tahunAjaranId) {
                $pengajuanPembimbing->whereHas('mahasiswa', function ($query) use ($tahunAjaranId) {
                    $query->where('tahun_ajaran_id', $tahunAjaranId);
                });
            }
        }

        $pengajuanPembimbing = $pengajuanPembimbing->paginate(10);

        return view('pengajuan_pembimbing.index', compact('pengajuanPembimbing', 'dosen', 'tahunAjaranList', 'user'));
    }


    // public function dropdownSearchDosen(Request $request)
    // {
    //     $user = Auth::user();

    //     if (!$user || !$user->dosen) {
    //         return redirect('/login')->with('message', 'Unauthorized');
    //     }

    //     $dosen = $user->dosen;
    //     $dosenId = $dosen->id;
    //     $jabatan = $dosen->jabatan;
    //     $programStudiId = $dosen->program_studi_id;

    //     $dosen = Dosen::all(); // untuk dropdown

    //     // Ambil nilai dari form
    //     $pembimbingUtamaId = $request->input('pembimbing_utama_id');
    //     $pembimbingPendampingId = $request->input('pembimbing_pendamping_id');
    //     $validasi = $request->input('validasi');
    //     $searchType = $request->input('search_type');

    //     // Query dasar
    //     $pengajuanPembimbing = PengajuanPembimbing::with(['pembimbingUtama', 'pembimbingPendamping', 'mahasiswa']);


    //     if ($searchType === 'Utama') {
    //         $pengajuanPembimbing->where('pembimbing_utama_id', $dosenId);
    //     } elseif ($searchType === 'Pendamping') {
    //         $pengajuanPembimbing->where('pembimbing_pendamping_id', $dosenId);
    //     } else {
    //         $pengajuanPembimbing = PengajuanPembimbing::where(function ($query) use ($dosenId) {
    //             $query->where('pembimbing_utama_id', $dosenId)
    //                 ->orWhere('pembimbing_pendamping_id', $dosenId);
    //         })
    //             ->where('validasi', 'Acc')
    //             ->with(['pembimbingUtama', 'pembimbingPendamping', 'mahasiswa']);
    //     }


    //     $pengajuanPembimbing = $pengajuanPembimbing->paginate(10);

    //     return view('pengajuan_pembimbing.index', compact('pengajuanPembimbing', 'dosen', 'user'));
    // }


    public function destroy(string $id)
    {
        $pengajuanPembimbing = PengajuanPembimbing::findOrFail($id);
        $pengajuanPembimbing->delete();
        return redirect()->route('pengajuan_pembimbing.index')->with('success', 'Pengajuan Pembimbing berhasil dihapus');
    }

    public function rekapDosenPembimbing(Request $request)
    {
        $user = Auth::user();

        if (!$user || $user->dosen->jabatan !== 'Koordinator Program Studi') {
            return redirect('/login')->with('message', 'Unauthorized');
        }

        $programStudiId = $user->dosen->program_studi_id;
        $tahunAjaranId = $request->input('tahun_ajaran');

        $data = PengajuanPembimbing::with(['pembimbingUtama', 'pembimbingPendamping', 'mahasiswa'])
            ->whereHas('mahasiswa', function ($q) use ($programStudiId, $tahunAjaranId) {
                $q->where('program_studi_id', $programStudiId);
                if ($tahunAjaranId) {
                    $q->where('tahun_ajaran_id', $tahunAjaranId);
                }
            })
            ->where('validasi', 'Acc')
            ->get();

        $grouped = [];

        foreach ($data as $item) {
            $mhs = $item->mahasiswa;

            // Pembimbing Utama
            if ($item->pembimbingUtama) {
                $nama = $item->pembimbingUtama->nama_dosen;

                if (!isset($grouped[$nama])) {
                    $grouped[$nama] = [
                        'nama_dosen' => $nama,
                        'detail' => [],
                    ];
                }

                $grouped[$nama]['detail'][] = (object)[
                    'peran' => 'Pembimbing Utama',
                    'nim' => $mhs->nim,
                    'nama_mahasiswa' => $mhs->nama_mahasiswa,
                ];
            }

            // Pembimbing Pendamping
            if ($item->pembimbingPendamping) {
                $nama = $item->pembimbingPendamping->nama_dosen;

                if (!isset($grouped[$nama])) {
                    $grouped[$nama] = [
                        'nama_dosen' => $nama,
                        'detail' => [],
                    ];
                }

                $grouped[$nama]['detail'][] = (object)[
                    'peran' => 'Pembimbing Pendamping',
                    'nim' => $mhs->nim,
                    'nama_mahasiswa' => $mhs->nama_mahasiswa,
                ];
            }
        }

        $rekap = array_values($grouped);

        $pdf = Pdf::loadView('pengajuan_pembimbing.rekap_dosen', compact('rekap'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream('rekap_dosen_pembimbing.pdf');
    }


    // public function rekapDosenPembimbing(Request $request)
    // {
    //     $user = Auth::user();

    //     if (!$user || $user->dosen->jabatan !== 'Koordinator Program Studi') {
    //         return redirect('/login')->with('message', 'Unauthorized');
    //     }

    //     $programStudiId = $user->dosen->program_studi_id;
    //     $tahunAjaranId = $request->input('tahun_ajaran');

    //     $data = PengajuanPembimbing::with(['pembimbingUtama', 'pembimbingPendamping', 'mahasiswa'])
    //         ->whereHas('mahasiswa', function ($q) use ($programStudiId, $tahunAjaranId) {
    //             $q->where('program_studi_id', $programStudiId);
    //             if ($tahunAjaranId) {
    //                 $q->where('tahun_ajaran_id', $tahunAjaranId);
    //             }
    //         })
    //         ->where('validasi', 'Acc')
    //         ->get();

    //     $grouped = [];

    //     foreach ($data as $item) {
    //         // Pembimbing Utama
    //         if ($item->pembimbingUtama) {
    //             $key = $item->pembimbingUtama->id . '_utama';
    //             if (!isset($grouped[$key])) {
    //                 $grouped[$key] = [
    //                     'nama_dosen' => $item->pembimbingUtama->nama_dosen,
    //                     'peran' => 'Pembimbing Utama',
    //                     'mahasiswa' => [],
    //                 ];
    //             }
    //             $grouped[$key]['mahasiswa'][] = (object)[
    //                 'nama_mahasiswa' => $item->mahasiswa->nama_mahasiswa,
    //                 'nim' => $item->mahasiswa->nim,
    //             ];
    //         }

    //         // Pembimbing Pendamping
    //         if ($item->pembimbingPendamping) {
    //             $key = $item->pembimbingPendamping->id . '_pendamping';
    //             if (!isset($grouped[$key])) {
    //                 $grouped[$key] = [
    //                     'nama_dosen' => $item->pembimbingPendamping->nama_dosen,
    //                     'peran' => 'Pembimbing Pendamping',
    //                     'mahasiswa' => [],
    //                 ];
    //             }
    //             $grouped[$key]['mahasiswa'][] = (object)[
    //                 'nama_mahasiswa' => $item->mahasiswa->nama_mahasiswa,
    //                 'nim' => $item->mahasiswa->nim,
    //             ];
    //         }
    //     }

    //     $rekap = array_values($grouped); // reset keys agar bisa di-loop di Blade

    //     $pdf = Pdf::loadView('pengajuan_pembimbing.rekap_dosen', compact('rekap'))
    //         ->setPaper('a4', 'landscape');

    //     return $pdf->stream('rekap_dosen_pembimbing.pdf');
    // }


    // public function rekapDosenPembimbing(Request $request)
    // {
    //     $user = Auth::user();

    //     if (!$user || $user->dosen->jabatan !== 'Koordinator Program Studi') {
    //         return redirect('/login')->with('message', 'Unauthorized');
    //     }

    //     $programStudiId = $user->dosen->program_studi_id;
    //     $tahunAjaranId = $request->input('tahun_ajaran');

    //     $query = PengajuanPembimbing::with(['pembimbingUtama', 'pembimbingPendamping', 'mahasiswa'])
    //         ->whereHas('mahasiswa', function ($q) use ($programStudiId, $tahunAjaranId) {
    //             $q->where('program_studi_id', $programStudiId);
    //             if ($tahunAjaranId) {
    //                 $q->where('tahun_ajaran_id', $tahunAjaranId);
    //             }
    //         })
    //         ->where('validasi', 'Acc');

    //     $data = $query->get();

    //     $rekap = [];

    //     foreach ($data as $item) {
    //         if ($item->pembimbing_utama_id) {
    //             $rekap[] = [
    //                 'dosen_id' => $item->pembimbingUtama->id,
    //                 'nama_dosen' => $item->pembimbingUtama->nama_dosen,
    //                 'peran' => 'Pembimbing Utama',
    //                 'nama_mahasiswa' => $item->mahasiswa->nama_mahasiswa,
    //             ];
    //         }

    //         if ($item->pembimbing_pendamping_id) {
    //             $rekap[] = [
    //                 'dosen_id' => $item->pembimbingPendamping->id,
    //                 'nama_dosen' => $item->pembimbingPendamping->nama_dosen,
    //                 'peran' => 'Pembimbing Pendamping',
    //                 'nama_mahasiswa' => $item->mahasiswa->nama_mahasiswa,
    //             ];
    //         }
    //     }

    //     // Grouping dan total
    //     $grouped = collect($rekap)->groupBy(['dosen_id', 'peran']);

    //     $rekap = [];
    //     $no = 1;

    //     foreach ($grouped as $dosenId => $roles) {
    //         $namaDosen = $roles->first()?->first()['nama_dosen'] ?? '-';

    //         foreach (['Pembimbing Utama', 'Pembimbing Pendamping'] as $peran) {
    //             if (!isset($roles[$peran])) continue;

    //             $mahasiswaList = $roles[$peran];

    //             foreach ($mahasiswaList as $entry) {
    //                 $rekap[] = [
    //                     'no' => $no++,
    //                     'nama_dosen' => $namaDosen,
    //                     'peran' => $peran,
    //                     'nama_mahasiswa' => $entry['nama_mahasiswa'],
    //                 ];
    //             }

    //             // Total
    //             $rekap[] = [
    //                 'no' => '',
    //                 'nama_dosen' => '',
    //                 'peran' => 'Total Bimbingan',
    //                 'nama_mahasiswa' => count($mahasiswaList),
    //             ];
    //         }
    //     }

    //     $pdf = Pdf::loadView('pengajuan_pembimbing.rekap_dosen', compact('rekap'))->setPaper('a4', 'portrait');
    //     return $pdf->stream('rekap_dosen_pembimbing.pdf');
    // }
}
