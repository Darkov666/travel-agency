$data = [
'id' => 101,
'title' => 'Chichen Itza Deluxe Tour',
'slug' => 'chichen-itza-deluxe',
'description' => 'Test',
'price' => 129,
'price_mxn' => 2580,
'price_usd' => 129,
'type' => 'tour',
'features' => json_encode(['Shop Item']),
'is_active' => true,
'image' => 'https://via.placeholder.com/150',
'updated_at' => now(),
'created_at' => now(),
'organization_id' => 1
];

try {
echo "Deleting 101...\n";
\Illuminate\Support\Facades\DB::table('services')->where('id', 101)->delete();

echo "Deleting slug conflict...\n";
\Illuminate\Support\Facades\DB::table('services')->where('slug', $data['slug'])->delete();

echo "Inserting 101...\n";
\Illuminate\Support\Facades\DB::table('services')->insert($data);
echo "Success!\n";
} catch (\Exception $e) {
echo "ERROR: " . $e->getMessage() . "\n";
}

echo "Check:\n";
dump(\Illuminate\Support\Facades\DB::table('services')->where('id', 101)->first());
exit;