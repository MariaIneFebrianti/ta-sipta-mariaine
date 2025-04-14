<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RuanganSidang extends Model
{
    use HasFactory;

    protected $table = 'ruangan_sidang';
    protected $fillable = [
        'program_studi_id',
        'nama_ruangan',
    ];

    public function programStudi()
    {
        return $this->belongsTo(ProgramStudi::class, 'program_studi_id');
    }
}
