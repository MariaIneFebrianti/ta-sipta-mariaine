<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\ProgramStudi;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {

        // if (Auth::check()) {
        //     $userRole = Auth::user()->role;
        // } else {
        //     return redirect('/login')->with('message', 'Please log in to continue.');
        // }

        if (!Auth::check()) {
            return redirect('/login')->with('message', 'Please log in to continue.');
        }

        $user = Auth::user();

        $userCount = User::count();
        $mahasiswaCount = Mahasiswa::count();
        $dosenCount = Dosen::count();
        $programstudiCount = ProgramStudi::count();

        return view('dashboard.index', compact('userCount', 'mahasiswaCount', 'dosenCount', 'programstudiCount', 'user'));
    }
}
