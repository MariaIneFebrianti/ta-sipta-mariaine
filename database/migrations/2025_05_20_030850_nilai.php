<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('nilai', function (Blueprint $table) {
            $table->id(); // ID
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->onDelete('cascade');
            $table->float('nilai_seminar_utama')->nullable();
            $table->float('nilai_seminar_pendamping')->nullable();
            $table->float('nilai_ta_utama')->nullable();
            $table->float('nilai_ta_pendamping')->nullable();
            $table->float('nilai_ta_penguji_utama')->nullable();
            $table->float('nilai_ta_penguji_pendamping')->nullable();
            $table->float('nilai_seminar')->nullable(); // Hasil perhitungan
            $table->float('nilai_ta')->nullable(); // Hasil perhitungan
            $table->timestamps(); // created_at dan updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('nilai');
    }
};
