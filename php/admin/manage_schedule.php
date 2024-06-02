<?php
session_start();
include '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../login.html");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $date_time = $_POST['date_time'];
    $capacity = $_POST['capacity'];
    $duration = $_POST['duration'];
    $room = $_POST['room'];

    $sql = "INSERT INTO schedules (date_time, capacity, duration, room) VALUES ('$date_time', '$capacity', '$duration', '$room')";

    if ($conn->query($sql) === TRUE) {
        echo "Schedule added successfully!";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$sql = "SELECT * FROM schedules";
$schedules = $conn->query($sql);
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Schedule</title>
</head>
<body>
    <h1>Manage Schedule</h1>
    <form method="POST" action="">
        Date and Time: <input type="text" name="date_time"><br>
        Capacity: <input type="number" name="capacity"><br>
        Duration: <input type="text" name="duration"><br>
        Room: <input type="text" name="room"><br>
        <input type="submit" value="Add Schedule">
    </form>

    <h2>Existing Schedules</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Date and Time</th>
            <th>Capacity</th>
            <th>Duration</th>
            <th>Room</th>
        </tr>
        <?php while($schedule = $schedules->fetch_assoc()): ?>
            <tr>
                <td><?php echo $schedule['id']; ?></td>
                <td><?php echo $schedule['date_time']; ?></td>
                <td><?php echo $schedule['capacity']; ?></td>
                <td><?php echo $schedule['duration']; ?></td>
                <td><?php echo $schedule['room']; ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
