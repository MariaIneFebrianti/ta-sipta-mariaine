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
                ->with(['pembimbingUtama', 'pembimbingPendamping', 'mahasiswa'])
                ->paginate(5);
        } else {
            // Jika role lain (Kooordinator Program Studi atau Super Admin), tampilkan semua pengajuan
            $pengajuanPembimbing = PengajuanPembimbing::with(['pembimbingUtama', 'pembimbingPendamping', 'mahasiswa'])
                ->paginate(5);
        }

        $dosen = Dosen::all();
        $mahasiswa = Mahasiswa::all();
        return view('pengajuan_pembimbing.index', compact('pengajuanPembimbing', 'dosen', 'mahasiswa', 'userRole'));
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

    public function update(Request $request, string $id)
    {
        $request->validate([
            'pembimbing_utama_id' => 'required|exists:dosen,id',
            'pembimbing_pendamping_id' => 'required|exists:dosen,id',
        ]);

        $pengajuanPembimbing = PengajuanPembimbing::findOrFail($id);
        $pengajuanPembimbing->update($request->all());
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

    public function destroy(string $id)
    {
        $pengajuanPembimbing = PengajuanPembimbing::findOrFail($id);
        $pengajuanPembimbing->delete();
        return redirect()->route('pengajuan_pembimbing.index')->with('success', 'Pengajuan Pembimbing berhasil dihapus');
    }
}
