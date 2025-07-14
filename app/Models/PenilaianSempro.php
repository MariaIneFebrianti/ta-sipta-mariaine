<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenilaianSempro extends Model
{
    protected $table = 'penilaian_sempro';

    protected $fillable = [
        'mahasiswa_id',
        'dosen_id',
        'jadwal_seminar_proposal_id',
        'nilai',
        'catatan_revisi'
    ];

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    public function dosen()
    {
        return $this->belongsTo(Dosen::class);
    }

    public function jadwalSeminar()
    {
        return $this->belongsTo(JadwalSeminarProposal::class, 'jadwal_seminar_proposal_id');
    }
}
