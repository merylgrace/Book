<?php
session_start();
include 'connect.php';

if (isset($_POST['post_id']) && isset($_SESSION['user_id'])) {
    $post_id = $_POST['post_id'];
    $user_id = $_SESSION['user_id'];

    // Check if the user has already liked the post
    $sql = "SELECT 1 FROM Likes WHERE post_id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $post_id, $user_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 0) {
        // User has not liked the post yet, insert the like
        $sql = "INSERT INTO Likes (user_id, post_id) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $user_id, $post_id);
        $stmt->execute();
    }

    $stmt->close();
    // Redirect back to the post or profile page
    header("Location: home.php"); // You can adjust this based on where you want to redirect
    exit();
}
?>