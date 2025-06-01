<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class Nilai extends Model
{

    use HasFactory;

    protected $table = 'nilai';

    protected $fillable = [
        'mahasiswa_id',
        'nilai_seminar_utama',
        'nilai_seminar_pendamping',
        'nilai_ta_utama',
        'nilai_ta_pendamping',
        'nilai_ta_penguji_utama',
        'nilai_ta_penguji_pendamping',
        'nilai_bimbingan',
        'nilai_seminar',
        'nilai_ta',
    ];

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'mahasiswa_id');
    }

    // public function pembimbingUtama()
    // {
    //     return $this->belongsTo(Dosen::class, 'pembimbing_utama_id');
    // }

    // // Relasi ke pembimbing pendamping
    // public function pembimbingPendamping()
    // {
    //     return $this->belongsTo(Dosen::class, 'pembimbing_pendamping_id');
    // }

    // // Relasi ke penguji utama
    // public function pengujiUtama()
    // {
    //     return $this->belongsTo(Dosen::class, 'penguji_utama_id');
    // }

    // // Relasi ke penguji pendamping
    // public function pengujiPendamping()
    // {
    //     return $this->belongsTo(Dosen::class, 'penguji_pendamping_id');
    // }
}
