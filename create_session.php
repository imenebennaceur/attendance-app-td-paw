<?php
require_once "db_connect.php";

$conn = getConnection();
if (!$conn) {
    echo json_encode(["error" => "DB connection failed"]);
    exit;
}

$course   = $_POST['course_id'] ?? '';
$group    = $_POST['group_id'] ?? '';
$prof     = $_POST['opened_by'] ?? '';
$date     = date("Y-m-d"); // Today

if ($course == "" || $group == "" || $prof == "") {
    echo json_encode(["error" => "Missing required fields"]);
    exit;
}

$sql = "INSERT INTO attendance_sessions (course_id, group_id, date, opened_by, status)
        VALUES (:course, :grp, :date, :prof, 'open')";

$stmt = $conn->prepare($sql);

$stmt->execute([
    ':course' => $course,
    ':grp'    => $group,
    ':date'   => $date,
    ':prof'   => $prof
]);

$sessionId = $conn->lastInsertId();

echo json_encode([
    "success" => true,
    "session_id" => $sessionId
]);
?>
