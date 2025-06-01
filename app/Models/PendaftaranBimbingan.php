<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PendaftaranBimbingan extends Model
{
    use HasFactory;

    protected $table = 'pendaftaran_bimbingan';
    protected $fillable = ['mahasiswa_id', 'jadwal_bimbingan_id'];

    // Relasi ke Mahasiswa
    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'mahasiswa_id');
    }

    // Relasi ke Jadwal Bimbingan
    public function jadwalBimbingan()
    {
        return $this->belongsTo(JadwalBimbingan::class, 'jadwal_bimbingan_id');
    }

    public function logbooks()
    {
        return $this->hasMany(LogbookBimbingan::class, 'pendaftaran_bimbingan_id');
    }
}
