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
        Schema::create('dosen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('nama_dosen', 100); // maksimal 100
            $table->integer('nip')->unique(); // validasi panjang di controller (maks 50 digit)
            $table->string('tempat_lahir', 100); // maksimal 100
            $table->date('tanggal_lahir');
            $table->string('jenis_kelamin', 9); // maksimal 9
            $table->string('jabatan', 25)->nullable(); // maksimal 25
            $table->foreignId('program_studi_id')->nullable()->constrained('program_studi')->onDelete('set null');
            $table->string('ttd_dosen')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dosen');
    }
};
