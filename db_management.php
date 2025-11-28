<?php
require_once "db_connect.php";
$conn = getConnection();

// Load students
$students = $conn->query("SELECT * FROM students ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

// Load sessions
$sessions = $conn->query("SELECT * FROM attendance_sessions ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
<title>Database Management</title>
<style>
body {
    background: #FBF1D6;
    font-family: Arial;
    padding: 40px;
}
.card {
    background: #FFFDF7;
    padding: 25px;
    border-radius: 16px;
    border: 1px solid #E7DFCC;
    box-shadow: 0 6px 15px rgba(0,0,0,0.08);
    margin-bottom: 30px;
}
h2 {
    color: #3F2F03;
    margin-bottom: 15px;
}
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
}
th {
    background: #E8DCC0;
    padding: 10px;
    font-weight: bold;
}
td {
    padding: 10px;
    border-bottom: 1px solid #E7DFCC;
}
.btn {
    padding: 6px 12px;
    background: #3F2F03;
    color: white;
    border-radius: 8px;
    text-decoration: none;
    border: none;
    cursor: pointer;
}
.btn:hover {
    opacity: 0.85;
}
.delete {
    background: #8B3A3A;
}
.input {
    padding: 6px;
    border: 1px solid #C0B292;
    border-radius: 6px;
    background: white;
}
.form-row {
    margin-bottom: 12px;
}
</style>
</head>
<body>

<a href="index.html" class="back-btn">← Back to Dashboard</a>

<style>
.back-btn {
    display: inline-block;
    margin-bottom: 20px;
    padding: 10px 16px;
    background: #3F2F03;
    color: white;
    border-radius: 10px;
    text-decoration: none;
    font-weight: bold;
}
.back-btn:hover {
    opacity: 0.85;
}
</style>

<!-- ================== STUDENTS PANEL ================== -->
<div class="card">
    <h2>Students Management</h2>


    <table>
        <tr>
            <th>ID</th>
            <th>Last Name</th>
            <th>First Name</th>
            <th>Email</th>
            <th>Actions</th>
        </tr>

        <?php if(count($students) == 0): ?>
            <tr><td colspan="5" style="text-align:center;">No students found</td></tr>
        <?php endif; ?>

        <?php foreach($students as $stu): ?>
        <tr>
            <td><?= $stu["id"] ?></td>
            <td><?= $stu["last_name"] ?></td>
            <td><?= $stu["first_name"] ?></td>
            <td><?= $stu["email"] ?></td>
            <td>
                <a class="btn" href="update_student.php?id=<?= $stu["id"] ?>">Edit</a>
                <a class="btn delete" href="delete_student.php?id=<?= $stu["id"] ?>">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>

<!-- ================== SESSIONS PANEL ================== -->
<div class="card">
    <h2>Attendance Sessions</h2>

    <!-- CREATE SESSION FORM -->
    <form action="create_session.php" method="POST">
        <div class="form-row">
            Course:
            <input class="input" name="course_id" required>
        </div>

        <div class="form-row">
            Group:
            <input class="input" name="group_id" required>
        </div>

        <div class="form-row">
            Professor ID:
            <input class="input" name="opened_by" required>
        </div>

        <button class="btn">Create Session</button>
    </form>

    <br><br>

    <table>
        <tr>
            <th>ID</th>
            <th>Course</th>
            <th>Group</th>
            <th>Date</th>
            <th>Opened By</th>
            <th>Status</th>
            <th>Action</th>
        </tr>

        <?php if(count($sessions) == 0): ?>
            <tr><td colspan="7" style="text-align:center;">No sessions found</td></tr>
        <?php endif; ?>

        <?php foreach($sessions as $ses): ?>
        <tr>
            <td><?= $ses["id"] ?></td>
            <td><?= $ses["course_id"] ?></td>
            <td><?= $ses["group_id"] ?></td>
            <td><?= $ses["date"] ?></td>
            <td><?= $ses["opened_by"] ?></td>
            <td><?= $ses["status"] ?></td>
            <td>
                <?php if($ses["status"] == "open"): ?>
                <form action="close_session.php" method="POST">
                    <input type="hidden" name="session_id" value="<?= $ses["id"] ?>">
                    <button class="btn delete">Close</button>
                </form>
                <?php else: ?>
                    Closed
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>

</body>
</html>
