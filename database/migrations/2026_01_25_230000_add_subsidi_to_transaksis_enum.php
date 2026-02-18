<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddSubsidiToTransaksisEnum extends Migration
{
    public function up()
    {
        // Add 'Subsidi' support by changing to VARCHAR (Safer for production)
        DB::statement("ALTER TABLE transaksis MODIFY COLUMN metode_pembayaran VARCHAR(50) NOT NULL DEFAULT 'tunai'");
    }

    public function down()
    {
        // Revert (Careful if data exists)
        // DB::statement("ALTER TABLE transaksis MODIFY COLUMN metode_pembayaran ENUM('tunai', 'tabungan') NOT NULL DEFAULT 'tunai'");
    }
}
