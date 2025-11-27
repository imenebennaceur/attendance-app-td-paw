<?php
require_once "db_connect.php";

$conn = getConnection();
if (!$conn) die("Database connection failed.");

$sql = "SELECT * FROM students ORDER BY id DESC";
$stmt = $conn->query($sql);
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Students List</h2>

<table border="1" cellpadding="10">
<tr>
    <th>ID</th>
    <th>Full Name</th>
    <th>Matricule</th>
    <th>Group</th>
    <th>Actions</th>
</tr>

<?php foreach ($students as $s): ?>
<tr>
    <td><?= $s['id'] ?></td>
    <td><?= $s['fullname'] ?></td>
    <td><?= $s['matricule'] ?></td>
    <td><?= $s['group_id'] ?></td>
    <td>
        <a href="update_student.php?id=<?= $s['id'] ?>">Edit</a> |
        <a href="delete_student.php?id=<?= $s['id'] ?>" onclick="return confirm('Delete?')">Delete</a>
    </td>
</tr>
<?php endforeach; ?>

</table>
