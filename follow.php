<?php
session_start();
include 'connect.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get the logged-in user ID and the user to follow
$follower_id = $_SESSION['user_id'];
$followed_user_id = isset($_GET['followed_user_id']) ? intval($_GET['followed_user_id']) : 0;

// Prevent self-following
if ($follower_id === $followed_user_id) {
    header("Location: home.php?error=self_follow");
    exit();
}

// Check if the follow relationship already exists
$sql = "SELECT * FROM Follows WHERE follower_id = ? AND followed_user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $follower_id, $followed_user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Redirect if already following
    header("Location: home.php?error=already_following");
} else {
    // Add a new follow relationship
    $sql = "INSERT INTO Follows (follower_id, followed_user_id) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $follower_id, $followed_user_id);
    
    if ($stmt->execute()) {
        // Redirect with success message
        header("Location: home.php?success=followed");
    } else {
        // Redirect with an error
        header("Location: home.php?error=follow_failed");
    }
}

$stmt->close();
$conn->close();
?>