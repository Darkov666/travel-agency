<?php

namespace App\Imports;

use App\Models\Provider;
use App\Models\Tariff;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TariffsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Headers: 'Proveedor', 'zona', 'pasajeros', 'costo - mayorista'
        echo "Processing row: " . json_encode($row) . PHP_EOL;

        $providerName = $row['proveedor'] ?? 'Enjoy Transfers';
        // echo "Provider Name: '$providerName'" . PHP_EOL; // Verified log
        if (!$providerName) {
            $providerName = 'Enjoy Transfers';
        }

        // Find or Create Provider
        // Mark 'Enjoy Transfers' as default if found
        $isDefault = strtolower($providerName) === 'enjoy transfers';

        $provider = Provider::firstOrCreate(
            ['name' => $providerName],
            ['is_default' => $isDefault, 'is_active' => true]
        );

        $zone = $row['zona'] ?? null;
        $pax = $row['pasajeros'] ?? null;
        $cost = $row['costo_-_mayorista'] ?? $row['costo_mayorista'] ?? 0;

        // Calculate Price: Cost + 10%
        $price = $cost * 1.10;

        if (!$zone || !$pax) {
            return null;
        }

        return new Tariff([
            'provider_id' => $provider->id,
            'zone' => $zone,
            'pax' => $pax,
            'cost' => $cost,
            'price' => $price,
        ]);
    }
}
