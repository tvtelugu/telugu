<?php
$serverAddress = $_SERVER['HTTP_HOST'] ?? 'default.server.address';

// The original M3U playlist URL
$playlist_url = "https://$serverAddress/kitkkkT77pwe87uujethha3aUUcVVD9ak.m3u";

// Fetch the playlist content
$playlist_content = file_get_contents($playlist_url);

if ($playlist_content === false) {
    die('Error fetching playlist.');
}

// Replace URLs in the playlist
$modified_content = str_replace(
    "http://ktv.im:8080/q3TrJFbKyq/awTvJxk2ct/", 
    "https://$serverAddress/k-play?id=", 
    $playlist_content
);

// Remove both .mkv, .mp4, and other entries along with the previous #EXTINF line
$modified_content = preg_replace('/#EXTINF:[^\r\n]*\r?\n[^\r\n]+\.(mkv|mp4|avi|flv|webp|webm|divx|ts)\r?\n/', '', $modified_content);

// Remove empty lines that might be left after removing entries
$modified_content = preg_replace('/^\s*[\r\n]+/m', '', $modified_content);

// Define the allowed group names
$allowed_groups = [
    'AS | IN | MALAYALAM',
    'AS | IN | TAMIL',
    'AS | IN | TELEGU',
    'AS | IN | PUNJAB',
    'AS | IN | KANNADA',
    'AS | IN | GUJRATI',
    'AS | IN | MARATHI',
    'AS | IN | ORIYA',
    'AS | IN | ASSAM',
    'IN | PANJABI MOVIES',
    'AS | INDIA',
    'AS | INDIA ENTERTAINMENT',
    'AS | INDIAN SPORTS',
    'AS | INDIA KIDS',
    'AS | INDIA DOUCMENTARY',
    'AS | INDIA ENGLISH MOVIES',
    'AS | INDIA HINDI MOVIES',
    'AS | INDIA MUSIC',
    'AS | INDIA 4K',
    'AS | INDIAN NEWS',
];

// Filter out entries that do not match the allowed groups
$filtered_content = '';
$lines = explode("\n", $modified_content);
$include_entry = false;

foreach ($lines as $line) {
    // Check for group name in the EXTINF line
    if (strpos($line, '#EXTINF') !== false) {
        // Reset flag
        $include_entry = false;
        
        // Check if the line contains one of the allowed group names
        foreach ($allowed_groups as $group) {
            if (strpos($line, $group) !== false) {
                $include_entry = true;
                break;
            }
        }
    }

    // If the entry should be included, add both the EXTINF and the URL lines
    if ($include_entry || trim($line) === '') {
        $filtered_content .= $line . "\n";
    }
}

// Set appropriate headers for M3U playlist
header('Content-Type: audio/mpegurl');
header('Content-Disposition: attachment; filename="playlist.m3u"');

// Output the filtered playlist
echo $filtered_content;
?>
