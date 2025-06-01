<?php

use App\Http\Controllers\JadwalSeminarProposalController;
use App\Http\Controllers\PendaftaranSidangController;
use App\Http\Controllers\PendaftaranSidangTAController;
use App\Models\RuanganSidang;
use App\Models\LogbookBimbingan;
use App\Models\PengajuanPembimbing;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\DosenController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\TahunAjaranController;
use App\Http\Controllers\ProgramStudiController;
use App\Http\Controllers\RuanganSidangController;
use App\Http\Controllers\JadwalBimbinganController;
use App\Http\Controllers\JadwalSidangTugasAkhirController;
use App\Http\Controllers\LogbookBimbinganController;
use App\Http\Controllers\NilaiController;
use App\Http\Controllers\PengajuanPembimbingController;
use App\Http\Controllers\ProposalController;
use App\Models\JadwalSeminarProposal;
use App\Models\JadwalSidangTugasAkhir;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');


// Route::get('/dashboard', function () {
//     return view('dashboard.index');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';

// Route::prefix('dashboard')->group(function () {
//     Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index');
// });

Route::prefix('user')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('user.index'); // Menampilkan semua pengguna
    // Route::put('/{id}', [UserController::class, 'update'])->name('user.update');
    Route::get('/search', [UserController::class, 'search'])->name('user.search');
    Route::post('/{user}/reset-password', [UserController::class, 'resetPassword'])->name('user.resetPassword'); // Mereset password pengguna
    Route::get('/dropdown-search', [UserController::class, 'dropdownSearch'])->name('user.dropdown-search');
});

Route::prefix('program_studi')->group(function () {
    Route::get('/', [ProgramStudiController::class, 'index'])->name('program_studi.index');
    Route::post('/', [ProgramStudiController::class, 'store'])->name('program_studi.store');
    Route::put('/{id}', [ProgramStudiController::class, 'update'])->name('program_studi.update');
    Route::get('/search', [ProgramStudiController::class, 'search'])->name('program_studi.search');
    Route::delete('/{id}', [ProgramStudiController::class, 'destroy'])->name('program_studi.destroy');
});

Route::prefix('ruangan_sidang')->group(function () {
    Route::get('/', [RuanganSidangController::class, 'index'])->name('ruangan_sidang.index');
    Route::post('/', [RuanganSidangController::class, 'store'])->name('ruangan_sidang.store');
    Route::put('/{id}', [RuanganSidangController::class, 'update'])->name('ruangan_sidang.update');
    Route::get('/search', [RuanganSidangController::class, 'search'])->name('ruangan_sidang.search');
    Route::delete('/{id}', [RuanganSidangController::class, 'destroy'])->name('ruangan_sidang.destroy');
});

Route::prefix('tahun_ajaran')->group(function () {
    Route::get('/', [TahunAjaranController::class, 'index'])->name('tahun_ajaran.index');
    Route::post('/', [TahunAjaranController::class, 'store'])->name('tahun_ajaran.store');
    Route::put('/{id}', [TahunAjaranController::class, 'update'])->name('tahun_ajaran.update');
    Route::get('/search', [TahunAjaranController::class, 'search'])->name('tahun_ajaran.search');
    Route::delete('/{id}', [TahunAjaranController::class, 'destroy'])->name('tahun_ajaran.destroy');
});

Route::prefix('mahasiswa')->group(function () {
    Route::get('/', [MahasiswaController::class, 'index'])->name('mahasiswa.index');
    Route::post('/', [MahasiswaController::class, 'store'])->name('mahasiswa.store');
    Route::put('/{id}', [MahasiswaController::class, 'update'])->name('mahasiswa.update');
    Route::get('/search', [MahasiswaController::class, 'search'])->name('mahasiswa.search');
    Route::get('/dropdown-search', [MahasiswaController::class, 'dropdownSearch'])->name('mahasiswa.dropdown-search');
    Route::delete('/{id}', [MahasiswaController::class, 'destroy'])->name('mahasiswa.destroy');
    Route::post('/import', [MahasiswaController::class, 'import'])->name('mahasiswa.import');
});

