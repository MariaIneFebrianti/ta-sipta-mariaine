<?php

namespace App\Http\Controllers;

use App\Imports\DosenImport;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\ProgramStudi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;



class DosenController extends Controller
{
    public function index()
    {
        $userRole = Auth::user()->role;
        // Mengambil semua data dosen dengan relasi user
        $dosen = Dosen::with('user')->paginate(5);
        // Mengambil semua data program studi
        $programStudi = ProgramStudi::all();

        return view('dosen.index', compact('dosen', 'programStudi', 'userRole'));
    }
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'nip' => 'required|integer|unique:dosen,nip',
            'tempat_lahir' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|string|max:10',
        ]);

        // Tentukan nilai default untuk password dan role
        $password = Hash::make('11111111');
        $email_verified_at = now();
        $role = 'Dosen';

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
        $dosen = Dosen::create([
            'user_id' => $user->id,
            'nip' => $request->nip,
            'nama_dosen' => $request->name,
            'tempat_lahir' => $request->tempat_lahir,
            'tanggal_lahir' => $request->tanggal_lahir,
            'jenis_kelamin' => $request->jenis_kelamin,
            'prodi_id' => $request->prodi_id,
        ]);

        if (!$dosen) {
            $user->delete();
            return redirect()->back()->with('error', 'Gagal menambahkan data dosen.');
        }

        return redirect()->route('dosen.index')->with('success', 'Data dosen berhasil ditambahkan.');
    }
    public function update(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'name' => 'required|string|max:255',
            'nip' => 'required|integer|unique:dosen,nip,' . $id,
            'tempat_lahir' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|string|max:10',
            'prodi_id' => 'required|exists:program_studi,id',
        ]);

        // Temukan dosen dan user yang akan diupdate
        $dosen = Dosen::findOrFail($id);
        $user = User::findOrFail($dosen->user_id); // Dapatkan user_id dari mahasiswa

        // Update data user
        $user->update([
            'name' => $request->name,
        ]);

        // Update data mahasiswa
        $dosen->update([
            'nip' => $request->nip,
            'nama_dosen' => $request->name,
            'tempat_lahir' => $request->tempat_lahir,
            'tanggal_lahir' => $request->tanggal_lahir,
            'jenis_kelamin' => $request->jenis_kelamin,
            'prodi_id' => $request->prodi_id,
        ]);

        return redirect()->route('dosen.index')->with('success', 'Data dosen berhasil diupdate.');
    }

    public function search(Request $request)
    {
        // Mendapatkan role pengguna yang sedang login
        $userRole = Auth::user()->role;
        $programStudi = ProgramStudi::all();
        $search = $request->input('search');

        // Mengambil data dosen berdasarkan pencarian
        $dosen = Dosen::when($search, function ($query) use ($search) {
            return $query->where(function ($query) use ($search) {
                $query->where('nama_dosen', 'like', "%$search%")
                    ->orWhere('nip', 'like', "%$search%")
                    ->orWhere('tempat_lahir', 'like', "%$search%")
                    ->orWhere('jenis_kelamin', 'like', "%$search%");
            });
        })->paginate(5);

        // Mengirimkan data ke view
        return view('dosen.index', compact('dosen', 'programStudi', 'userRole'));
    }


    public function destroy(string $id)
    {
        // Temukan user dan mahasiswa yang akan dihapus
        $dosen = Dosen::findOrFail($id);
        $user = User::findOrFail($dosen->user_id);
        $dosen->delete();
        $user->delete();

        return redirect()->route('dosen.index')->with('success', 'Data dosen berhasil dihapus.');
    }

    public function mahasiswaBimbingan()
    {
        $dosenId = Auth::id();

        // Ambil data mahasiswa yang mengajukan dosen ini sebagai pembimbing utama atau pendamping
        $mahasiswa = Mahasiswa::whereHas('pengajuanPembimbing', function ($query) use ($dosenId) {
            $query->where('pembimbing_utama_id', $dosenId)
                ->orWhere('pembimbing_pendamping_id', $dosenId);
        })->get();

        return view('dosen.mahasiswa_pengajuan_bimbingan', compact('mahasiswa'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,xlsx,xls|max:2048',
        ]);

        try {
            Excel::import(new DosenImport, $request->file('file'));
            return redirect()->route('dosen.index')->with('success', 'Data dosen berhasil diimpor.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengimpor data dosen: ' . $e->getMessage());
        }
    }
}
