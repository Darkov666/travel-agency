<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class MainImport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            0 => new PostalCodesImport(),
            1 => new TariffsImport(),
        ];
    }
}