Route::prefix('dosen')->group(function () {
    Route::get('/', [DosenController::class, 'index'])->name('dosen.index');
    Route::post('/', [DosenController::class, 'store'])->name('dosen.store');
    Route::put('/{id}', [DosenController::class, 'update'])->name('dosen.update');
    Route::get('/search', [DosenController::class, 'search'])->name('dosen.search');
    Route::delete('/{id}', [DosenController::class, 'destroy'])->name('dosen.destroy');
    Route::get('/dosen/mahasiswa-bimbingan', [DosenController::class, 'mahasiswaBimbingan'])->name('dosen.mahasiswa-bimbingan');
    Route::post('/import', [DosenController::class, 'import'])->name('dosen.import');
});

Route::prefix('proposal')->group(function () {
    Route::get('/', [ProposalController::class, 'index'])->name('proposal.index');
    Route::post('/', [ProposalController::class, 'store'])->name('proposal.store');
    Route::get('/{id}/proposal', [ProposalController::class, 'showFile'])->name('proposal.showFile');
    Route::get('/{mahasiswaId}', [ProposalController::class, 'showKaprodi'])->name('proposal.show_kaprodi');    // Route::get('/{mahasiswaId}', [LogbookBimbinganController::class, 'show'])->name('logbook_bimbingan.show');

});

Route::prefix('pengajuan_pembimbing')->group(function () {
    Route::get('/dosen', [PengajuanPembimbingController::class, 'index'])->name('pengajuan_pembimbing.index');
    Route::get('/list-pengajuan', [PengajuanPembimbingController::class, 'indexKaprodi'])->name('pengajuan_pembimbing.index_kaprodi');
    Route::post('/', [PengajuanPembimbingController::class, 'store'])->name('pengajuan_pembimbing.store');
    Route::put('/{id}/validasi', [PengajuanPembimbingController::class, 'validasi'])->name('pengajuan_pembimbing.validasi');
    Route::put('/{id}', [PengajuanPembimbingController::class, 'update'])->name('pengajuan_pembimbing.update');
    Route::get('/search', [PengajuanPembimbingController::class, 'search'])->name('pengajuan_pembimbing.search');
    Route::get('/list-pengajuan/dropdown-search', [PengajuanPembimbingController::class, 'dropdownSearch'])->name('pengajuan_pembimbing.dropdown-search');
    Route::get('/dosen/dropdown-search', [PengajuanPembimbingController::class, 'dropdownSearchDosen'])->name('pengajuan_pembimbing.dropdown-search_dosen');

    Route::delete('/{id}', [PengajuanPembimbingController::class, 'destroy'])->name('pengajuan_pembimbing.destroy');
});

Route::prefix('jadwal_bimbingan')->group(function () {
    Route::get('/dosen', [JadwalBimbinganController::class, 'index'])->name('jadwal_bimbingan.index');
    Route::get('/list-bimbingan', [JadwalBimbinganController::class, 'indexKaprodi'])->name('jadwal_bimbingan.index_kaprodi');
    Route::post('/', [JadwalBimbinganController::class, 'store'])->name('jadwal_bimbingan.store');
    Route::post('/jadwal-bimbingan/daftar/{id}', [JadwalBimbinganController::class, 'daftarBimbingan'])->name('jadwal_bimbingan.daftar');
    Route::put('/{id}', [JadwalBimbinganController::class, 'update'])->name('jadwal_bimbingan.update');
    Route::get('/detail/{id}', [JadwalBimbinganController::class, 'detail'])->name('jadwal_bimbingan.detail');
    Route::get('/list-bimbingan/dropdown-search', [JadwalBimbinganController::class, 'dropdownSearch'])->name('jadwal_bimbingan.dropdown-search');
    Route::delete('/{id}', [JadwalBimbinganController::class, 'destroy'])->name('jadwal_bimbingan.destroy');
});

