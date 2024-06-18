<?php
session_start();
include 'php/config.php';

// Mengecek apakah pengguna sudah login
if (isset($_SESSION['user_id'])) {
    // Arahkan pengguna ke dashboard yang sesuai berdasarkan peran
    switch ($_SESSION['user_role']) {
        case 'admin':
            header("Location: admin_dashboard.php");
            break;
        case 'teacher':
            header("Location: dosen_dashboard.php");
            break;
        case 'kaprodi':
            header("Location: kaprodi_dashboard.php");
            break;
        case 'student':
            header("Location: profile.php");
            break;
    }
    exit();
}

// Memproses login jika form dikirim
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Query untuk mendapatkan data pengguna berdasarkan email
    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = $conn->query($sql);

    // Mengecek apakah pengguna ditemukan
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verifikasi password
        if (password_verify($password, $user['password'])) {
            // Set session dengan informasi pengguna
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_role'] = $user['role'];

            // Arahkan pengguna ke dashboard yang sesuai berdasarkan peran
            switch ($user['role']) {
                case 'admin':
                    header("Location: admin_dashboard.php");
                    break;
                case 'teacher':
                    header("Location: dosen_dashboard.php");
                    break;
                case 'kaprodi':
                    header("Location: kaprodi_dashboard.php");
                    break;
                case 'student':
                    header("Location: profile.php");
                    break;
            }
            exit();
        } else {
            $error = "Password salah";
        }
    } else {
        $error = "Tidak ada pengguna dengan email tersebut";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
    <div class="container login">
        <h1>Login</h1>
        <form method="POST" action="">
            Email: <input type="email" name="email" required><br>
            Password: <input type="password" name="password" required><br>
            <input type="submit" value="Login">
        </form>
        <?php
        if (isset($error)) {
            echo "<p class='error'>$error</p>";
        }
        ?>
    </div>
</body>
</html>
