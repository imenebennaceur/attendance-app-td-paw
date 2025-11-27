<?php
require_once "db_connect.php";
$conn = getConnection();

if (!$conn) {
    die("DB connection error");
}

$id    = $_POST["student_id"];
$ln    = $_POST["last_name"];
$fn    = $_POST["first_name"];
$email = $_POST["email"];

$sql = "INSERT INTO students (id, last_name, first_name, email)
        VALUES (:id, :ln, :fn, :email)";

$stmt = $conn->prepare($sql);

$stmt->execute([
    ":id"    => $id,
    ":ln"    => $ln,
    ":fn"    => $fn,
    ":email" => $email
]);

header("Location: db_management.php?added=1");
exit;
?>
