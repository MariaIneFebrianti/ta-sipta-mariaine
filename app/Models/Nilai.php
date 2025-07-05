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
}
