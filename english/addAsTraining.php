<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$input = $_POST;
$timestamp = $input['timestamp'] ?? date('c');

$data = [
    'word' => $input['word'] ?? '',
    'partOfSpeech' => $input['partOfSpeech'] ?? '',
    'definition' => $input['definition'] ?? '',
    'example' => $input['example'] ?? null,
    'timestamp' => $timestamp
];

// Load existing JSON or start empty
$filename = 'training_defs.json';
$jsonData = file_exists($filename) ? json_decode(file_get_contents($filename), true) : [];

// Append new entry
$jsonData[] = $data;

// Write back
if (file_put_contents($filename, json_encode($jsonData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))) {
    echo json_encode(['success' => true]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to save']);
}
?>