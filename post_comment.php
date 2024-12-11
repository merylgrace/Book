<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'connect.php';

// Get the current user ID
$current_user_id = $_SESSION['user_id'];

// Handle the comment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['comment_content']) && isset($_POST['post_id'])) {
        $comment_content = trim($_POST['comment_content']);
        $post_id = $_POST['post_id'];

        if (!empty($comment_content)) {
            $comment_sql = "INSERT INTO Comments (post_id, user_id, content, created_at) VALUES (?, ?, ?, NOW())";
            $stmt = $conn->prepare($comment_sql);
            $stmt->bind_param("iis", $post_id, $current_user_id, $comment_content);
            if ($stmt->execute()) {
                // Redirect back to home.php to show the updated post with comments
                header("Location: home.php");
                exit();
            } else {
                echo "Error: Could not submit comment.";
            }
        } else {
            // Redirect with an error message if the comment is empty
            header("Location: home.php?error=empty_comment");
            exit();
        }
    }
}
?>