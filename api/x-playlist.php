<?php
// The original M3U playlist URL
$playlist_url = 'http://xtv.ooo:8080/get.php?username=kamran800&password=800kamran&type=m3u&output=mpegts';
$serverAddress = $_SERVER['HTTP_HOST'] ?? 'default.server.address';

// Fetch the playlist content
$playlist_content = file_get_contents($playlist_url);

if ($playlist_content === false) {
    die('Error fetching playlist.');
}

// Replace URLs in the playlist
$modified_content = str_replace(
    "http://xtv.ooo:8080/movie/kamran800/800kamran/", 
    "https://$serverAddress/x-play?id=", 
    $playlist_content
);

// Remove both .mkv, .mp4, and other entries along with the previous #EXTINF line
$modified_content = preg_replace('/#EXTINF:[^\r\n]*\r?\n[^\r\n]+\.(mkv|mp4|avi|flv|webp|webm|divx|ts)\r?\n/', '', $modified_content);

// Remove empty lines that might be left after removing entries
$modified_content = preg_replace('/^\s*[\r\n]+/m', '', $modified_content);

// Set appropriate headers for M3U playlist
header('Content-Type: audio/mpegurl');
header('Content-Disposition: attachment; filename="playlist.m3u"');

// Output the modified playlist
echo $modified_content;
?>
