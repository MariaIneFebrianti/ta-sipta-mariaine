<?php

namespace App\Http\Controllers;

use App\Models\ProgramStudi;
use App\Models\RuanganSidang;
use Illuminate\Http\Request;

class RuanganSidangController extends Controller
{
    public function index()
    {
        $ruanganSidang = RuanganSidang::with('programStudi')->get();
        $programStudi = ProgramStudi::all();
        return view('ruangan_sidang.index', compact('ruanganSidang', 'programStudi'));
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

    public function show(string $id)
    {
        //
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

    public function destroy(string $id)
    {
        $ruanganSidang = RuanganSidang::findOrFail($id);
        $ruanganSidang->delete();
        return redirect()->route('ruangan_sidang.index')->with('success', 'Program Studi berhasil dihapus');
    }
}
