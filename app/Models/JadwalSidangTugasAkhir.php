<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalSidangTugasAkhir extends Model
{
    use HasFactory;

    protected $table = 'jadwal_sidang_tugas_akhir';

    protected $fillable = [
        'mahasiswa_id',
        'pembimbing_utama_id',
        'pembimbing_pendamping_id',
        'penguji_utama_id',
        'penguji_pendamping_id',
        'tanggal',
        'waktu_mulai',
        'waktu_selesai',
        'ruangan_sidang_id',
    ];

    // Relasi ke mahasiswa
    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    // Relasi ke pembimbing utama
    public function pembimbingUtama()
    {
        return $this->belongsTo(Dosen::class, 'pembimbing_utama_id');
    }

    // Relasi ke pembimbing pendamping
    public function pembimbingPendamping()
    {
        return $this->belongsTo(Dosen::class, 'pembimbing_pendamping_id');
    }

    // Relasi ke penguji utama
    public function pengujiUtama()
    {
        return $this->belongsTo(Dosen::class, 'penguji_utama_id');
    }

    // Relasi ke penguji pendamping
    public function pengujiPendamping()
    {
        return $this->belongsTo(Dosen::class, 'penguji_pendamping_id');
    }

    // Relasi ke ruangan sidang
    public function ruanganSidang()
    {
        return $this->belongsTo(RuanganSidang::class, 'ruangan_sidang_id');
    }
}
