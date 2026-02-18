<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDetailsToJenisBiayasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('jenis_biayas', function (Blueprint $table) {
            $table->string('kategori')->default('Operasional'); // e.g., Pembangunan, Operasional
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->string('target_type')->default('all'); // all, class, level
            $table->string('target_value')->nullable(); // e.g., "7", "MDT Ula"
        });
    }

    public function down()
    {
        Schema::table('jenis_biayas', function (Blueprint $table) {
            $table->dropColumn(['kategori', 'status', 'target_type', 'target_value']);
        });
    }
}
