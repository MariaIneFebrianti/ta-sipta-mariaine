<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\PengajuanPembimbing;
use Illuminate\Http\Request;

class PengajuanPembimbingController extends Controller
{
    public function index()
    {
        $userRole = Auth::user()->role;
        if ($userRole === 'Mahasiswa') {
            // Mahasiswa hanya melihat pengajuannya sendiri
            $pengajuanPembimbing = PengajuanPembimbing::where('mahasiswa_id', Auth::user()->mahasiswa->id)
                ->with(['pembimbingUtama', 'pembimbingPendamping', 'mahasiswa'])
                ->paginate(5);
        } elseif ($userRole === 'Dosen') {
            // Ambil ID dosen dari user yang login
            // $dosenId = Auth::user()->dosen->id;
            $dosenId = Auth::user()->dosen->id;

            // Query hanya menampilkan pengajuan di mana dosen login sebagai pembimbing utama atau pendamping
            $pengajuanPembimbing = PengajuanPembimbing::where(function ($query) use ($dosenId) {
                $query->where('pembimbing_utama_id', $dosenId)
                    ->orWhere('pembimbing_pendamping_id', $dosenId);
            })
                ->where('validasi', 'Acc')
                ->with(['pembimbingUtama', 'pembimbingPendamping', 'mahasiswa'])
                ->paginate(5);
        } else {
            // Jika role lain (Kooordinator Program Studi atau Super Admin), tampilkan semua pengajuan
            $pengajuanPembimbing = PengajuanPembimbing::with(['pembimbingUtama', 'pembimbingPendamping', 'mahasiswa'])
                ->paginate(5);
        }

        $dosen = Dosen::all();
        // $mahasiswa = Mahasiswa::all();
        // $mahasiswa = Mahasiswa::find($id);
        return view('pengajuan_pembimbing.index', compact('pengajuanPembimbing', 'dosen', 'userRole'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'pembimbing_utama_id' => 'required|exists:dosen,id',
            'pembimbing_pendamping_id' => 'required|exists:dosen,id',
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
        $pengajuan->validasi = 'Acc';  // Set validasi menjadi Acc
        $pengajuan->save();

        return redirect()->back()->with('success', 'Status validasi berhasil diperbarui menjadi Acc.');
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'pembimbing_utama_id' => 'required|exists:dosen,id',
            'pembimbing_pendamping_id' => 'required|exists:dosen,id',
        ]);

        $pengajuanPembimbing = PengajuanPembimbing::findOrFail($id);
        $pengajuanPembimbing->update($request->all());

        // Update field validasi
        $pengajuanPembimbing->validasi = 'Acc';
        $pengajuanPembimbing->save();
        return redirect()->route('pengajuan_pembimbing.index')->with('success', 'Pengajuan Pembimbing berhasil diperbarui');
    }

    public function search(Request $request)
    {
        $pengajuanPembimbing = PengajuanPembimbing::all();
        $search = $request->input('search'); // Ambil input pencarian

        // Mengambil data pengguna berdasarkan pencarian nama mahasiswa atau tahun ajaran
        $pengajuanPembimbing = PengajuanPembimbing::when($search, function ($query) use ($search) {
            return $query->where(function ($query) use ($search) {
                $query->where('mahasiswa', function ($query) use ($search) {
                    $query->where('nama_mahasiswa', 'like', "%$search%");
                })
                    ->orWhereHas('tahunAjaran', function ($query) use ($search) {
                        $query->where('tahun_ajaran', 'like', "%$search%");
                    });
            });
        })
            ->paginate(5);

        return view('ruangan_sidang.index', compact('ruanganSidang', 'programStudi'));
    }

    public function dropdownSearch(Request $request)
    {
        $userRole = Auth::user()->role;

        // Ambil semua dosen
        $dosen = Dosen::all();

        // Ambil nilai dari dropdown
        $pembimbingUtamaId = $request->input('pembimbing_utama_id');
        $pembimbingPendampingId = $request->input('pembimbing_pendamping_id');
        $validasi = $request->input('validasi'); // Ambil nilai validasi

        // Query untuk mencari pengajuan pembimbing
        $pengajuanPembimbing = PengajuanPembimbing::when($pembimbingUtamaId, function ($query) use ($pembimbingUtamaId) {
            return $query->where('pembimbing_utama_id', $pembimbingUtamaId);
        })
            ->when($pembimbingPendampingId, function ($query) use ($pembimbingPendampingId) {
                return $query->where('pembimbing_pendamping_id', $pembimbingPendampingId);
            })
            ->when($validasi, function ($query) use ($validasi) {
                return $query->where('validasi', $validasi);
            })
            ->with(['pembimbingUtama', 'pembimbingPendamping', 'mahasiswa']) // Pastikan memuat relasi
            ->paginate(5);

        return view('pengajuan_pembimbing.index', compact('pengajuanPembimbing', 'dosen', 'userRole'));
    }


    // public function dropdownSearch(Request $request)
    // {
    //     $userRole = Auth::user()->role;

    //     // Ambil semua pembimbing
    //     // $pembimbingUtama = Dosen::all(); // Ambil semua dosen untuk pembimbing utama
    //     // $pembimbingPendamping = Dosen::all(); // Ambil semua dosen untuk pembimbing pendamping

    //     $dosen = Dosen::all();

    //     // Ambil nilai dari dropdown
    //     $pembimbingUtamaId = $request->input('pembimbing_utama_id');
    //     $pembimbingPendampingId = $request->input('pembimbing_pendamping_id');

    //     // Query untuk mencari pengajuan pembimbing
    //     $pengajuanPembimbing = PengajuanPembimbing::when($pembimbingUtamaId, function ($query) use ($pembimbingUtamaId) {
    //         return $query->where('pembimbing_utama_id', $pembimbingUtamaId);
    //     })
    //         ->when($pembimbingPendampingId, function ($query) use ($pembimbingPendampingId) {
    //             return $query->where('pembimbing_pendamping_id', $pembimbingPendampingId);
    //         })
    //         ->with(['pembimbingUtama', 'pembimbingPendamping', 'mahasiswa']) // Pastikan memuat relasi
    //         ->paginate(5);

    //     return view('pengajuan_pembimbing.index', compact('pengajuanPembimbing', 'dosen', 'userRole'));
    // }

    public function destroy(string $id)
    {
        $pengajuanPembimbing = PengajuanPembimbing::findOrFail($id);
        $pengajuanPembimbing->delete();
        return redirect()->route('pengajuan_pembimbing.index')->with('success', 'Pengajuan Pembimbing berhasil dihapus');
    }
}
