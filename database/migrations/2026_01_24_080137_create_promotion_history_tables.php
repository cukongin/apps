<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePromotionHistoryTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promotion_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('action_type'); // magic_level, manual_promote, manual_graduate
            $table->string('batch_name'); // e.g. "Kenaikan Level Ula" or "Promosi Kelas 1 Ula A"
            $table->integer('details_count')->default(0);
            $table->timestamps();
        });

        Schema::create('promotion_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->constrained('promotion_batches')->onDelete('cascade');
            $table->foreignId('siswa_id')->constrained('siswa')->onDelete('cascade');
            $table->foreignId('old_kelas_id')->nullable()->constrained('kelas')->onDelete('set null');
            $table->foreignId('new_kelas_id')->nullable()->constrained('kelas')->onDelete('set null');
            $table->string('old_status');
            $table->string('new_status');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('promotion_details');
        Schema::dropIfExists('promotion_batches');
    }
}

