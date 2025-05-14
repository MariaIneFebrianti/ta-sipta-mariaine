<?php

namespace App\Http\Controllers;

use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class TahunAjaranController extends Controller
{
    public function index()
    {
        $userRole = Auth::user()->role;
        $tahunAjaran = TahunAjaran::paginate(5);

        return view('tahun_ajaran.index', compact('tahunAjaran', 'userRole'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tahun_ajaran' => 'required|string|max:255'
        ]);
        TahunAjaran::create($request->all());

        return redirect()->route('tahun_ajaran.index')->with('success', 'Program Studi berhasil ditambahkan');
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'tahun_ajaran' => 'required|string|max:255'
        ]);
        $tahunAjaran = TahunAjaran::findOrFail($id);
        $tahunAjaran->update($request->all());

        return redirect()->route('tahun_ajaran.index')->with('success', 'Program Studi berhasil diperbarui');
    }

    public function search(Request $request)
    {
        $userRole = Auth::user()->role;
        $search = $request->input('search');
        $tahunAjaran = TahunAjaran::when($search, function ($query) use ($search) {
            return $query->where(function ($query) use ($search) {
                $query->where('tahun_ajaran', 'like', "%$search%");
            });
        })->paginate(5);

        return view('tahun_ajaran.index', compact('tahunAjaran', 'userRole'));
    }

    public function destroy(string $id)
    {
        $tahunAjaran = TahunAjaran::findOrFail($id);
        $tahunAjaran->delete();

        return redirect()->route('tahun_ajaran.index')->with('success', 'Program Studi berhasil dihapus');
    }
}
