<?php
require_once "db_connect.php";

$conn = getConnection();
if (!$conn) {
    echo "DB error";
    exit;
}

$session_id = $_POST['session_id'] ?? 0;

if (!$session_id) {
    echo "Invalid session ID";
    exit;
}

$sql = "UPDATE attendance_sessions SET status='closed' WHERE id=:id";

$stmt = $conn->prepare($sql);
$stmt->execute([":id" => $session_id]);

echo "Session closed successfully";
?>