<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Dosen;
use Illuminate\Http\Request;
use App\Models\JadwalBimbingan;
use App\Models\LogbookBimbingan;
use Symfony\Component\Clock\now;
use App\Models\PengajuanPembimbing;
use App\Http\Controllers\Controller;
use App\Models\PendaftaranBimbingan;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

\Carbon\Carbon::setLocale('id');


class JadwalBimbinganController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return redirect('/login')->with('message', 'Please log in to continue.');
        }

        $user = Auth::user();
        $today = Carbon::today();
        $dosen = Dosen::all();

        // Update status berdasarkan tanggal
        JadwalBimbingan::where('tanggal', '<', $today)
            ->where('status', '!=', 'Selesai')
            ->update(['status' => 'Selesai']);

        JadwalBimbingan::where('tanggal', '=', $today)
            ->where('status', '!=', 'Sedang Berlangsung')
            ->update(['status' => 'Sedang Berlangsung']);

        JadwalBimbingan::where('tanggal', '>', $today)
            ->where('status', '!=', 'Terjadwal')
            ->update(['status' => 'Terjadwal']);

        if ($user->role === 'Mahasiswa') {
            // Ambil data pengajuan pembimbing mahasiswa yang sedang login
            $pengajuanPembimbing = PengajuanPembimbing::where('mahasiswa_id', Auth::user()->mahasiswa->id)
                ->where('validasi', 'Acc')
                ->first();

            if ($pengajuanPembimbing) {
                $jadwalBimbingan = JadwalBimbingan::where(function ($query) use ($pengajuanPembimbing) {
                    $query->where('dosen_id', $pengajuanPembimbing->pembimbing_utama_id)
                        ->orWhere('dosen_id', $pengajuanPembimbing->pembimbing_pendamping_id);
                })
                    ->with('dosen')->orderBy('created_at', 'desc')->paginate(10);

                // Tambahkan properti sudahMendaftar untuk masing-masing jadwal
                $mahasiswaId = Auth::user()->mahasiswa->id;
                $jadwalBimbingan->getCollection()->transform(function ($jadwalBimbingan) use ($mahasiswaId) {
                    $sudahMendaftar = PendaftaranBimbingan::where('mahasiswa_id', $mahasiswaId)
                        ->where('jadwal_bimbingan_id', $jadwalBimbingan->id)
                        ->exists();
                    $jadwalBimbingan->sudahMendaftar = $sudahMendaftar;
                    return $jadwalBimbingan;
                });
            } else {
                // Jika mahasiswa belum punya pembimbing, kosongkan jadwal
                $jadwalBimbingan = collect([]);
            }
        } elseif ($user->role === 'Dosen') {
            // Ambil ID dosen dari user yang login
            $dosenId = Auth::user()->dosen->id;

            // Ambil semua jadwal bimbingan milik dosen tersebut
            $jadwalBimbingan = JadwalBimbingan::where('dosen_id', $dosenId)
                ->with('dosen')
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            // Cek apakah jadwal sudah dipakai di logbook
            $jadwalBimbingan->getCollection()->transform(function ($jadwal) {
                $jadwal->isUsedInLogbook = PendaftaranBimbingan::where('jadwal_bimbingan_id', $jadwal->id)
                    ->whereHas('logbooks')
                    ->exists();
                return $jadwal;
            });
        } else {
            abort(403);
        }
        $mahasiswa = Auth::user();
        $pengajuan = PengajuanPembimbing::where('mahasiswa_id', $mahasiswa->id)->first();


        return view('jadwal_bimbingan.index', compact('jadwalBimbingan', 'dosen', 'user', 'pengajuan'));
    }
    public function indexKaprodi()
    {
        if (!Auth::check()) {
            return redirect('/login')->with('message', 'Please log in to continue.');
        }

        $user = Auth::user();
        $today = Carbon::today();
        $dosen = Dosen::all();

        // Update status berdasarkan tanggal
        JadwalBimbingan::where('tanggal', '<', $today)
            ->where('status', '!=', 'Selesai')
            ->update(['status' => 'Selesai']);

        JadwalBimbingan::where('tanggal', '=', $today)
            ->where('status', '!=', 'Sedang Berlangsung')
            ->update(['status' => 'Sedang Berlangsung']);

        JadwalBimbingan::where('tanggal', '>', $today)
            ->where('status', '!=', 'Terjadwal')
            ->update(['status' => 'Terjadwal']);

        if ($user->role === 'Dosen' && $user->dosen->jabatan === 'Koordinator Program Studi' || $user->role === 'Dosen' && $user->dosen->jabatan === 'Super Admin') {
            // Koordinator Program Studi dan Admin bisa melihat semua jadwal bimbingan
            $jadwalBimbingan = JadwalBimbingan::with('dosen')->orderBy('created_at', 'desc')->paginate(10);
        } else {
            abort(403);
        }

        return view('jadwal_bimbingan.index_kaprodi', compact('jadwalBimbingan', 'dosen', 'user'));
    }
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'tanggal' => 'required|date|after_or_equal:today',
            'waktu' => 'required',
            'kuota' => 'required|integer|min:1',
        ]);

        $dosenId = Auth::user()->dosen->id;

        $today = Carbon::today();
        $tanggalBimbingan = Carbon::parse($request->tanggal);

        // Tentukan status berdasarkan tanggal bimbingan
        if ($tanggalBimbingan->gt($today)) {
            $status = 'Terjadwal';
        } elseif ($tanggalBimbingan->eq($today)) {
            $status = 'Sedang Berlangsung';
        } else {
            $status = 'Selesai';
        }

        // Simpan data ke database
        JadwalBimbingan::create([
            'dosen_id' => $dosenId,
            'tanggal' => $request->tanggal,
            'waktu' => $request->waktu,
            'kuota' => $request->kuota,
            'status' => $status,
        ]);

        return redirect()->route('jadwal_bimbingan.index')->with('success', 'Jadwal Bimbingan berhasil ditambahkan.');
    }

    public function daftarBimbingan(Request $request, $id)
    {
        $mahasiswa = Auth::user()->mahasiswa;

        // Ambil jadwal yang dipilih
        $jadwalBimbingan = JadwalBimbingan::findOrFail($id);

        // Cek apakah mahasiswa sudah mendaftar bimbingan di jadwal yang sama
        $sudahMendaftar = PendaftaranBimbingan::where('mahasiswa_id', $mahasiswa->id)
            ->where('jadwal_bimbingan_id', $jadwalBimbingan->id)
            ->exists();

        if ($sudahMendaftar) {
            return redirect()->back()->with('error', 'Anda sudah mendaftar bimbingan pada jadwal ini.');
        }

        // Cek kuota sebelum mendaftar
        if ($jadwalBimbingan->kuota <= 0) {
            return redirect()->back()->with('error', 'Kuota sudah penuh!');
        }

        // Daftarkan mahasiswa ke pendaftaran bimbingan
        PendaftaranBimbingan::create([
            'mahasiswa_id' => $mahasiswa->id,
            'jadwal_bimbingan_id' => $jadwalBimbingan->id,
        ]);

        // Kurangi kuota bimbingan
        $jadwalBimbingan->decrement('kuota');

        return redirect()->route('jadwal_bimbingan.index')->with([
            'success' => 'Anda berhasil mendaftar bimbingan!',
            'dosen' => $jadwalBimbingan->dosen->nama_dosen,
            'tanggal' => $jadwalBimbingan->tanggal,
            'waktu' => $jadwalBimbingan->waktu
        ]);
    }

    public function dropdownSearch(Request $request)
    {
        $user = Auth::user();

        // Ambil semua data untuk dropdown
        $dosen = Dosen::all();
        $statusList = ['Selesai', 'Sedang Berlangsung', 'Terjadwal'];

        // Ambil nilai dari dropdown
        $namaDosen = $request->input('nama_dosen');
        $tanggal = $request->input('tanggal');
        $waktu = $request->input('waktu');
        $status = $request->input('status');

        // Query jadwal bimbingan dengan filter yang dipilih
        $jadwalBimbingan = JadwalBimbingan::when($namaDosen, function ($query) use ($namaDosen) {
            return $query->whereHas('dosen', function ($q) use ($namaDosen) {
                $q->where('nama_dosen', 'LIKE', "%$namaDosen%");
            });
        })
            ->when($tanggal, function ($query) use ($tanggal) {
                return $query->whereDate('tanggal', $tanggal);
            })
            ->when($waktu, function ($query) use ($waktu) {
                return $query->where('waktu', $waktu);
            })
            ->when($status, function ($query) use ($status) {
                return $query->where('status', $status);
            })
            ->with('dosen')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('jadwal_bimbingan.index_kaprodi', compact('jadwalBimbingan', 'dosen', 'statusList', 'user'));
    }
    public function destroy(string $id)
    {
        // Temukan user dan mahasiswa yang akan dihapus
        $jadwalBimbingan = JadwalBimbingan::findOrFail($id);
        $jadwalBimbingan->delete();

        return redirect()->route('jadwal_bimbingan.index')->with('success', 'Berhasil membatalkan jadwal bimbingan.');
    }
}
