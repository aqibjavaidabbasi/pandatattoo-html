<?php
/**
 * Which Contentful token is broken? Run: php tests/contentful-token-probe.php
 * Prints HTTP codes only — never the token itself.
 */
require __DIR__ . '/../inc/config.php';

$sp   = env('CONTENTFUL_SPACE_ID');
$envn = env('CONTENTFUL_ENVIRONMENT', 'master');
$cma  = env('CONTENTFUL_CMA_TOKEN');
$cda  = env('CONTENTFUL_CDA_TOKEN');

$mask = fn(string $t) => $t === '' ? '(EMPTY)' : substr($t, 0, 4) . '…' . substr($t, -4) . ' (len ' . strlen($t) . ')';
echo "space=$sp environment=$envn\nCMA={$mask($cma)}\nCDA={$mask($cda)}\n\n";

$hit = function (string $url, string $tok): array {
    $ch = curl_init($url);
    curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => 1, CURLOPT_TIMEOUT => 15,
        CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $tok]]);
    $b = curl_exec($ch);
    $c = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    curl_close($ch);
    return [$c, substr((string) $b, 0, 250)];
};

$checks = [
    'CMA token -> api.contentful.com/users/me'     => ["https://api.contentful.com/users/me", $cma],
    "CMA token -> api.contentful.com/spaces/$sp"   => ["https://api.contentful.com/spaces/$sp", $cma],
    'CDA token -> cdn.contentful.com entries'      => ["https://cdn.contentful.com/spaces/$sp/environments/$envn/entries?limit=1", $cda],
];
foreach ($checks as $label => [$url, $tok]) {
    [$code, $body] = $hit($url, $tok);
    echo str_pad($label, 46) . " HTTP $code\n";
    if ($code >= 400) echo "    $body\n";
}
