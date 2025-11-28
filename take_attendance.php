<?php
// Set today's date
$today = date("Y-m-d");
$attendance_file = "attendance_" . $today . ".json";


// ===============================
// 1. If form is submitted
// ===============================
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // If today's attendance file already exists → STOP
    if (file_exists($attendance_file)) {
        echo "<h2 style='color:red'>Attendance for today has already been taken.</h2>";
        echo "<a href='take_attendance.php'>Go Back</a>";
        exit;
    }

    // Build attendance array
    $attendance = [];

    foreach ($_POST['status'] as $student_id => $status) {
        $attendance[] = [
            "student_id" => $student_id,
            "status" => $status
        ];
    }

    // Save file
    file_put_contents($attendance_file, json_encode($attendance, JSON_PRETTY_PRINT));

    echo "<h2>Attendance saved successfully for $today!</h2>";
    echo "<a href='take_attendance.php'>Take Attendance Again</a>";
    exit;
}



// ===============================
// 2. Load students from students.json
// ===============================

$students_file = "students.json";

if (!file_exists($students_file)) {
    die("<h2>No students found. Add students first.</h2>");
}

$students_json = file_get_contents($students_file);
$students = json_decode($students_json, true);

if (!is_array($students)) {
    die("<h2>Error: students.json is corrupted.</h2>");
}


// ===============================
// 3. If attendance for today already exists → show message
// ===============================
if (file_exists($attendance_file)) {
    echo "<h2 style='color:red'>Attendance for today has already been taken.</h2>";
    echo "<a href='attendance_$today.json' target='_blank'>View Today’s Attendance</a>";
    exit;
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Take Attendance</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f7f1de;
            padding: 20px;
        }
        h1 {
            color: #4b3a1f;
        }
        table {
            width: 60%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 12px;
            border-bottom: 1px solid #e0d6c2;
            text-align: center;
        }
        th {
            background: #d7c5a5;
            color: black;
        }
        button {
            margin-top: 20px;
            padding: 12px 20px;
            background: #6b4f2f;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }
        button:hover {
            background: #8a693c;
        }
    </style>
</head>
<body>

<h1>Take Attendance – <?php echo $today; ?></h1>

<form method="POST">

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Last Name</th>
            <th>First Name</th>
            <th>Present</th>
            <th>Absent</th>
        </tr>
    </thead>

    <tbody>
        <?php foreach ($students as $s): ?>
            <tr>
                <td><?php echo $s['student_id']; ?></td>
                <td><?php echo $s['last_name']; ?></td>
                <td><?php echo $s['first_name']; ?></td>

                <td><input type="radio" name="status[<?php echo $s['student_id']; ?>]" value="present" required></td>
                <td><input type="radio" name="status[<?php echo $s['student_id']; ?>]" value="absent" required></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<button type="submit">Save Attendance</button>

</form>

</body>
</html>