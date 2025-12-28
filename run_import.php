<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Maatwebsite\Excel\Facades\Excel;
use App\Imports\MainImport;
use App\Models\Provider;
use App\Models\Tariff;

use Illuminate\Support\Facades\DB;

DB::listen(function ($query) {
    echo "SQL: " . $query->sql . " [" . implode(', ', $query->bindings) . "]" . PHP_EOL;
});

try {
    echo "Starting Import..." . PHP_EOL;
    $file = 'codigos postales.xlsx';
    if (!file_exists($file)) {
        if (file_exists(base_path($file))) {
            $file = base_path($file);
        } else {
            throw new Exception("File not found: $file");
        }
    }

    Excel::import(new MainImport, $file);
    echo "Import Success." . PHP_EOL;

    echo "Providers: " . Provider::count() . PHP_EOL;
    echo "Tariffs: " . Tariff::count() . PHP_EOL;

    // Check if Enjoy Transfers is default
    $default = Provider::where('is_default', true)->first();
    if ($default) {
        echo "Default Provider: " . $default->name . PHP_EOL;
    } else {
        echo "No Default Provider found." . PHP_EOL;
    }

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
}
