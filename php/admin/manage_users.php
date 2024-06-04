<?php
session_start();
include '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$sql = "SELECT * FROM users";
$users = $conn->query($sql);

if (isset($_GET['delete_user_id'])) {
    $user_id = $_GET['delete_user_id'];

    $sql = "DELETE FROM users WHERE id = '$user_id'";

    if ($conn->query($sql) === TRUE) {
        $success_message = "User berhasil dihapus";
    } else {
        $error_message = "Error: " . $sql . "<br>" . $conn->error;
    }
}

if (isset($_POST['update_user_id'])) {
    $user_id = $_POST['update_user_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    $sql = "UPDATE users SET name = '$name', email = '$email', role = '$role' WHERE id = '$user_id'";

    if ($conn->query($sql) === TRUE) {
        $success_message = "User berhasil diupdate";
    } else {
        $error_message = "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manage Users</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
</head>
<body class="bg-light">
    <div class="container mt-5 border rounded bg-white py-4 px-5 mb-5">
        <header class="header-title mb-4">
            <h1><span class="fw-normal text-dark">Manage</span> <span class="text-primary">Users</span></h1>
            <hr>
        </header>
        <section>
            <h2 class="text-center">User Data</h2>
            <?php if (isset($success_message)): ?>
                <div class="alert alert-success"><?php echo $success_message; ?></div>
            <?php elseif (isset($error_message)): ?>
                <div class="alert alert-danger"><?php echo $error_message; ?></div>
            <?php endif; ?>
            <div class="table-responsive">
                <table class="table table-striped mt-4">
                    <thead>
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Name</th>
                            <th scope="col">Email</th>
                            <th scope="col">Role</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($user = $users->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $user['id']; ?></td>
                                <td><?php echo $user['name']; ?></td>
                                <td><?php echo $user['email']; ?></td>
                                <td>
                                    <?php
                                    if ($user['role'] == 'admin') {
                                        echo '<span class="badge bg-success">Admin</span>';
                                    } else {
                                        echo '<span class="badge bg-secondary">User</span>';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <a href="?delete_user_id=<?php echo $user['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>

                                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#updateModal<?php echo $user['id']; ?>">Update</button>

                                    <div class="modal fade" id="updateModal<?php echo $user['id']; ?>" tabindex="-1" aria-labelledby="updateModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="updateModalLabel">Update User</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                                                    <div class="modal-body">
                                                        <input type="hidden" name="update_user_id" value="<?php echo $user['id']; ?>">
                                                        <div class="mb-3">
                                                            <label for="name" class="form-label">Name</label>
                                                            <input type="text" class="form-control" id="name" name="name" value="<?php echo $user['name']; ?>" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="email" class="form-label">Email</label>
                                                            <input type="email" class="form-control" id="email" name="email" value="<?php echo $user['email']; ?>" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="role" class="form-label">Role</label>
                                                            <select class="form-select" id="role" name="role" required>
                                                                <option value="admin" <?php if ($user['role'] == 'admin') echo 'selected'; ?>>Admin</option>
                                                                <option value="user" <?php if ($user['role'] == 'user') echo 'selected'; ?>>User</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-primary">Update</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>