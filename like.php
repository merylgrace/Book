<?php
session_start();
include 'connect.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get the user ID and post ID
$user_id = $_SESSION['user_id'];
$post_id = isset($_GET['post_id']) ? intval($_GET['post_id']) : 0;

// Check if the user has already liked the post
$sql = "SELECT * FROM Likes WHERE user_id = ? AND post_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $post_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // If already liked, remove the like (toggle behavior)
    $sql = "DELETE FROM Likes WHERE user_id = ? AND post_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $user_id, $post_id);
    if ($stmt->execute()) {
        header("Location: home.php?success=unliked");
    } else {
        header("Location: home.php?error=unlike_failed");
    }
} else {
    // Add a new like
    $sql = "INSERT INTO Likes (user_id, post_id) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $user_id, $post_id);
    if ($stmt->execute()) {
        header("Location: home.php?success=liked");
    } else {
        header("Location: home.php?error=like_failed");
    }
}

$stmt->close();
$conn->close();
?>