<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('mapel_plotting', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_jenjang'); // Foreign key logic manual if table constrained
            $table->integer('tingkat_kelas');
            $table->foreignId('id_mapel')->constrained('mapel')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['id_jenjang', 'tingkat_kelas', 'id_mapel'], 'unique_plotting');
        });
    }

    public function down()
    {
        Schema::dropIfExists('mapel_plotting');
    }
};
