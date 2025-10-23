<?php
// removeUnknownDef.php
header('Content-Type: application/json');

$word = $_POST['word'] ?? '';
$pos = $_POST['partOfSpeech'] ?? '';
$def = $_POST['definition'] ?? '';

if (!$word || !$def || !$pos) {
    echo json_encode(['status' => 'error']);
    exit;
}

$file = 'unknown_defs.json';
if (!file_exists($file)) {
    echo json_encode(['status' => 'not_found']);
    exit;
}

$defs = json_decode(file_get_contents($file), true);
$filtered = array_filter($defs, function($d) use ($word, $pos, $def) {
    return !($d['word'] === $word && $d['definition'] === $def && $d['partOfSpeech'] === $pos);
});

file_put_contents($file, json_encode(array_values($filtered), JSON_PRETTY_PRINT));
echo json_encode(['status' => 'removed']);
?>