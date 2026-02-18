<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddSebagianToTagihansEnum extends Migration
{
    public function up()
    {
        // Update 'status' to VARCHAR to support 'sebagian' and others
        // Avoid modifying ENUM directly as it causes issues on some DBs
        DB::statement("ALTER TABLE tagihans MODIFY COLUMN status VARCHAR(50) NOT NULL DEFAULT 'belum'");
    }

    public function down()
    {
        // Revert (Careful if data exists)
        // DB::statement("ALTER TABLE tagihans MODIFY COLUMN status ENUM('belum', 'lunas') NOT NULL DEFAULT 'belum'");
    }
}
