<?php
// unknownDef.php
header('Content-Type: application/json');

$file = 'unknown_defs.json';
if (!file_exists($file)) {
    echo json_encode([]);
    exit;
}

$data = json_decode(file_get_contents($file), true);
echo json_encode($data ?: []);
?>