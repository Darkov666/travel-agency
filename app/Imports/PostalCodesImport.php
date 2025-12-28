<?php

namespace App\Imports;

use App\Models\PostalCode;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PostalCodesImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Headers from codigos postales.xlsx:
        // Código Postal▼, Municipio, Ciudad, Asentamiento, Tipo de Asentamiento, Zona

        $code = $row['codigo_postal'] ?? null;

        if (!$code) {
            return null;
        }

        return new PostalCode([
            'postal_code' => $code,
            'municipality' => $row['municipio'] ?? '',
            'city' => $row['ciudad'] ?? null,
            'settlement' => $row['asentamiento'] ?? '',
            'settlement_type' => $row['tipo_de_asentamiento'] ?? '',
            'zone' => $row['zona'] ?? null,
            'state' => null, // Not present in file, nullable in DB
        ]);
    }
}
