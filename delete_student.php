<?php
require_once "db_connect.php";
$conn = getConnection();

if (!$conn) die("DB error");

if (!isset($_GET['id'])) {
    die("Missing student ID");
}

$id = intval($_GET['id']);

$stmt = $conn->prepare("DELETE FROM students WHERE id = :id");
$stmt->execute([":id" => $id]);

header("Location: db_management.php?deleted=1");
exit;
?>
