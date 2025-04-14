<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use App\Models\JadwalBimbingan;
use App\Models\LogbookBimbingan;
use App\Models\Mahasiswa;
use App\Models\PendaftaranBimbingan;
use App\Models\PengajuanPembimbing;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class LogbookBimbinganController extends Controller
{

    public function index()
    {
        $userRole = Auth::user()->role;
        $mahasiswa = Auth::user();
        $pengajuan = PengajuanPembimbing::where('mahasiswa_id', $mahasiswa->id)->first();

        return view('logbook_bimbingan.index', compact('pengajuan', 'mahasiswa', 'userRole'));
    }

    public function show($dosenId, $mahasiswaId)
    {
        $userRole = Auth::user()->role;

        // Ambil mahasiswa berdasarkan ID
        $mahasiswa = Mahasiswa::find($mahasiswaId);

        // Jika yang login adalah mahasiswa, ambil mahasiswaId dari pengguna yang login
        if ($userRole == 'Mahasiswa') {
            $mahasiswaId = Auth::user()->mahasiswa->id;
        }

        // Ambil pengajuan pembimbing berdasarkan mahasiswa
        $pengajuan = PengajuanPembimbing::where('mahasiswa_id', $mahasiswaId)->first();

        // Ambil jadwal yang sudah digunakan di tabel logbook_bimbingan
        $usedJadwalIds = LogbookBimbingan::whereHas('pendaftaranBimbingan', function ($query) use ($mahasiswaId) {
            $query->where('mahasiswa_id', $mahasiswaId);
        })->pluck('pendaftaran_bimbingan_id');

        $availablePendaftaranBimbingan = PendaftaranBimbingan::where('mahasiswa_id', $mahasiswaId)
            ->whereHas('jadwalBimbingan', function ($query) use ($dosenId) {
                $query->where('dosen_id', $dosenId);
            })
            ->whereNotIn('id', function ($query) {
                $query->select('pendaftaran_bimbingan_id')
                    ->from('logbook_bimbingan');
            })
            ->get();

        // Ambil data logbook sesuai dengan role pengguna
        $logbooks = LogbookBimbingan::whereHas('pendaftaranBimbingan', function ($query) use ($mahasiswaId, $dosenId) {
            $query->where('mahasiswa_id', $mahasiswaId)
                ->whereHas('jadwalBimbingan', function ($query) use ($dosenId) {
                    $query->where('dosen_id', $dosenId);
                });
        })->get();

        // Ambil data dosen berdasarkan dosenId
        $dosen = Dosen::find($dosenId);

        return view('logbook_bimbingan.show', compact(
            'pengajuan',
            'mahasiswa',
            'availablePendaftaranBimbingan',
            'userRole',
            'logbooks',
            'dosen'
        ));
    }


    public function store(Request $request)
    {
        $request->validate([
            'pendaftaran_bimbingan_id' => 'required|exists:pendaftaran_bimbingan,id', // Validasi yang benar
            'permasalahan' => 'required|string',
            'file_bimbingan' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
        ]);

        // Ambil pendaftaran_bimbingan
        $pendaftaran = PendaftaranBimbingan::find($request->pendaftaran_bimbingan_id);

        // Pastikan pendaftaran ditemukan
        if (!$pendaftaran) {
            return redirect()->back()->withErrors(['error' => 'Pendaftaran bimbingan tidak ditemukan.']);
        }

        // Ambil mahasiswa_id dari pendaftaran_bimbingan
        $mahasiswa_id = $pendaftaran->mahasiswa_id; // Ambil mahasiswa_id yang berelasi dengan pendaftaran_bimbingan

        $path = null;
        if ($request->hasFile('file_bimbingan')) {
            $path = $request->file('file_bimbingan')->store('logbooks', 'public');
        }

        // Simpan logbook
        LogbookBimbingan::create([
            'mahasiswa_id' => $mahasiswa_id, // Gunakan mahasiswa_id yang diambil dari pendaftaran_bimbingan
            'pendaftaran_bimbingan_id' => $request->pendaftaran_bimbingan_id,
            'permasalahan' => $request->permasalahan,
            'file_bimbingan' => $path,
        ]);

        return redirect()->back()->with('success', 'Logbook berhasil ditambahkan.');
    }
}
