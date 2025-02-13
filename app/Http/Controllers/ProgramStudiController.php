<?php

namespace App\Http\Controllers;

use App\Models\ProgramStudi;
use Illuminate\Http\Request;

class ProgramStudiController extends Controller
{
    public function index()
    {
        $programStudi = ProgramStudi::all();
        return view('program_studi.index', compact('programStudi'));
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

    public function show(string $id)
    {
        //
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

    public function destroy(string $id)
    {
        $programStudi = ProgramStudi::findOrFail($id);
        $programStudi->delete();
        return redirect()->route('program_studi.index')->with('success', 'Program Studi berhasil dihapus');
    }
}
