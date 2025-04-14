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
        $userRole = Auth::user()->role;
        $userCount = User::count();
        $mahasiswaCount = Mahasiswa::count();
        $dosenCount = Dosen::count();
        $programstudiCount = ProgramStudi::count();

        return view('dashboard.index', compact('userCount', 'mahasiswaCount', 'dosenCount', 'programstudiCount', 'userRole'));
    }
}
