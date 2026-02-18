<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentScheduleToJenisBiayasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('jenis_biayas', function (Blueprint $table) {
            $table->date('due_date')->nullable()->after('target_value');
            $table->tinyInteger('recurring_day')->nullable()->after('due_date'); // e.g. 10 (tanggal 10 setiap bulan)
        });
    }

    public function down()
    {
        Schema::table('jenis_biayas', function (Blueprint $table) {
            $table->dropColumn(['due_date', 'recurring_day']);
        });
    }
}
