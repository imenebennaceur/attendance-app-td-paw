<?php
require_once "db_connect.php";
$conn = getConnection();

// Verify ID
if (!isset($_GET["id"])) {
    die("Missing ID");
}

$id = intval($_GET["id"]);

// Load student from DB
$stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
$stmt->execute([$id]);
$stu = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$stu) {
    die("Student not found.");
}

// If form submitted → update
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $ln = $_POST["last_name"];
    $fn = $_POST["first_name"];
    $email = $_POST["email"];

    $stmt = $conn->prepare("UPDATE students SET last_name=?, first_name=?, email=? WHERE id=?");
    $stmt->execute([$ln, $fn, $email, $id]);

    header("Location: db_management.php?updated=1");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Edit Student</title>
<style>
body { background:#FAF4E6; font-family:Arial; padding:40px; }
.card { background:white; padding:25px; border-radius:12px; width:400px; margin:auto; }
input { width:100%; padding:10px; margin-bottom:10px; }
.btn { padding:10px; background:#6A4E23; color:white; border:none; border-radius:8px; width:100%; }
</style>
</head>

<body>

<div class="card">
    <h2>Edit Student</h2>

    <form method="POST">
        <label>Last Name</label>
        <input name="last_name" value="<?= htmlspecialchars($stu['last_name']) ?>">

        <label>First Name</label>
        <input name="first_name" value="<?= htmlspecialchars($stu['first_name']) ?>">

        <label>Email</label>
        <input name="email" value="<?= htmlspecialchars($stu['email']) ?>">

        <button class="btn">Update</button>
    </form>
</div>

</body>
</html>
