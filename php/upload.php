<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["document"])) {
    $target_dir = "../uploads/";
    $target_file = $target_dir . basename($_FILES["document"]["name"]);
    $student_id = $_SESSION['user_id'];

    if (move_uploaded_file($_FILES["document"]["tmp_name"], $target_file)) {
        $file_name = basename($_FILES["document"]["name"]);

        $sql = "INSERT INTO documents (student_id, file_name) VALUES ('$student_id', '$file_name')";

        if ($conn->query($sql) === TRUE) {
            echo "The file " . htmlspecialchars($file_name) . " has been uploaded.";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
}
$conn->close();
?>
