<?php
session_start();
include 'php/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id='$user_id'";
$result = $conn->query($sql);
$user = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];

    $sql = "UPDATE users SET name='$name', email='$email' WHERE id='$user_id'";

    if ($conn->query($sql) === TRUE) {
        echo "<p style='color: white;'>Record updated successfully</p>";
    } else {
        echo "Error updating record: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Profile</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
    <div class="container profile">
        <h1>Profile</h1>
        <form method="POST" action="">
            Name: <input type="text" name="name" value="<?php echo $user['name']; ?>"><br>
            Email: <input type="email" name="email" value="<?php echo $user['email']; ?>"><br>
            <input type="submit" value="Update">
        </form>
        <a href="schedule.php">View Schedule</a>
        <a href="map.php">Check Map</a>
        <a href="logout.php">Logout</a>
    </div>
</body>
</html>

