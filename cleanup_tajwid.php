<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// 1. Find Tajwid Mapel
$tajwid = \App\Models\Mapel::where('nama_mapel', 'Tajwid')->first();
if (!$tajwid) {
    echo "Mapel Tajwid not found.\n";
    exit;
}
echo "Tajwid ID: " . $tajwid->id . "\n";

// 2. Find Incorrect Tajwid Grades (Tajwid used in MTs Classes)
// Safe Delete: Only delete if the grade is associated with a class level >= 7 (MTs)
// This preserves Tajwid grades for the same student if they were in MI (Level < 7)

$query = \App\Models\NilaiSiswa::where('id_mapel', $tajwid->id)
    ->whereHas('kelas', function($q) {
        $q->where('tingkat_kelas', '>=', 7);
    });

$count = $query->count();
echo "Found $count incorrect Tajwid grades associated with MTs classes (Level >= 7).\n";

// 3. Delete
if ($count > 0) {
    if (in_array('--force', $argv)) {
        $query->delete();
        echo "Successfully deleted $count records.\n";
    } else {
        echo "Dry Run completed. Run with --force to actually delete.\n";
    }
} else {
    echo "Nothing to delete.\n";
}
