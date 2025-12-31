<?php

namespace App\Services;

use App\Models\Zone;

class ZoneMatcher
{
    /**
     * Find the zone that contains the given coordinates.
     *
     * @param float $lat
     * @param float $lng
     * @return Zone|null
     */
    public function match(float $lat, float $lng): ?Zone
    {
        // Fetch all zones with coordinates
        // Optimization: In a real app with many zones, you'd filter by bounding box first using SQL
        $zones = Zone::whereNotNull('coordinates')->get();

        foreach ($zones as $zone) {
            $polygon = $zone->coordinates;
            if ($polygon && $this->pointInPolygon($lat, $lng, $polygon)) {
                return $zone;
            }
        }

        return null;
    }

    /**
     * Ray-casting algorithm to check if a point is inside a polygon.
     */
    private function pointInPolygon($lat, $lng, $polygon)
    {
        $inside = false;
        $count = count($polygon);

        for ($i = 0, $j = $count - 1; $i < $count; $j = $i++) {
            $xi = $polygon[$i]['lat'];
            $yi = $polygon[$i]['lng'];
            $xj = $polygon[$j]['lat'];
            $yj = $polygon[$j]['lng'];

            $intersect = (($yi > $lng) != ($yj > $lng))
                && ($lat < ($xj - $xi) * ($lng - $yi) / ($yj - $yi) + $xi);

            if ($intersect) {
                $inside = !$inside;
            }
        }

        return $inside;
    }
}
