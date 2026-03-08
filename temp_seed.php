<?php

use App\Models\Contribution;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();


Contribution::query()->delete(); 

for($i = 0; $i < 150; $i++) { 
    Contribution::create([
        'day' => now()->subDays(rand(0, 364))->format('Y-m-d'), 
        'count' => rand(1, 10)
    ]); 
}

echo "Seeded database with test contributions.\n";
