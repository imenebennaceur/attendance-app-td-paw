<?php
require_once "db_connect.php";

$conn = getConnection();
header("Content-Type: application/json");

if (!$conn) {
    echo json_encode(["status" => "error", "msg" => "DB connection failed"]);
    exit;
}

$id     = $_POST["student_id"] ?? null;
$ln     = $_POST["last_name"] ?? null;
$fn     = $_POST["first_name"] ?? null;
$email  = $_POST["email"] ?? null;

if (!$id || !$ln || !$fn || !$email) {
    echo json_encode(["status" => "error", "msg" => "Missing fields"]);
    exit;
}

try {
    $sql = "INSERT INTO students (id, last_name, first_name, email)
            VALUES (:id, :ln, :fn, :email)";

    $stmt = $conn->prepare($sql);

    $stmt->execute([
        ":id"    => $id,
        ":ln"    => $ln,
        ":fn"    => $fn,
        ":email" => $email
    ]);

    echo json_encode(["status" => "success", "msg" => "Student added"]);

} catch (Exception $e) {
    echo json_encode(["status" => "error", "msg" => $e->getMessage()]);
}
?>
