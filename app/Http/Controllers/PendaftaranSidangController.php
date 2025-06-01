<?php

namespace App\Http\Controllers;

use App\Models\LogbookBimbingan;
use Illuminate\Http\Request;
use App\Models\PendaftaranSidang;
use Illuminate\Support\Facades\Auth;

class PendaftaranSidangController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return redirect('/login')->with('message', 'Please log in to continue.');
        }

        $user = Auth::user();
        if ($user->role === 'Mahasiswa') {
            $mahasiswa = $user->mahasiswa;
            $pendaftaran = PendaftaranSidang::where('mahasiswa_id', $mahasiswa->id)->get();
            $adaUtama = LogbookBimbingan::where('mahasiswa_id', $mahasiswa->id)
                ->where('rekomendasi_utama', 'Ya')
                ->exists();

            $adaPendamping = LogbookBimbingan::where('mahasiswa_id', $mahasiswa->id)
                ->where('rekomendasi_pendamping', 'Ya')
                ->exists();

            $rekomendasi = $adaUtama && $adaPendamping;
        } else {
            abort(403);
        }

        return view('pendaftaran_sidang.index', compact('pendaftaran', 'user', 'rekomendasi'));
    }
    public function indexKaprodi()
    {
        if (!Auth::check()) {
            return redirect('/login')->with('message', 'Please log in to continue.');
        }

        $user = Auth::user();
        if ($user->role === 'Dosen' && $user->dosen->jabatan === 'Koordinator Program Studi') {
            $pendaftaran = PendaftaranSidang::with('mahasiswa')->get();
        } else {
            abort(403);
        }

        return view('pendaftaran_sidang.index_kaprodi', compact('pendaftaran'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal_pendaftaran' => 'required|date',
            'file_tugas_akhir' => 'required|file',
            'file_bebas_pinjaman_administrasi' => 'required|file',
            'file_slip_pembayaran_semester_akhir' => 'required|file',
            'file_transkip_sementara' => 'required|file',
            'file_bukti_pembayaran_sidang_ta' => 'required|file',
        ]);

        $mahasiswaId = Auth::user()->mahasiswa->id;

        // $data = [
        //     'mahasiswa_id' => $mahasiswaId,
        //     'tanggal_pendaftaran' => $request->tanggal_pendaftaran,
        //     'file_tugas_akhir' => $request->file('file_tugas_akhir')->store('sidang'),
        //     'file_bebas_pinjaman_administrasi' => $request->file('file_bebas_pinjaman_administrasi')->store('sidang'),
        //     'file_slip_pembayaran_semester_akhir' => $request->file('file_slip_pembayaran_semester_akhir')->store('sidang'),
        //     'file_transkip_sementara' => $request->file('file_transkip_sementara')->store('sidang'),
        //     'file_bukti_pembayaran_sidang_ta' => $request->file('file_bukti_pembayaran_sidang_ta')->store('sidang'),
        // ];

        // PendaftaranSidang::create($data);

        // Proses file upload
        $file_tugas_akhir = time() . '.' . $request->file('file_tugas_akhir')->getClientOriginalExtension();
        $request->file('file_tugas_akhir')->storeAs('sidang', $file_tugas_akhir, 'public');

        $file_bebas_pinjaman = time() + 1 . '.' . $request->file('file_bebas_pinjaman_administrasi')->getClientOriginalExtension();
        $request->file('file_bebas_pinjaman_administrasi')->storeAs('sidang', $file_bebas_pinjaman, 'public');

        $file_slip_pembayaran = time() + 2 . '.' . $request->file('file_slip_pembayaran_semester_akhir')->getClientOriginalExtension();
        $request->file('file_slip_pembayaran_semester_akhir')->storeAs('sidang', $file_slip_pembayaran, 'public');

        $file_transkip = time() + 3 . '.' . $request->file('file_transkip_sementara')->getClientOriginalExtension();
        $request->file('file_transkip_sementara')->storeAs('sidang', $file_transkip, 'public');

        $file_bukti_sidang = time() + 4 . '.' . $request->file('file_bukti_pembayaran_sidang_ta')->getClientOriginalExtension();
        $request->file('file_bukti_pembayaran_sidang_ta')->storeAs('sidang', $file_bukti_sidang, 'public');

        // Simpan ke database
        PendaftaranSidang::create([
            'mahasiswa_id' => $mahasiswaId,
            'tanggal_pendaftaran' => $request->tanggal_pendaftaran,
            'file_tugas_akhir' => $file_tugas_akhir,
            'file_bebas_pinjaman_administrasi' => $file_bebas_pinjaman,
            'file_slip_pembayaran_semester_akhir' => $file_slip_pembayaran,
            'file_transkip_sementara' => $file_transkip,
            'file_bukti_pembayaran_sidang_ta' => $file_bukti_sidang,
        ]);


        return redirect()->back()->with('success', 'Pendaftaran sidang berhasil disimpan.');
    }

    public function showFile($id, $fileField)
    {
        $pendaftaran = PendaftaranSidang::findOrFail($id);

        // Daftar kolom file yang valid, supaya user tidak bisa akses file sembarangan
        $allowedFields = [
            'file_tugas_akhir',
            'file_bebas_pinjaman_administrasi',
            'file_slip_pembayaran_semester_akhir',
            'file_transkip_sementara',
            'file_bukti_pembayaran_sidang_ta',
        ];

        if (!in_array($fileField, $allowedFields)) {
            abort(404, 'File tidak ditemukan');
        }

        $filename = $pendaftaran->$fileField;

        if (!$filename) {
            abort(404, 'File tidak ditemukan');
        }

        $filePath = storage_path('app/public/sidang/' . $filename);

        if (!file_exists($filePath)) {
            abort(404, 'File tidak ditemukan');
        }

        return response()->file($filePath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline',
            'Cache-Control' => 'public, max-age=3600'
        ]);
    }
}
