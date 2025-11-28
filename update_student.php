?php
require_once "db_connect.php";
$conn = getConnection();

if (!isset($_GET['id'])) die("Missing ID");

$id = intval($_GET['id']);

// Load student
$stmt = $conn->prepare("SELECT * FROM students WHERE id = :id");
$stmt->execute([":id" => $id]);
$stu = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$stu) die("Student not found");

// If form submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $ln    = $_POST['last_name'];
    $fn    = $_POST['first_name'];
    $email = $_POST['email'];

    $update = $conn->prepare("
        UPDATE students
        SET last_name = :ln, first_name = :fn, email = :email
        WHERE id = :id
    ");

    $update->execute([
        ":ln" => $ln,
        ":fn" => $fn,
        ":email" => $email,
        ":id" => $id
    ]);

    header("Location: db_management.php?updated=1");
    exit;
}

?>

<!DOCTYPE html>
<html>
<head>
<title>Edit Student</title>
<style>
body { font-family:Arial; background:#FBF1D6; padding:40px; }
.card { background:#FFFDF7; padding:30px; width:400px; border-radius:14px; margin:auto; }
label { font-weight:bold; color:#3F2F03; }
input { width:100%; padding:10px; margin:8px 0; border-radius:8px; border:1px solid #CDBF9C; }
.btn { padding:10px 16px; background:#3F2F03; color:white; border:none; border-radius:8px; cursor:pointer; }
</style>
</head>
<body>

<div class="card">
    <h2>Edit Student</h2>

    <form method="POST">

        <label>Last Name</label>
        <input name="last_name" value="<?= $stu['last_name'] ?>" required>

        <label>First Name</label>
        <input name="first_name" value="<?= $stu['first_name'] ?>" required>

        <label>Email</label>
        <input name="email" value="<?= $stu['email'] ?>" required>

        <button class="btn">Save Changes</button>

    </form>
</div>

</body>
</html>