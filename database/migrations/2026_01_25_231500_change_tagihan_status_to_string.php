<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ChangeTagihanStatusToString extends Migration
{
    public function up()
    {
        // Change status to string/varchar to support 'sebagian' and future statuses
        // Using DB::statement because Schema code sometimes has issues with Enums in some DB drivers
        DB::statement("ALTER TABLE tagihans MODIFY COLUMN status VARCHAR(50) NOT NULL DEFAULT 'belum'");
    }

    public function down()
    {
        // Revert to Enum if needed (Optional, usually we don't revert to restricted types if data varies)
        // DB::statement("ALTER TABLE tagihans MODIFY COLUMN status ENUM('belum', 'lunas') NOT NULL DEFAULT 'belum'");
    }
}
