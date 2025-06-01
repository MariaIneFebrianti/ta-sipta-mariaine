<?php

namespace App\Http\Controllers;

use App\Models\Proposal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class ProposalController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return redirect('/login')->with('message', 'Please log in to continue.');
        }
        $user = Auth::user();
        $dosen = $user->dosen;


        if ($user->role === 'Mahasiswa') {
            $mahasiswa = Auth::user()->mahasiswa;
            if ($mahasiswa) {
                $proposal = Proposal::where('mahasiswa_id', $mahasiswa->id)->get();
            } else {
                $proposal = null;
            }
        } elseif ($dosen->jabatan === 'Koordinator Program Studi') {
            $proposal = Proposal::with('mahasiswa')
                ->whereHas('mahasiswa', function ($query) use ($dosen) {
                    $query->where('program_studi_id', $dosen->program_studi_id);
                })
                ->paginate(5);
        } else {
            $proposal = Proposal::with('mahasiswa')->paginate(10);
        }

        return view('proposal.index', compact('proposal', 'user'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul_proposal' => 'required|string|max:255',
            'file_proposal' => 'required|mimes:pdf',
        ]);

        $mahasiswaId = Auth::user()->mahasiswa->id;


        // $path = $request->file('file_proposal')->store('proposals', 'public');
        $path = null;
        if ($request->hasFile('file_proposal')) {
            $file_proposal = $request->file('file_proposal');

            $filename = time() . '.' . $file_proposal->getClientOriginalExtension();

            $file_proposal->storeAs('proposals', $filename, 'public');

            $path = 'proposals/' . $filename;
        }

        Proposal::create([
            'mahasiswa_id' => $mahasiswaId,
            'judul_proposal' => $request->judul_proposal,
            'file_proposal' => $path,
        ]);

        return redirect()->back()->with('success', 'Proposal berhasil diunggah.');
    }

    public function showFile($id)
    {
        $proposal = Proposal::findOrFail($id);
        $filePath = storage_path('app/public/' . $proposal->file_proposal);

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
