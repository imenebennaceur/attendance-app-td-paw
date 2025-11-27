<?php
require_once "db_connect.php";
$conn = getConnection();

$session_id = $_GET['session_id'];

$stmt = $conn->prepare("
    SELECT student_id, session_number, present, participated
    FROM attendance_data
    WHERE session_id = ?
");
$stmt->execute([$session_id]);

echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
?>
