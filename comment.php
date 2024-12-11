<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'connect.php';

// Get the current user ID
$current_user_id = $_SESSION['user_id'];

// Check if the post_id is provided via GET
if (isset($_GET['post_id'])) {
    $post_id = $_GET['post_id'];

    // Get the post details and the username of the post creator
    $post_sql = "SELECT Posts.*, Users.username FROM Posts 
                 JOIN Users ON Posts.user_id = Users.user_id 
                 WHERE post_id = ?";
    $stmt = $conn->prepare($post_sql);
    $stmt->bind_param("i", $post_id);
    $stmt->execute();
    $post_result = $stmt->get_result();
    $post = $post_result->fetch_assoc();
} else {
    header("Location: home.php");
    exit();
}

// Handle the comment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $comment_content = trim($_POST['comment_content']);

    if (!empty($comment_content)) {
        $comment_sql = "INSERT INTO Comments (post_id, user_id, content, created_at) VALUES (?, ?, ?, NOW())";
        $stmt = $conn->prepare($comment_sql);
        $stmt->bind_param("iis", $post_id, $current_user_id, $comment_content);
        if ($stmt->execute()) {
            header("Location: home.php");
            exit();
        } else {
            echo "Error: Could not submit comment.";
        }
    } else {
        // Redirect with an error message if the comment is empty
        header("Location: comment.php?post_id=" . $post_id . "&error=empty_comment");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comment - Book Lovers Social Media</title>
</head>

<body>
    <div class="container">
        <h2>Comment on the Post</h2>
        <div class="post">
            <div class="post-header">
                <div class="user-info">
                    <a href="profile.php?user_id=<?= $post['user_id'] ?>" class="username">
                        <?= htmlspecialchars($post['username']) ?>
                    </a>
                </div>
            </div>
            <div class="post-content">
                <p><?= htmlspecialchars($post['content']) ?></p>
                <small>Posted on: <?= $post['created_at'] ?></small>
            </div>
        </div>

        <!-- Comment Form -->
        <form action="comment.php?post_id=<?= $post_id ?>" method="POST">
            <textarea name="comment_content" placeholder="Write your comment..." required></textarea><br>
            <button type="submit">Submit Comment</button>
        </form>

        <!-- Display Error if Comment is Empty -->
        <?php if (isset($_GET['error']) && $_GET['error'] === 'empty_comment'): ?>
            <p style="color: red;">Please write a comment before submitting!</p>
        <?php endif; ?>

    </div>
</body>

</html>