<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$p = App\Models\Page::where('slug', 'about')->first();

$en = $p->content[1]['data']['content'];
$ml = $p->content[1]['data']['content_ml'];

$en = preg_replace('/<span style="color: #[0-9a-fA-F]{6};">(.*?)<\/span>/', '$1', $en);
$ml = preg_replace('/<span style="color: #[0-9a-fA-F]{6};">(.*?)<\/span>/', '$1', $ml);

$content = $p->content;
$content[1]['data']['content'] = $en;
$content[1]['data']['content_ml'] = $ml;
$p->content = $content;
$p->save();

echo "Stripped colors!\n";
