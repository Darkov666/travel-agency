$orgId = \Illuminate\Support\Facades\DB::table('organizations')->value('id') ?? 1;
echo "Org: $orgId\n";

$services = [
[
'id' => 101,
'title' => 'Chichen Itza Deluxe Tour',
'slug' => 'chichen-itza-deluxe',
'description' => 'Guided tour to the wonder of the world with buffet lunch and cenote visit included.',
'price' => 129,
'price_mxn' => 2580,
'price_usd' => 129,
'type' => 'tour',
'image' => 'https://images.unsplash.com/photo-1518638151313-982d2ba5011b?q=80&w=800&auto=format&fit=crop',
],
[
'id' => 102,
'title' => 'Xcaret Plus Package',
'slug' => 'xcaret-plus-package',
'description' => 'Full day access to Xcaret park with buffet lunch and night show.',
'price' => 159,
'price_mxn' => 3180,
'price_usd' => 159,
'type' => 'tour',
'image' => 'https://images.unsplash.com/photo-1534151759604-03738dbb772c?q=80&w=800&auto=format&fit=crop',
],
[
'id' => 103,
'title' => 'Catamaran to Isla Mujeres',
'slug' => 'catamaran-isla-mujeres',
'description' => 'Sail the Caribbean sea, snorkel in the reef and enjoy an open bar.',
'price' => 89,
'price_mxn' => 1780,
'price_usd' => 89,
'type' => 'tour',
'image' => 'https://images.unsplash.com/photo-1544551763-46a42a46e865?q=80&w=800&auto=format&fit=crop',
],
[
'id' => 104,
'title' => 'Cancun Sunny Cap',
'slug' => 'cancun-sunny-cap',
'description' => 'Exclusive branded cap to protect you from the sun in style.',
'price' => 25,
'price_mxn' => 500,
'price_usd' => 25,
'type' => 'tour',
'image' => 'https://images.unsplash.com/photo-1588850561407-ed78c282e89b?q=80&w=800&auto=format&fit=crop',
],
[
'id' => 105,
'title' => 'Tulum & Coba Expedition',
'slug' => 'tulum-coba-expedition',
'description' => 'Explore two ancient Mayan cities in one day. Transport and guide included.',
'price' => 110,
'price_mxn' => 2200,
'price_usd' => 110,
'type' => 'tour',
'image' => 'https://images.unsplash.com/photo-1506869640319-fe1a24fd76dc?q=80&w=800&auto=format&fit=crop',
]
];

foreach ($services as $data) {
try {
// Aggressive cleanup for ID 105
if ($data['id'] == 105) {
\Illuminate\Support\Facades\DB::table('services')->where('id', 105)->delete();
\Illuminate\Support\Facades\DB::table('services')->where('slug', 'tulum-coba-expedition')->delete();
}

$conflict = \Illuminate\Support\Facades\DB::table('services')->where('slug', $data['slug'])->where('id', '!=',
$data['id'])->first();
if ($conflict) {
echo "Removing slug conflict ID {$conflict->id}\n";
\Illuminate\Support\Facades\DB::table('services')->where('id', $conflict->id)->delete();
}

$formatted = [
'organization_id' => $orgId,
'title' => $data['title'],
'slug' => $data['slug'],
'description' => $data['description'],
'price' => $data['price'],
'price_mxn' => $data['price_mxn'],
'price_usd' => $data['price_usd'],
'type' => $data['type'],
'features' => json_encode(['Shop Item']),
'is_active' => true,
'image' => $data['image'],
'updated_at' => now(),
];

if (\Illuminate\Support\Facades\DB::table('services')->where('id', $data['id'])->exists()) {
echo "Update {$data['id']}\n";
\Illuminate\Support\Facades\DB::table('services')->where('id', $data['id'])->update($formatted);
} else {
echo "Create {$data['id']}\n";
$formatted['id'] = $data['id'];
$formatted['created_at'] = now();
\Illuminate\Support\Facades\DB::table('services')->insert($formatted);
}
} catch (\Exception $e) {
echo "Err {$data['id']}: " . $e->getMessage() . "\n";
}
}

echo "Verification:\n";
$s = \App\Models\Service::find(101);
echo $s ? "Found 101: {$s->title}\n" : "101 Not Found\n";

$s105 = \App\Models\Service::find(105);
echo $s105 ? "Found 105: {$s105->title}\n" : "105 Not Found\n";

exit;