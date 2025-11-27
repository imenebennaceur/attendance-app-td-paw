<?php
require_once "db_connect.php";

$conn = getConnection();
if (!$conn) exit;

$sessions = $conn->query("SELECT * FROM attendance_sessions ORDER BY id DESC");

foreach ($sessions as $s) {
    echo "<tr>
            <td>{$s['id']}</td>
            <td>{$s['course_id']}</td>
            <td>{$s['group_id']}</td>
            <td>{$s['date']}</td>
            <td>{$s['opened_by']}</td>
            <td>{$s['status']}</td>
            <td>";

    if ($s['status'] == 'open') {
        echo "<button class='closeSessionBtn' data-id='{$s['id']}'>Close</button>";
    } else {
        echo "—";
    }

    echo "</td></tr>";
}
?>
