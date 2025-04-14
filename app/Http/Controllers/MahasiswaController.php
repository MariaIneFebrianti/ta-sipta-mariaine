<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use App\Models\ProgramStudi;
use App\Models\RuanganSidang;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;


class MahasiswaController extends Controller
{
    public function index()
    {
        $userRole = Auth::user()->role;
        // Mengambil semua data mahasiswa dengan relasi user
        $mahasiswa = Mahasiswa::with('user')->paginate(5);
        $programStudi = ProgramStudi::all();
        $tahunAjaran = TahunAjaran::all();

        return view('mahasiswa.index', compact('mahasiswa', 'programStudi', 'tahunAjaran', 'userRole'));
    }
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
            'prodi_id' => 'required|string|max:255',
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
            'prodi_id' => $request->prodi_id,
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
            'prodi_id' => 'required|exists:program_studi,id',
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
            'prodi_id' => $request->prodi_id,
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
                    ->orWhere('nim', 'like', "%$search%")
                    ->orWhere('tempat_lahir', 'like', "%$search%")
                    ->orWhere('jenis_kelamin', 'like', "%$search%")
                    ->orWhereHas('programStudi', function ($query) use ($search) {
                        $query->where('nama_prodi', 'like', "%$search%");
                    })
                    ->orWhereHas('tahunAjaran', function ($query) use ($search) {
                        $query->where('tahun_ajaran', 'like', "%$search%");
                    });
            });
        })
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
}
