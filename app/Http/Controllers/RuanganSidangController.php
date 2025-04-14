<?php

namespace App\Http\Controllers;

use App\Models\ProgramStudi;
use App\Models\RuanganSidang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class RuanganSidangController extends Controller
{
    public function index()
    {
        $userRole = Auth::user()->role;
        $ruanganSidang = RuanganSidang::with('programStudi')->paginate(5);
        $programStudi = ProgramStudi::all();

        return view('ruangan_sidang.index', compact('ruanganSidang', 'programStudi', 'userRole'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_ruangan' => 'required|string|max:255',
            'prodi_id' => 'required|exists:program_studi,id',
        ]);
        RuanganSidang::create($request->all());

        return redirect()->route('ruangan_sidang.index')->with('success', 'Program Studi berhasil ditambahkan');
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'nama_ruangan' => 'required|string|max:255',
            'prodi_id' => 'required|exists:program_studi,id',
        ]);
        $ruanganSidang = RuanganSidang::findOrFail($id);
        $ruanganSidang->update($request->all());

        return redirect()->route('ruangan_sidang.index')->with('success', 'Program Studi berhasil diperbarui');
    }

    public function search(Request $request)
    {
        $programStudi = ProgramStudi::all();
        $search = $request->input('search');

        // Mengambil data pengguna berdasarkan pencarian nama ruangan atau program studi
        $ruanganSidang = RuanganSidang::when($search, function ($query) use ($search) {
            return $query->where(function ($query) use ($search) {
                $query->where('nama_ruangan', 'like', "%$search%")
                    ->orWhereHas('programStudi', function ($query) use ($search) {
                        $query->where('nama_prodi', 'like', "%$search%");
                    });
            });
        })->paginate(5);

        return view('ruangan_sidang.index', compact('ruanganSidang', 'programStudi'));
    }

    public function destroy(string $id)
    {
        $ruanganSidang = RuanganSidang::findOrFail($id);
        $ruanganSidang->delete();

        return redirect()->route('ruangan_sidang.index')->with('success', 'Program Studi berhasil dihapus');
    }
}
