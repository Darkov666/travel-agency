<?php
$org = App\Models\Organization::where('slug', 'cancun-sunny')->first();
if ($org) {
    echo "Organization: " . $org->name . "\n";
    echo "Settings: " . json_encode($org->settings, JSON_PRETTY_PRINT) . "\n";

    // Simulate logic
    $modules = $org->settings['modules'] ?? ['transport', 'tours', 'shop'];
    echo "Modules: " . implode(', ', $modules) . "\n";
} else {
    echo "Organization not found\n";
}
