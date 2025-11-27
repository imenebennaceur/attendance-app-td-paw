<?php
require_once "db_connect.php";
$conn = getConnection();

$student_id     = $_POST['student_id'];
$session_id     = $_POST['session_id'];
$session_number = $_POST['session_number'];
$present        = $_POST['present'];
$participated   = $_POST['participated'];

// Check if entry exists
$query = $conn->prepare("
    SELECT id FROM attendance_data 
    WHERE student_id = ? AND session_id = ? AND session_number = ?
");
$query->execute([$student_id, $session_id, $session_number]);

if ($query->rowCount() > 0) {

    // Update row
    $update = $conn->prepare("
        UPDATE attendance_data
        SET present = ?, participated = ?
        WHERE student_id = ? AND session_id = ? AND session_number = ?
    ");
    $update->execute([$present, $participated, $student_id, $session_id, $session_number]);

} else {

    // Insert new row
    $insert = $conn->prepare("
        INSERT INTO attendance_data (student_id, session_id, session_number, present, participated)
        VALUES (?, ?, ?, ?, ?)
    ");
    $insert->execute([$student_id, $session_id, $session_number, $present, $participated]);

}

echo "saved";
?>
