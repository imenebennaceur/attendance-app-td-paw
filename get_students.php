<?php
header("Content-Type: application/json");

$file = "students.json";

if (!file_exists($file)) {
    echo json_encode([]);
    exit;
}

$json = file_get_contents($file);
$data = json_decode($json, true);

echo json_encode($data);
?>
