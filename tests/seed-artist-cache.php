<?php
/**
 * Seeds the Contentful artist cache with a synthetic payload so the artist-grid rendering
 * path can be exercised without a CDA token. Names and slugs are the ten real artists the
 * live capture shows. Run, then request /gallery.php or /index.php.
 *
 *   php tests/seed-artist-cache.php && curl -s localhost:8123/gallery.php
 *
 * This verifies the rendering, not the API response shape — that still needs a real token.
 */
require __DIR__ . '/../inc/contentful.php';

$names = ['Alex','Ashley','Dani Luz','Edwin','Ilay','Isabela','Nicole','Onyx','Panda','Sophie'];
$artists = [];
foreach ($names as $i => $name) {
    $slug = cf_sanitize_title($name);
    $imgs = [];
    for ($n = 0; $n < 12; $n++) {
        $imgs[] = ['url' => "https://images.ctfassets.net/seed/{$slug}-{$n}.jpg", 'id' => "a{$i}{$n}", 'alt' => $name];
    }
    $artists[] = [
        'id' => cf_artist_map()[$slug] ?? "seed-$slug",
        'name' => $name, 'slug' => $slug, 'bio' => "Bio for $name",
        'instagram_handle' => strtolower(str_replace(' ', '', $name)),
        'profile_picture' => "https://images.ctfassets.net/seed/{$slug}-profile.jpg",
        'portfolio_images' => $imgs,
        'booking_link' => '', 'date' => '', 'personal_website' => '',
    ];
}

// Same args both gallery.php and index.php pass.
cf_cache_set('contentful_artists_' . md5(serialize(['limit' => 50, 'order' => 'fields.artistName'])), $artists);
echo "seeded " . count($artists) . " artists into the Contentful cache\n";
