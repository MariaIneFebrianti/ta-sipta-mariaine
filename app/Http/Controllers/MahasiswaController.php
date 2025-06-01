<?php

namespace App\Http\Controllers;

use App\Models\Proposal;
use App\Models\User;
use App\Models\Mahasiswa;
use App\Models\TahunAjaran;
use App\Models\ProgramStudi;
use Illuminate\Http\Request;
use App\Models\RuanganSidang;
use App\Imports\MahasiswaImport;
use App\Models\Dosen;
use App\Models\PengajuanPembimbing;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;

\Carbon\Carbon::setLocale('id');



class MahasiswaController extends Controller
{
    public function index(Request $request)
    {
        if (!Auth::check()) {
            return redirect('/login')->with('message', 'Please log in to continue.');
        }

        $user = Auth::user();
        if ($user->role === 'Dosen' && $user->dosen->jabatan === 'Koordinator Program Studi') {
            $mahasiswa = Mahasiswa::with('user')->orderBy('mahasiswa.nama_mahasiswa', 'asc')->paginate(5);

            $programStudi = ProgramStudi::all();
            $tahunAjaran = TahunAjaran::all();
        } else {
            abort(403);
        }

        return view('mahasiswa.index', compact('programStudi', 'tahunAjaran', 'mahasiswa', 'user'));
    }

    // public function index(Request $request)
    // {
    //     if (!Auth::check()) {
    //         return redirect('/login')->with('message', 'Please log in to continue.');
    //     }

    //     $user = Auth::user();

    //     if ($user->role === 'Dosen' && $user->dosen->jabatan === 'Koordinator Program Studi') {
    //         $programStudiId = $user->dosen->program_studi_id;

    //         // Ambil mahasiswa yang program studinya sama dengan kaprodi
    //         $mahasiswa = Mahasiswa::with('user', 'proposal')
    //             ->where('program_studi_id', $programStudiId)
    //             ->orderBy('nama_mahasiswa', 'asc')
    //             ->paginate(5);
    //     } elseif ($user->role === 'Dosen' && $user->dosen->jabatan === 'Super Admin') {
    //         // Kalau bukan kaprodi, ambil semua mahasiswa
    //         $mahasiswa = Mahasiswa::with('user', 'proposal')
    //             ->orderBy('nama_mahasiswa', 'asc')
    //             ->paginate(5);
    //     } else {
    //         abort(403);
    //     }

    //     $programStudi = ProgramStudi::all();
    //     $tahunAjaran = TahunAjaran::all();

