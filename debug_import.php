<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Maatwebsite\Excel\Facades\Excel;
use App\Imports\MainImport;

try {
    // Note: Excel path is relative to storage/app usually, or absolute. 
    // If 'codigos postales.xlsx' is in root, we might need full path or ensure it's found.
    // The user executed: Excel::import(..., 'codigos postales.xlsx');
    // Default disk is usually 'local' (storage/app). But if it worked for PostalCodes, it must be findable.
    // If it's in the project root, we should pass base_path('codigos postales.xlsx').

    $path = 'codigos postales.xlsx';
    if (!file_exists($path)) {
        // user ran it successfully, maybe it is in public or root?
        if (file_exists(base_path($path))) {
            $path = base_path($path);
        }
    }

    echo "Importing from: " . $path . PHP_EOL;

    $data = Excel::toArray(new MainImport, $path);

    // Sheet 0: Postal Codes
    echo "Sheet 0 (Postal Codes) Rows: " . count($data[0]) . PHP_EOL;

    // Sheet 1: Tariffs
    if (isset($data[1])) {
        echo "Sheet 1 (Tariffs) Rows: " . count($data[1]) . PHP_EOL;
        if (count($data[1]) > 0) {
            echo "Sheet 1 First Row Keys: " . implode(', ', array_keys($data[1][0])) . PHP_EOL;
            echo "Sheet 1 First Row Data: " . json_encode($data[1][0]) . PHP_EOL;
        }
    } else {
        echo "Sheet 1 not found." . PHP_EOL;
    }

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
}
