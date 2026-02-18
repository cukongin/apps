<?php

namespace App\Keuangan\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class BackupController extends \App\Http\Controllers\Controller
{
    /**
     * Generate and download SQL Dump
     */
    public function download()
    {
        // ... (existing download logic) ...
        // Get all table names
        $tables = DB::select('SHOW TABLES');
        $dbName = env('DB_DATABASE'); // Should handle config correctly usually
        // The result of SHOW TABLES is an object with a property named "Tables_in_dbname"
        
        $out = "SET FOREIGN_KEY_CHECKS=0;\n";
        $out .= "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n";
        $out .= "SET AUTOCOMMIT = 0;\n";
        $out .= "START TRANSACTION;\n";
        $out .= "SET time_zone = \"+00:00\";\n\n";

        foreach ($tables as $tablePayload) {
            // Determine key name for table
            $tableArray = (array)$tablePayload;
            $table = reset($tableArray);
            
            // Add Drop Table
            $out .= "DROP TABLE IF EXISTS `$table`;\n";

            // Get Create Table Schema
            $createTable = DB::select("SHOW CREATE TABLE `$table`");
            $createTableSql = $createTable[0]->{'Create Table'};
            
            $out .= $createTableSql . ";\n\n";
            
            // Get Data
            $rows = DB::table($table)->get();
            
            foreach ($rows as $row) {
                $out .= "INSERT INTO `$table` VALUES(";
                $values = [];
                foreach ($row as $value) {
                    if (is_null($value)) {
                        $values[] = "NULL";
                    } elseif (is_numeric($value)) {
                        $values[] = $value;
                    } else {
                        $values[] = "'" . addslashes($value) . "'";
                    }
                }
                $out .= implode(', ', $values);
                $out .= ");\n";
            }
        }

        $out .= "\nSET FOREIGN_KEY_CHECKS=1;\n";
        $out .= "COMMIT;\n";

        $filename = 'backup_bendana_' . date('Y-m-d_H-i-s') . '.sql';

        return Response::make($out, 200, [
            'Content-Type' => 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Restore Database from SQL Dump
     */
    public function restore(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|file|mimes:sql,txt',
        ]);

        try {
            $file = $request->file('backup_file');
            $sql = file_get_contents($file->getRealPath());

            // 1. Disable FK & Wipe Constraints
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            
            // 2. Clear existing tables to prevent "Table exists" error
            $tables = DB::select('SHOW TABLES');
            foreach ($tables as $table) {
                $array = (array) $table;
                $tableName = reset($array);
                DB::statement("DROP TABLE IF EXISTS `$tableName`");
            }

            // 3. Execute the raw SQL Dump
            // Note: DB::unprepared is needed for multiple statements
            DB::unprepared($sql);
            
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            return redirect()->back()->with('success', 'Database berhasil direstore! Sistem kembali ke kondisi saat backup dibuat.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal merestore database: ' . $e->getMessage());
        }
    }
    /**
     * Reset Database (Truncate Tables) but Keep Users & Config
     */
    public function reset(Request $request)
    {
        $request->validate([
            'confirm_reset' => 'required|in:RESET',
        ]);

        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            // Transactional Data
            DB::table('transaksis')->truncate();
            DB::table('tagihans')->truncate();
            DB::table('tabungans')->truncate();
            DB::table('pengeluarans')->truncate();
            DB::table('pemasukans')->truncate();
            
            // Master Data (Student & Class)
            // Note: We keep Levels and Cost Types (JenisBiaya) as they are likely Config
            DB::table('siswas')->truncate();
            DB::table('kelas')->truncate();
            
            // Optional: Logs
            // DB::table('audit_logs')->truncate(); // Uncomment if logs should be cleared

            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            return redirect()->back()->with('success', 'Database berhasil di-reset! Data Guru/User dan Pengaturan TETAP AMAN. Silakan input data asli.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal reset database: ' . $e->getMessage());
        }
    }
}

