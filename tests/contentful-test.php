<?php
require __DIR__ . "/../inc/contentful.php";
$fail = 0;
function ok($cond, $label) { global $fail; if (!$cond) { $fail++; echo "FAIL: $label\n"; } }

// Slugs must match the keys in cf_artist_map() exactly — a booking submission
// resolves the Contentful entry ID through this, so a drift here breaks bookings.
$cases = ['Dani Luz'=>'dani-luz','Ashley'=>'ashley','Alex'=>'alex','Panda'=>'panda',
          'Onyx'=>'onyx','Ilay'=>'ilay','Edwin'=>'edwin','Sophie'=>'sophie',
          'Nicole'=>'nicole','Isabela'=>'isabela'];
foreach ($cases as $name => $want) ok(cf_sanitize_title($name) === $want, "sanitize_title('$name') => '$want', got '".cf_sanitize_title($name)."'");

$map = cf_artist_map();
foreach ($cases as $name => $slug) ok(isset($map[$slug]), "artist map has slug '$slug'");
ok(count($map) === 10, 'artist map has 10 entries, got ' . count($map));

// edge cases
ok(cf_sanitize_title('  Mixed  CASE Name ') === 'mixed-case-name', 'collapses whitespace + lowercases');
ok(cf_sanitize_title("O'Brien & Co.") === 'obrien-co', "strips punctuation, got '".cf_sanitize_title("O'Brien & Co.")."'");
ok(cf_sanitize_title('') === '', 'empty stays empty');

// asset URL resolution, incl. the protocol-relative case Contentful returns
$assets = [['sys'=>['id'=>'a1'],'fields'=>['file'=>['url'=>'//images.ctfassets.net/x/y.jpg']]],
           ['sys'=>['id'=>'a2'],'fields'=>['file'=>['url'=>'https://images.ctfassets.net/x/z.jpg']]]];
ok(cf_asset_url('a1',$assets) === 'https://images.ctfassets.net/x/y.jpg', 'prefixes https: on //url');
ok(cf_asset_url('a2',$assets) === 'https://images.ctfassets.net/x/z.jpg', 'leaves absolute url alone');
ok(cf_asset_url('missing',$assets) === '', 'unknown asset id => empty string');
ok(cf_asset_url('a1',[]) === '', 'no assets => empty string');

echo $fail === 0 ? "contentful.php: all checks passed\n" : "contentful.php: $fail FAILED\n";
exit($fail === 0 ? 0 : 1);
