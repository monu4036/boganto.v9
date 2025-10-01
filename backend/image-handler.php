<?php
function getMimeType($filePath) {
    $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
    $mimeTypes = [
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'gif' => 'image/gif',
        'webp' => 'image/webp'
    ];
    return $mimeTypes[$extension] ?? 'application/octet-stream';
}

function serveImage($filePath) {
    if (!file_exists($filePath)) {
        http_response_code(404);
        exit('File not found');
    }

    $mimeType = getMimeType($filePath);
    $fileSize = filesize($filePath);
    $etag = md5_file($filePath);

    // Check if browser has valid cache
    if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && trim($_SERVER['HTTP_IF_NONE_MATCH']) === $etag) {
        http_response_code(304);
        exit();
    }

    // Set headers
    header("Content-Type: $mimeType");
    header("Content-Length: $fileSize");
    header("Cache-Control: public, max-age=31536000, immutable");
    header("ETag: $etag");
    header("Access-Control-Allow-Origin: http://localhost:5173");
    header("Cross-Origin-Resource-Policy: cross-origin");

    // Output file in chunks to prevent memory issues
    $handle = fopen($filePath, 'rb');
    while (!feof($handle)) {
        echo fread($handle, 8192);
        flush();
    }
    fclose($handle);
    exit();
}
?>