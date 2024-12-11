<?php
session_start();
include 'connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['followed_user_id'])) {
    $followed_user_id = $_GET['followed_user_id'];
    $follower_id = $_SESSION['user_id'];

    // Check if the user is already following
    $checkFollowQuery = "SELECT * FROM Follows WHERE follower_id = ? AND followed_id = ?";
    $stmt = $conn->prepare($checkFollowQuery);
    $stmt->bind_param("ii", $follower_id, $followed_user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        // If not already following, insert a new follow
        $followQuery = "INSERT INTO Follows (follower_id, followed_id) VALUES (?, ?)";
        $stmt = $conn->prepare($followQuery);
        $stmt->bind_param("ii", $follower_id, $followed_user_id);
        $stmt->execute();
    }

    // Redirect back to the profile or home page
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}