Route::prefix('logbook_bimbingan')->group(function () {
    Route::get('/mahasiswa', [LogbookBimbinganController::class, 'indexMahasiswa'])->name('logbook_bimbingan.index_mahasiswa');
    // Route::get('/', [LogbookBimbinganController::class, 'indexKaprodi'])->name('logbook_bimbingan.index_kaprodi');
    Route::get('/{id}/logbook', [LogbookBimbinganController::class, 'showFile'])->name('logbook_bimbingan.showFile');
    Route::patch('/{id}/update-permasalahan', [LogbookBimbinganController::class, 'updatePermasalahan'])->name('logbook_bimbingan.update_permasalahan');
    Route::get('/{dosenId}/{mahasiswaId}', [LogbookBimbinganController::class, 'showMahasiswa'])->name('logbook_bimbingan.show_mahasiswa');    // Route::get('/{mahasiswaId}', [LogbookBimbinganController::class, 'show'])->name('logbook_bimbingan.show');
    Route::get('/{mahasiswaId}', [LogbookBimbinganController::class, 'showKaprodi'])->name('logbook_bimbingan.show_kaprodi');    // Route::get('/{mahasiswaId}', [LogbookBimbinganController::class, 'show'])->name('logbook_bimbingan.show');
    Route::post('/', [LogbookBimbinganController::class, 'store'])->name('logbook_bimbingan.store');
    Route::put('/{id}', [LogbookBimbinganController::class, 'update'])->name('logbook_bimbingan.update');
    Route::post('/{id}/beri-rekomendasi', [LogbookBimbinganController::class, 'beriRekomendasi'])->name('logbook_bimbingan.rekomendasi');
});

Route::prefix('pendaftaran_sidang')->group(function () {
    Route::get('/mahasiswa', [PendaftaranSidangController::class, 'index'])->name('pendaftaran_sidang.index');
    Route::get('/kaprodi', [PendaftaranSidangController::class, 'indexKaprodi'])->name('pendaftaran_sidang.index_kaprodi');
    Route::post('/', [PendaftaranSidangController::class, 'store'])->name('pendaftaran_sidang.store');
    Route::get('/file/{id}/{fileField}', [PendaftaranSidangController::class, 'showFile'])->name('pendaftaran_sidang.showFile');
});

Route::prefix('jadwal_sidang')->group(function () {
    Route::get('/tugas_akhir', [JadwalSidangTugasAkhirController::class, 'index'])->name('jadwal_sidang_tugas_akhir.index');
    Route::post('/tugas_akhir/import', [JadwalSidangTugasAkhirController::class, 'import'])->name('jadwal_sidang_tugas_akhir.import');
    Route::put('/tugas_akhir/{id}', [JadwalSidangTugasAkhir::class, 'update'])->name('jadwal_sidang_tugas_akhir.update');

    Route::get('/seminar_proposal', [JadwalSeminarProposalController::class, 'index'])->name('jadwal_seminar_proposal.index');
    Route::post('/seminar_proposal/import', [JadwalSeminarProposalController::class, 'import'])->name('jadwal_seminar_proposal.import');
    Route::put('/seminar_proposal/{id}', [JadwalSeminarProposal::class, 'update'])->name('jadwal_seminar_proposal.update');
});

Route::prefix('nilai')->group(function () {
    Route::get('proposal/dosen/', [NilaiController::class, 'indexProposalDosen'])->name('nilai.proposal_dosen');
    Route::get('tugas_akhir/dosen/', [NilaiController::class, 'indexTugasAkhirDosen'])->name('nilai.tugas_akhir_dosen');
    Route::get('/mahasiswa', [NilaiController::class, 'indexMahasiswa'])->name('nilai.index_mahasiswa');
    Route::get('/kaprodi', [NilaiController::class, 'daftarNilai'])->name('nilai.daftar_nilai');
    Route::post('/{mahasiswa}', [NilaiController::class, 'store'])->name('nilai.store');

    // Route::post('/', [NilaiController::class, 'store'])->name('nilai.store');
    // Route::get('/{id}/edit', [NilaiController::class, 'edit'])->name('nilai.edit');
});



Route::get('file_bimbingan/{filename}', function ($filename) {
    // Memastikan file ada di storage dan dapat diakses
    $path = storage_path('app/public/' . $filename);

    if (!file_exists($path)) {
        abort(404);  // Jika file tidak ada, tampilkan halaman 404
    }
    return Response::file($path);
})->name('file_bimbingan');


    // Route::get('/{filename}', [LogbookBimbinganController::class, 'show'])->name('logbook_bimbingan.show');