    //     return view('mahasiswa.index', compact('programStudi', 'tahunAjaran', 'mahasiswa'));
    // }


    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'nim' => 'required|integer|unique:mahasiswa,nim',
            'tempat_lahir' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|string|max:10',
            'program_studi_id' => 'required|string|max:255',
            'tahun_ajaran_id' => 'required|string|max:255',

        ]);

        // Tentukan nilai default untuk password dan role
        $password = Hash::make('11111111');
        $email_verified_at = now();
        $role = 'Mahasiswa';

        // Buat user baru
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'email_verified_at' => $email_verified_at,
            'password' => $password,
            'role' => $role,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Simpan data mahasiswa baru dengan user_id dari user yang baru dibuat
        $mahasiswa = Mahasiswa::create([
            'user_id' => $user->id,
            'nim' => $request->nim,
            'nama_mahasiswa' => $request->name,
            'tempat_lahir' => $request->tempat_lahir,
            'tanggal_lahir' => $request->tanggal_lahir,
            'jenis_kelamin' => $request->jenis_kelamin,
            'program_studi_id' => $request->program_studi_id,
            'tahun_ajaran_id' => $request->tahun_ajaran_id,
        ]);

        if (!$mahasiswa) {
            $user->delete();
            return redirect()->back()->with('error', 'Gagal menambahkan data mahasiswa.');
        }

        return redirect()->route('mahasiswa.index')->with('success', 'Data mahasiswa berhasil ditambahkan.');
    }
    public function update(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'name' => 'required|string|max:255',
            'nim' => 'required|integer|unique:mahasiswa,nim,' . $id,
            'tempat_lahir' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|string|max:10',
            'program_studi_id' => 'required|exists:program_studi,id',
            'tahun_ajaran_id' => 'required|exists:tahun_ajaran,id',
        ]);

        // Temukan mahasiswa dan user yang akan diupdate
        $mahasiswa = Mahasiswa::findOrFail($id);
        $user = User::findOrFail($mahasiswa->user_id); // Dapatkan user_id dari mahasiswa

        // Update data user
        $user->update([
            'name' => $request->name,
        ]);

        // Update data mahasiswa
        $mahasiswa->update([
            'nim' => $request->nim,
            'nama_mahasiswa' => $request->name,
            'tempat_lahir' => $request->tempat_lahir,
            'tanggal_lahir' => $request->tanggal_lahir,
            'jenis_kelamin' => $request->jenis_kelamin,
            'program_studi_id' => $request->program_studi_id,
            'tahun_ajaran_id' => $request->tahun_ajaran_id,
        ]);

        return redirect()->route('mahasiswa.index')->with('success', 'Data mahasiswa berhasil diupdate.');
    }

    public function search(Request $request)
    {
        $programStudi = ProgramStudi::all();
        $tahunAjaran = TahunAjaran::all();
        $search = $request->input('search'); // Ambil input pencarian

        // Mengambil data pengguna berdasarkan pencarian nama, nim, tempat lahir, jenis kelamin, atau program studi
        $mahasiswa = Mahasiswa::when($search, function ($query) use ($search) {
            return $query->where(function ($query) use ($search) {
                $query->where('nama_mahasiswa', 'like', "%$search%")
                    ->orWhere('nim', 'like', "%$search%");
                // ->orWhere('tempat_lahir', 'like', "%$search%");
            });
        })->paginate(5);

        return view('mahasiswa.index', compact('mahasiswa', 'programStudi', 'tahunAjaran'));
    }

    public function dropdownSearch(Request $request)
    {
        $programStudi = ProgramStudi::all();
        $tahunAjaran = TahunAjaran::all();

        // Ambil nilai dari dropdown
        $programStudiId = $request->input('program_studi');
        $tahunAjaranId = $request->input('tahun_ajaran');
        $jenisKelamin = $request->input('jenis_kelamin');

        // Query untuk mencari mahasiswa dengan kondisi yang dipilih
        $mahasiswa = Mahasiswa::when($programStudiId, function ($query) use ($programStudiId) {
            return $query->where('program_studi_id', $programStudiId);
        })
            ->when($tahunAjaranId, function ($query) use ($tahunAjaranId) {
                return $query->where('tahun_ajaran_id', $tahunAjaranId);
            })
            ->when($jenisKelamin, function ($query) use ($jenisKelamin) {
                return $query->where('jenis_kelamin', $jenisKelamin);
            })
            ->orderBy('nama_mahasiswa', 'asc')
            ->paginate(5);

        return view('mahasiswa.index', compact('mahasiswa', 'programStudi', 'tahunAjaran'));
    }


    public function destroy(string $id)
    {
        // Temukan user dan mahasiswa yang akan dihapus
        $mahasiswa = Mahasiswa::findOrFail($id);
        $user = User::findOrFail($mahasiswa->user_id);
        $mahasiswa->delete();
        $user->delete();

        return redirect()->route('mahasiswa.index')->with('success', 'Data mahasiswa berhasil dihapus.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,xlsx,xls|max:2048',
        ]);

        try {
            Excel::import(new MahasiswaImport, $request->file('file'));
            return redirect()->route('mahasiswa.index')->with('success', 'Data mahasiswa berhasil diimpor.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengimpor data mahasiswa: ' . $e->getMessage());
        }
    }
}
