<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pendaftaran_sidang', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mahasiswa_id');
            $table->date('tanggal_pendaftaran');
            $table->string('file_tugas_akhir');
            $table->string('file_bebas_pinjaman_administrasi');
            $table->string('file_slip_pembayaran_semester_akhir');
            $table->string('file_transkip_sementara');
            $table->string('file_bukti_pembayaran_sidang_ta');
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('mahasiswa_id')->references('id')->on('mahasiswa')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pendaftaran_sidang');
    }
};
