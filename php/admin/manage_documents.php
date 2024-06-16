<?php
session_start();
include '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

if (isset($_POST['upload_file'])) {
    $file_name = $_FILES['file']['name'];
    $file_tmp_name = $_FILES['file']['tmp_name'];
    $file_size = $_FILES['file']['size'];
    $file_type = $_FILES['file']['type'];
    $file_error = $_FILES['file']['error'];

    if ($file_error === 0) {
        $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
        $allowed = array('pdf', 'zip', 'rar', 'xlsx');

        if (in_array($file_ext, $allowed)) {
            $new_file_name = uniqid() . '.' . $file_ext;
            $upload_dir = '../uploads/';

            if (move_uploaded_file($file_tmp_name, $upload_dir . $new_file_name)) {
                $sql = "INSERT INTO documents (file_name, uploaded_at) VALUES ('$new_file_name', NOW())";
                $conn->query($sql);
                header("Location: manage_documents.php");
                exit();
            } else {
                $error_message = "Failed to upload file!";
            }
        } else {
            $error_message = "File type not allowed!";
        }
    } else {
        $error_message = "Error uploading file!";
    }
}

$sql = "SELECT * FROM documents";
$documents = $conn->query($sql);
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Documents</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 50px;
        }
        .table-container {
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center mb-4">Manage Documents</h1>
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger text-center"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data" class="mb-4">
            <div class="mb-3">
                <label for="file" class="form-label">Upload File</label>
                <input type="file" class="form-control" name="file" id="file" required>
            </div>
            <button type="submit" class="btn btn-primary" name="upload_file">Upload File</button>
        </form>
        <div class="table-container">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Student ID</th>
                        <th>File Name</th>
                        <th>Uploaded At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($document = $documents->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $document['id']; ?></td>
                            <td><?php echo $document['student_id']; ?></td>
                            <td><?php echo $document['file_name']; ?></td>
                            <td><?php echo $document['uploaded_at']; ?></td>
                            <td><a href="../uploads/<?php echo $document['file_name']; ?>" class="btn btn-success btn-sm" download>Download</a></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
