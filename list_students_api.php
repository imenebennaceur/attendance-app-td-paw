<?php
require_once "db_connect.php";

$conn = getConnection();

header("Content-Type: application/json");

try {
    $stmt = $conn->query("SELECT * FROM students ORDER BY id ASC");
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($students);

} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>
