<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\JadwalBimbingan;
use App\Models\LogbookBimbingan;
use App\Models\PendaftaranBimbingan;
use App\Models\PengajuanPembimbing;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class JadwalBimbinganController extends Controller
{
    public function index()
    {
        $userRole = Auth::user()->role;
        $today = Carbon::today();

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

        if ($userRole === 'Mahasiswa') {
            // Ambil data pengajuan pembimbing mahasiswa yang sedang login
            $pengajuanPembimbing = PengajuanPembimbing::where('mahasiswa_id', Auth::user()->mahasiswa->id)->first();

            if ($pengajuanPembimbing) {
                $jadwalBimbingan = JadwalBimbingan::where(function ($query) use ($pengajuanPembimbing) {
                    $query->where('dosen_id', $pengajuanPembimbing->pembimbing_utama_id)
                        ->orWhere('dosen_id', $pengajuanPembimbing->pembimbing_pendamping_id);
                })->with('dosen')->paginate(10);

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
        } elseif ($userRole === 'Dosen') {
            // Hanya lihat jadwal bimbingan milik dosen yang login
            $dosenId = Auth::user()->dosen->id;
            $jadwalBimbingan = JadwalBimbingan::where('dosen_id', $dosenId)
                ->with('dosen')
                ->paginate(10);
        } else {
            // Koordinator Program Studi bisa melihat semua jadwal bimbingan
            $jadwalBimbingan = JadwalBimbingan::with('dosen')->paginate(10);
        }

        return view('jadwal_bimbingan.index', compact('jadwalBimbingan', 'userRole'));
    }


    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'tanggal' => 'required|date',
            'waktu' => 'required',
            'kuota' => 'required|integer|min:1',
        ]);

        $dosenId = Auth::user()->dosen->id;

        $today = Carbon::today();
        $tanggalBimbingan = Carbon::parse($request->tanggal);

        // Tentukan status berdasarkan tanggal bimbingan
        if ($tanggalBimbingan->gt($today)) {
            $status = 'Terjadwal'; // Jika tanggal bimbingan di masa depan
        } elseif ($tanggalBimbingan->eq($today)) {
            $status = 'Sedang Berlangsung'; // Jika jadwal untuk hari ini
        } else {
            $status = 'Selesai'; // Ini jarang terjadi, kecuali user input tanggal yang sudah lewat
        }

        // Simpan data ke database
        JadwalBimbingan::create([
            'dosen_id' => $dosenId,
            'tanggal' => $request->tanggal,
            'waktu' => $request->waktu,
            'kuota' => $request->kuota,
            'status' => $status, // Status ditentukan otomatis
        ]);

        return redirect()->route('jadwal_bimbingan.index')->with('success', 'Jadwal Bimbingan berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'dosen_id' => 'required|exists:dosen,id',
            'tanggal' => 'required|date',
            'waktu' => 'required',
            'kuota' => 'required|integer|max:2',
        ]);

        $jadwalBimbingan = JadwalBimbingan::findOrFail($id);
        $jadwalBimbingan->update($request->all());

        return redirect()->route('jadwal_bimbingan.index')->with('success', 'Jadwal Bimbingan berhasil diperbarui.');
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
}
