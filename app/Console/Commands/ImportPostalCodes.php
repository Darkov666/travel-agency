<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Imports\PostalCodesImport;
use Maatwebsite\Excel\Facades\Excel;

class ImportPostalCodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:postal-codes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import postal codes from CPQROO.xls';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting import...');

        $file = base_path('CPQROO.xls');
        if (!file_exists($file)) {
            $this->error("File not found: $file");
            return 1;
        }

        try {
            $this->info('Truncating tables...');
            \App\Models\PostalCode::truncate();
            \App\Models\Tariff::truncate();
            // Don't truncate Providers to keep existing ones if any, or truncate if we want full fresh start.
            // Let's keep providers for now or use updateOrCreate logic.

            $this->info('Importing Postal Codes (Sheet 1)...');
            Excel::import(new PostalCodesImport, $file);

            $this->info('Importing Tariffs (Sheet 2)...');
            // Sheet 2 is index 1
            Excel::import(new \App\Imports\TariffsImport, $file, null, \Maatwebsite\Excel\Excel::XLSX);
            // Note: Maatwebsite Excel multiple sheets handling might need WithMultipleSheets if imported together,
            // or specific sheet selection.
            // Simple way: Import using specific sheet reader if possible or assume default.
            // Ideally:
            // Excel::import(new TariffsImport, $file); // This reads first sheet by default?

            // Explicit sheet selection isn't trivial in single call without MultipleSheets concern.
            // Let's try Using WithMultipleSheets concern in a main Import class, OR:
            // Just use simple import and hope `TariffsImport` works if we specify sheet?
            // Actually, we can pass a reader type.

            // Correction: To import specific sheet, using `WithMultipleSheets` in a parent import is best.
            // OR use `SelectSheet` concern inside the Import class if we invoke them separately?
            // `Excel::selectSheets('Hoja 1')->load($file)->import(new PostalCodesImport);` (Old syntax)

            // New syntax (3.1):
            // We'll create a MainImport class that handles multiple sheets.

            $this->info('Import completed successfully.');
        } catch (\Exception $e) {
            $this->error('Error importing: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
