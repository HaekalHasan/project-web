<?php
session_start();
include '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../login.html");
    exit();
}

$sql = "SELECT * FROM documents";
$documents = $conn->query($sql);
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Documents</title>
</head>
<body>
    <h1>Manage Documents</h1>
    <table>
        <tr>
            <th>ID</th>
            <th>Student ID</th>
            <th>File Name</th>
            <th>Action</th>
        </tr>
        <?php while($document = $documents->fetch_assoc()): ?>
            <tr>
                <td><?php echo $document['id']; ?></td>
                <td><?php echo $document['student_id']; ?></td>
                <td><?php echo $document['file_name']; ?></td>
                <td><a href="../uploads/<?php echo $document['file_name']; ?>" download>Download</a></td>
            </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
