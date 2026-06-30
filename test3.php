<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$p = App\Models\Page::where('slug', 'about')->first();
echo "--- EN ---\n";
echo $p->content[1]['data']['content'];
echo "\n--- ML ---\n";
echo $p->content[1]['data']['content_ml'];
