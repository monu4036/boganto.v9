<?php
// Clear any existing output buffers
while (ob_get_level()) ob_end_clean();

// Get image path from URL
$image = isset($_GET['path']) ? $_GET['path'] : null;
if (!$image) {
    header("HTTP/1.0 404 Not Found");
    exit('Image not specified');
}

// Secure the path
$image = str_replace('..', '', $image);
$filePath = __DIR__ . '/../uploads/' . basename($image);

if (!file_exists($filePath)) {
    header("HTTP/1.0 404 Not Found");
    exit('Image not found');
}

// Set basic headers
header('Content-Type: ' . mime_content_type($filePath));
header('Access-Control-Allow-Origin: http://localhost:5173');

// Stream file
$fp = fopen($filePath, 'rb');
fpassthru($fp);
fclose($fp);
exit();