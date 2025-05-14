<?php

namespace App\Http\Controllers;

use App\Models\ProgramStudi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class ProgramStudiController extends Controller
{
    public function index()
    {
        $userRole = Auth::user()->role;
        $programStudi = ProgramStudi::paginate(5);

        return view('program_studi.index', compact('programStudi', 'userRole'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_prodi' => 'required|string|max:255',
            'nama_prodi' => 'required|string|max:255'
        ]);
        ProgramStudi::create($request->all());

        return redirect()->route('program_studi.index')->with('success', 'Program Studi berhasil ditambahkan');
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'kode_prodi' => 'required|string|max:255',
            'nama_prodi' => 'required|string|max:255'
        ]);
        $programStudi = ProgramStudi::findOrFail($id);
        $programStudi->update($request->all());

        return redirect()->route('program_studi.index')->with('success', 'Program Studi berhasil diperbarui');
    }

    public function search(Request $request)
    {
        $userRole = Auth::user()->role;
        $search = $request->input('search');

        // Mengambil data pengguna berdasarkan pencarian kode prodi atau nama prodi
        $programStudi = ProgramStudi::when($search, function ($query) use ($search) {
            return $query->where(function ($query) use ($search) {
                $query->where('kode_prodi', 'like', "%$search%")
                    ->orWhere('nama_prodi', 'like', "%$search%");
            });
        })
            ->paginate(5);

        return view('program_studi.index', compact('programStudi', 'userRole'));
    }

    public function destroy(string $id)
    {
        $programStudi = ProgramStudi::findOrFail($id);
        $programStudi->delete();
        return redirect()->route('program_studi.index')->with('success', 'Program Studi berhasil dihapus');
    }
}
