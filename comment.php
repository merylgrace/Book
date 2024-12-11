<?php
session_start();
include 'connect.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Process the comment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
    $content = isset($_POST['content']) ? trim($_POST['content']) : '';

    if (empty($content)) {
        header("Location: home.php?error=empty_comment");
        exit();
    }

    // Insert the comment into the database
    $sql = "INSERT INTO Comments (user_id, post_id, content, created_at) VALUES (?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iis", $user_id, $post_id, $content);

    if ($stmt->execute()) {
        header("Location: home.php?success=commented");
    } else {
        header("Location: home.php?error=comment_failed");
    }

    $stmt->close();
    $conn->close();
} else {
    // Redirect if accessed directly
    header("Location: home.php");
    exit();
}
?>