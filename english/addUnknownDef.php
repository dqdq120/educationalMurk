<?php
// addUnknownDef.php
header('Content-Type: application/json');

$word = $_POST['word'] ?? '';
$pos = $_POST['partOfSpeech'] ?? '';
$def = $_POST['definition'] ?? '';
$example = $_POST['example'] ?? null;

if (!$word || !$def || !$pos) {
    echo json_encode(['status' => 'error', 'msg' => 'Missing data']);
    exit;
}

$file = 'unknown_defs.json';
$defs = file_exists($file) ? json_decode(file_get_contents($file), true) : [];

// Avoid duplicates
foreach ($defs as $d) {
    if ($d['word'] === $word && $d['definition'] === $def && $d['partOfSpeech'] === $pos) {
        echo json_encode(['status' => 'exists']);
        exit;
    }
}

$defs[] = [
    'word' => $word,
    'partOfSpeech' => $pos,
    'definition' => $def,
    'example' => $example,
    'timestamp' => date('c')
];

file_put_contents($file, json_encode($defs, JSON_PRETTY_PRINT));
echo json_encode(['status' => 'saved']);
?>