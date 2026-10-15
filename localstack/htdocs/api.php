<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

// ── CONFIG ─────────────────────────────────────────────
// __DIR__ = the folder this file lives in (your htdocs root).
// To scan a subfolder instead, change BOTH lines together:
//   $root    = __DIR__ . '/projects';
//   $webBase = './projects/';
$root    = __DIR__;
$webBase = './';
// ───────────────────────────────────────────────────────

$entries = @scandir($root);

if ($entries === false) {
    http_response_code(500);
    echo json_encode(['error' => 'Cannot read directory: ' . $root]);
    exit;
}

$projects = [];

foreach ($entries as $entry) {
    // Skip the two special entries and hidden folders (.git, .vscode...)
    if ($entry === '.' || $entry === '..') continue;
    if (strpos($entry, '.') === 0) continue;

    $fullPath = $root . DIRECTORY_SEPARATOR . $entry;

    // This is the filter we talked about: folders only, no files.
    if (!is_dir($fullPath)) continue;

    // Does this project actually have a landing page?
    $hasIndex = file_exists($fullPath . '/index.php')
             || file_exists($fullPath . '/index.html')
             || file_exists($fullPath . '/index.htm');

    $projects[] = [
        'name'     => $entry,
        'url'      => $webBase . rawurlencode($entry) . '/',
        'hasIndex' => $hasIndex,
    ];
}

// Nice, case-insensitive alphabetical order.
usort($projects, fn($a, $b) => strcasecmp($a['name'], $b['name']));

echo json_encode([
    'count'    => count($projects),
    'projects' => $projects,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
