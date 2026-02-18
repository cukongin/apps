<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('pengeluaran_details', function (Blueprint $table) {
            $table->string('satuan')->nullable()->after('jumlah');
        });
    }

    public function down()
    {
        Schema::table('pengeluaran_details', function (Blueprint $table) {
            $table->dropColumn('satuan');
        });
    }
};
