<?php

namespace App\Http\Controllers;

use App\Models\Proposal;
use Illuminate\Http\Request;
use App\Models\PenilaianSempro;
use Illuminate\Support\Facades\Auth;
use App\Models\JadwalSeminarProposal;

\Carbon\Carbon::setLocale('id');


class ProposalController extends Controller
{
    // public function index()
    // {
    //     if (!Auth::check()) {
    //         return redirect('/login')->with('message', 'Please log in to continue.');
    //     }
    //     $user = Auth::user();
    //     $dosen = $user->dosen;
    //     $penilaianSempro = null;


    //     if ($user->role === 'Mahasiswa') {
    //         $mahasiswa = Auth::user()->mahasiswa;
    //         if ($mahasiswa) {
    //             $proposal = Proposal::where('mahasiswa_id', $mahasiswa->id)->get();
    //             $penilaianSempro = PenilaianSempro::where('mahasiswa_id', $mahasiswa->id)->get();
    //         } else {
    //             $proposal = null;
    //         }
    //     } elseif ($dosen->jabatan === 'Koordinator Program Studi') {
    //         $proposal = Proposal::with('mahasiswa')
    //             ->whereHas('mahasiswa', function ($query) use ($dosen) {
    //                 $query->where('program_studi_id', $dosen->program_studi_id);
    //             })
    //             ->paginate(10);
    //         $penilaianSempro = PenilaianSempro::whereHas('mahasiswa', function ($query) use ($dosen) {
    //             $query->where('program_studi_id', $dosen->program_studi_id);
    //         })->get();
    //     } elseif ($dosen->jabatan === 'Super Admin') {
    //         $proposal = Proposal::with('mahasiswa')->paginate(10);
    //         $penilaianSempro = PenilaianSempro::with('mahasiswa')->get();
    //     } else {
    //         abort(403);
    //     }

    //     return view('proposal.index', [
    //         'proposal' => $proposal,
    //         'user' => $user,
    //         'penilaianSempro' => $penilaianSempro ?? collect()
    //     ]);
    //     // return view('proposal.index', compact('proposal', 'user', 'penilaianSempro'));
    // }

    public function index()
    {
        if (!Auth::check()) {
            return redirect('/login')->with('message', 'Please log in to continue.');
        }

        $user = Auth::user();
        $dosen = $user->dosen;
        $penilaianSempro = null;
        $punyaCatatanRevisi = false; // default

        if ($user->role === 'Mahasiswa') {
            $mahasiswa = $user->mahasiswa;

            if ($mahasiswa) {
                $proposal = Proposal::where('mahasiswa_id', $mahasiswa->id)->get();
                // Cek apakah mahasiswa punya catatan revisi
                $punyaCatatanRevisi = PenilaianSempro::where('mahasiswa_id', $mahasiswa->id)
                    ->whereNotNull('catatan_revisi')
                    ->exists();
            } else {
                $proposal = null;
            }
        } elseif ($dosen && $dosen->jabatan === 'Koordinator Program Studi') {
            $proposal = Proposal::with('mahasiswa')
                ->whereHas('mahasiswa', function ($query) use ($dosen) {
                    $query->where('program_studi_id', $dosen->program_studi_id);
                })
                ->paginate(10);
        } elseif ($dosen && $dosen->jabatan === 'Super Admin') {
            $proposal = Proposal::with('mahasiswa')->paginate(10);
        } else {
            abort(403);
        }

        return view('proposal.index', [
            'proposal' => $proposal,
            'user' => $user,
            'punyaCatatanRevisi' => $punyaCatatanRevisi,
        ]);
    }


    public function store(Request $request)
    {
        $request->validate([
            'judul_proposal' => 'required|string|max:100|unique:proposal,judul_proposal',
            'file_proposal' => 'required|mimes:pdf|max:2048',
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

    public function showFileProposal($id)
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

    public function showFileProposalRevisi($id)
    {
        $proposal = Proposal::findOrFail($id);
        $filePath = storage_path('app/public/' . $proposal->revisi_file_proposal);

        if (!file_exists($filePath)) {
            abort(404, 'File tidak ditemukan');
        }

        return response()->file($filePath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline',
            'Cache-Control' => 'public, max-age=3600'
        ]);
    }

    public function simpanCatatanRevisi(Request $request, $id)
    {
        $proposal = Proposal::findOrFail($id);
        $dosenId = Auth::user()->dosen->id;

        $jadwal = JadwalSeminarProposal::where('mahasiswa_id', $proposal->mahasiswa_id)->first();

        if ($jadwal->penguji_utama_id == $dosenId) {
            $proposal->catatan_utama = $request->catatan;
        } elseif ($jadwal->penguji_pendamping_id == $dosenId) {
            $proposal->catatan_pendamping = $request->catatan;
        }

        $proposal->save();

        return redirect()->back()->with('success', 'Catatan revisi berhasil disimpan.');
    }

    public function updateRevisi(Request $request, $id)
    {
        $request->validate([
            'revisi_judul_proposal' => 'nullable|string|max:100|unique:proposal,revisi_judul_proposal',
            'revisi_file_proposal' => 'nullable|file|mimes:pdf|max:2048',
        ]);

        $proposal = Proposal::findOrFail($id);

        if ($request->filled('revisi_judul_proposal')) {
            $proposal->revisi_judul_proposal = $request->revisi_judul_proposal;
        }

        if ($request->hasFile('revisi_file_proposal')) {
            $file_proposal_revisi = $request->file('revisi_file_proposal');
            $filename = time() . '_' . $file_proposal_revisi->getClientOriginalName();
            $file_proposal_revisi->storeAs('proposals_revisi', $filename);
            $proposal->revisi_file_proposal = 'proposals_revisi/' . $filename;
        }

        $proposal->save();

        return redirect()->back()->with('success', 'Revisi proposal berhasil disimpan.');
    }





    // public function simpanCatatanRevisi(Request $request, $id)
    // {
    //     if (!Auth::check()) {
    //         return redirect('/login')->with('message', 'Please log in to continue.');
    //     }
    //     $user = Auth::user();

    //     // Cari data proposal berdasarkan $id
    //     $proposal = Proposal::findOrFail($id);

    //     $catatanBaru = trim($request->input('catatan'));
    //     $existing = $proposal->catatan_revisi ?? '';

    //     $isUtama = $user->pembimbingUtama ?? false;
    //     $isPendamping = $user->pembimbingPendamping ?? false;

    //     $parts = explode("Pembimbing Pendamping:", $existing);

    //     $pembimbingUtama = isset($parts[0]) ? trim(str_replace("Pembimbing Utama:", "", $parts[0])) : '';
    //     $pembimbingPendamping = isset($parts[1]) ? trim($parts[1]) : '';

    //     if ($isUtama) {
    //         $pembimbingUtama = $catatanBaru;
    //     } elseif ($isPendamping) {
    //         $pembimbingPendamping = $catatanBaru;
    //     } else {
    //         return back()->with('error', 'Anda tidak memiliki izin mengupdate catatan revisi.');
    //     }

    //     $proposal->catatan_revisi = "Pembimbing Utama:\n" . $pembimbingUtama . "\n\nPembimbing Pendamping:\n" . $pembimbingPendamping;
    //     $proposal->save();

    //     return back()->with('success', 'Catatan revisi berhasil disimpan.');
    // }
}
