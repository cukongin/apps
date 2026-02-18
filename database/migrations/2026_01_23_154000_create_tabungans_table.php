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
        Schema::create('tabungans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa')->onDelete('cascade');
            $table->enum('tipe', ['setor', 'tarik'])->comment('Jenis transaksi: setor / tarik');
            $table->decimal('jumlah', 15, 2);
            $table->text('keterangan')->nullable();
            $table->decimal('saldo_akhir', 15, 2)->nullable()->comment('Saldo setelah transaksi ini');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tabungans');
    }
};

