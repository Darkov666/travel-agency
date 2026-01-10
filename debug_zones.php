<?php
$zone15 = \App\Models\Zone::with('providerServices')->find(15);
$zone23 = \App\Models\Zone::with('providerServices')->find(23);

echo "--- Zone 15: {$zone15->name} ---\n";
echo "Services Linked: " . $zone15->providerServices->count() . "\n";

echo "--- Zone 23: {$zone23->name} ---\n";
echo "Services Linked: " . $zone23->providerServices->count() . "\n";
