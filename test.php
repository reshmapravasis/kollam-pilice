<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$p = App\Models\Page::where('slug', 'about')->first();
echo json_encode([
    'en' => $p->content[1]['data']['content'],
    'ml' => $p->content[1]['data']['content_ml']
], JSON_PRETTY_PRINT);
