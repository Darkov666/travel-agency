<?php

namespace App\Services;

use App\Models\Tariff;
use App\Models\Provider;

class ProviderSelectionService
{
    /**
     * Get the cheapest provider and the in-house provider for a specific service.
     *
     * @param string $serviceType  'tour' or 'transfer'
     * @param string|int $referenceId ID of the tour/transfer
     * @return array
     */
    public function getProviderOptions(string $serviceType, $referenceId)
    {
        $tariffs = Tariff::with('provider')
            ->where('service_type', $serviceType)
            ->where('reference_id', $referenceId)
            ->orderBy('price', 'asc')
            ->get();

        $cheapest = $tariffs->first();

        // Find In-House provider's tariff for this service
        $inHouse = $tariffs->first(function ($tariff) {
            return $tariff->provider->is_inhouse;
        });

        // Loophole: If no tariff found for in-house, maybe we should just return the provider info?
        // For now, assuming we only care if they have a tariff.

        return [
            'cheapest' => $cheapest,
            'in_house' => $inHouse,
            'all_options' => $tariffs
        ];
    }
}
