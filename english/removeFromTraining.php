<?php
// removeFromTraining.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$word = $_POST['word'] ?? '';
$pos  = $_POST['partOfSpeech'] ?? '';
$def  = $_POST['definition'] ?? '';

if (!$word || !$pos || !$def) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required fields']);
    exit;
}

$filename = 'training_defs.json';
if (!file_exists($filename)) {
    echo json_encode(['success' => true, 'message' => 'Not in training']);
    exit;
}

$data = json_decode(file_get_contents($filename), true);
if (!is_array($data)) {
    $data = [];
}

$key = "$word|$pos|$def";
$filtered = array_filter($data, function($entry) use ($key) {
    return "$entry[word]|$entry[partOfSpeech]|$entry[definition]" !== $key;
});

file_put_contents($filename, json_encode(array_values($filtered), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

echo json_encode(['success' => true]);
?>