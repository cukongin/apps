<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePemasukansTable extends Migration
{
    public function up()
    {
        Schema::create('pemasukans', function (Blueprint $table) {
            $table->id();
            $table->string('sumber'); // Donatur, Kantin, Hibah, dll
            $table->decimal('jumlah', 15, 2);
            $table->string('kategori')->nullable(); // e.g. Donasi, Usaha Sekolah
            $table->text('keterangan')->nullable();
            $table->date('tanggal_pemasukan');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pemasukans');
    }
}
