<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFinancialTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Table: jenis_biayas (Master Data Biaya)
        Schema::create('jenis_biayas', function (Blueprint $table) {
            $table->id();
            $table->string('nama'); // e.g., SPP, Seragam, Uang Pangkal
            $table->decimal('jumlah', 15, 2)->default(0); // Default amount
            $table->enum('tipe', ['bulanan', 'sekali'])->default('sekali'); // Payment type
            $table->timestamps();
        });

        // Table: tagihans (Student Bills)
        Schema::create('tagihans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa')->onDelete('cascade');
            $table->foreignId('jenis_biaya_id')->constrained('jenis_biayas')->onDelete('cascade');
            $table->decimal('jumlah', 15, 2); // Amount to pay
            $table->decimal('terbayar', 15, 2)->default(0); // Amount paid
            $table->enum('status', ['belum', 'lunas', 'cicilan'])->default('belum');
            $table->string('keterangan')->nullable(); // e.g., "Oktober 2023"
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tagihans');
        Schema::dropIfExists('jenis_biayas');
    }
}

