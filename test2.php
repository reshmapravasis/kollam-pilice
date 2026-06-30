<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$p = App\Models\Page::where('slug', 'about')->first();
file_put_contents('/tmp/ml.txt', $p->content[1]['data']['content_ml']);
