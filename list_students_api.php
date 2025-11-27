<?php
require_once "db_connect.php";
$conn = getConnection();

$students = $conn->query("SELECT * FROM students")->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($students);
?>

