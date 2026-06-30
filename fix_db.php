<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$p = App\Models\Page::where('slug', 'about')->first();

// The text is completely messed up by copy-pasting from the frontend.
// Let's reset both to clean HTML with red color.

$en = '<p>The <span style="color: #ff0000;"><strong><em>Kollam District Police Department Employees\' Co-operative Society Ltd. No. Q.1179</em></strong></span><em> </em>was registered on 04-06-1994 and commenced its operations on 29-07-1994. Having successfully completed <span style="color: #ff0000;"><strong>25 years</strong></span> of dedicated service, the Society proudly reflects on its remarkable journey.</p><p>We gratefully remember and express our sincere appreciation to all those who played a vital role in the establishment and growth of our Society. Overcoming numerous challenges and difficulties, and with the wholehearted support of our members, the Society has achieved Class-I status.</p><p></p><p>While we take pride in this achievement, we recognize that there is still much more to accomplish. We sincerely seek the continued cooperation, trust, and wholehearted support of all our members as we strive towards greater progress and excellence in the years ahead.</p>';

$ml = '<p>04 -06 -1994 -ൽ രജിസ്റ്റർ ചെയ്ത് 29 -07 -94 ൽ പ്രവർത്തനം ആരംഭിച്ച <span style="color: #ff0000;"><strong><em>കൊല്ലം ജില്ലാ പോലീസ് ഡിപ്പാർട്ടമെന്റ് എംപ്ലോയീസ് സഹകരണസംഘം ക്ലിപ്തം നമ്പർ ക്യു.1179</em></strong></span> ,25 വർഷം പൂർത്തീകരിച്ചിരിക്കുന്നു. <span style="color: #ff0000;"><strong>25 വർഷം</strong></span> പൂർത്തിയാക്കിയ നമ്മുടെ സംഘത്തിന്റെ രൂപീകരണത്തിനും വളർച്ചക്കും നേതൃത്വം നൽകിയ ഏവരെയും നന്ദിയോടെ സ്മരിക്കുന്നു . പ്രതിസന്ധികളേയും വെല്ലുവിളികളെയും അതിജീവിച്ചു സഹകാരികളുടെ അകമഴിഞ്ഞ പിന്തുണ കൊണ്ട് സംഘം ക്ലാസ്-1 പദവിയിൽ എത്തിനിൽക്കുന്നു . ഇനിയും നാം കൂടുതൽ മുന്നേറേണ്ടതായിട്ടുണ്ട് . അതിനു എല്ലാ സഹകാരികളുടെയും ആത്മാര്ത്ഥമായ പിന്തുണ അഭ്യർത്ഥിക്കുന്നു.</p>';

$content = $p->content;
$content[1]['data']['content'] = $en;
$content[1]['data']['content_ml'] = $ml;
$p->content = $content;
$p->save();
echo "Fixed DB!\n";
