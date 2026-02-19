<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);
$tables = Illuminate\Support\Facades\DB::select('SHOW TABLES');
// Flatten the array of objects to just table names
$tableNames = array_map(function($table) {
    return array_values((array)$table)[0];
}, $tables);
echo json_encode($tableNames, JSON_PRETTY_PRINT);
