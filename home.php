<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'connect.php';
include 'header.php';

// Get posts and related user details
$sql = "SELECT Posts.post_id, Users.user_id AS poster_id, Users.username, Posts.content, Posts.created_at 
        FROM Posts 
        JOIN Users ON Posts.user_id = Users.user_id 
        ORDER BY Posts.created_at DESC";
$result = $conn->query($sql);

// Get the logged-in user ID
$current_user_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Book Lovers Social Media</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 70%;
            margin: 50px auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .post {
            background-color: #fff;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .post-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .post-header .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .post-header .username {
            color: #007BFF;
            text-decoration: none;
            font-weight: bold;
        }

        .post-header .username:hover {
            text-decoration: underline;
        }

        .post-header .follow-link {
            color: #007BFF;
            text-decoration: none;
            font-size: 0.9em;
        }

        .post-header .follow-link:hover {
            text-decoration: underline;
        }

        .post-content {
            margin-bottom: 10px;
        }

        .post-actions {
            display: flex;
            justify-content: flex-start;
            gap: 10px;
        }

        .post-actions button {
            background-color: #007BFF;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
        }

        .post-actions button:hover {
            background-color: #0056b3;
        }

        .no-posts {
            text-align: center;
            font-size: 1.2em;
            color: #888;
        }

        .post-form textarea {
            width: 100%;
            height: 100px;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            margin-bottom: 10px;
        }

        .post-form input[type="text"] {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            margin-bottom: 10px;
        }

        .post-form button {
            background-color: #007BFF;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
        }

        .post-form button:hover {
            background-color: #0056b3;
        }

        .liked-button {
            background-color: #28a745;
        }

        .comment-form {
            display: none;
            margin-top: 10px;
        }

        .comment-form textarea {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .comment-form button {
            background-color: #007BFF;
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
            border: none;
        }
    </style>
    <script>
        function toggleCommentForm(postId) {
            const commentForm = document.getElementById('comment-form-' + postId);
            commentForm.style.display = (commentForm.style.display === 'none' || commentForm.style.display === '') ? 'block' : 'none';
        }
    </script>
</head>

<body>
    <div class="container">
        <div class="post-form">
            <h2>Create a New Post</h2>
            <form action="home.php" method="POST">
                <textarea name="content" placeholder="What's on your mind?" required></textarea><br>
                <input type="text" name="book_url" placeholder="Enter a Book URL (optional)"><br>
                <button type="submit">Post</button>
            </form>
        </div>
        <h2>Recent Posts</h2>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="post">
                    <!-- Post Header -->
                    <div class="post-header">
                        <div class="user-info">
                            <a href="profile.php?user_id=<?= $row['poster_id'] ?>" class="username">
                                <?= htmlspecialchars($row['username']) ?>
                            </a>

                            <?php
                            // Check if the current user is already following the target user
                            $checkFollowQuery = "SELECT * FROM Follows WHERE follower_id = ? AND followed_id = ?";
                            $stmt = $conn->prepare($checkFollowQuery);
                            $stmt->bind_param("ii", $current_user_id, $row['poster_id']);
                            $stmt->execute();
                            $followResult = $stmt->get_result();

                            // Display Follow/Following button
                            if ($followResult->num_rows > 0): ?>
                                <span class="follow-link">Following</span>
                            <?php else: ?>
                                <a href="follow.php?followed_user_id=<?= $row['poster_id'] ?>" class="follow-link">Follow</a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Post Content -->
                    <div class="post-content">
                        <p><?= htmlspecialchars($row['content']) ?></p>
                        <small>Posted on: <?= $row['created_at'] ?></small>
                    </div>

                    <!-- Post Actions -->
                    <div class="post-actions">
                        <?php
                        // Check if the current user has already liked this post
                        $like_check_sql = "SELECT 1 FROM Likes WHERE post_id = ? AND user_id = ?";
                        $stmt = $conn->prepare($like_check_sql);
                        $stmt->bind_param("ii", $row['post_id'], $current_user_id);
                        $stmt->execute();
                        $stmt->store_result();
                        $is_liked = $stmt->num_rows > 0;
                        $stmt->close();
                        ?>

                        <!-- Display Like Button or Liked -->
                        <?php if ($is_liked): ?>
                            <button class="liked-button" disabled>Liked</button>
                        <?php else: ?>
                            <form action="like.php" method="POST" style="display: inline;">
                                <input type="hidden" name="post_id" value="<?= $row['post_id'] ?>">
                                <button type="submit">Like</button>
                            </form>
                        <?php endif; ?>

                        <!-- Comment Button with Toggle -->
                        <button type="button" onclick="toggleCommentForm(<?= $row['post_id'] ?>)">Comment</button>

                        <!-- Comment Form -->
                        <div id="comment-form-<?= $row['post_id'] ?>" class="comment-form">
                            <form action="post_comment.php" method="POST">
                                <textarea name="comment_content" placeholder="Write your comment here..." required></textarea><br>
                                <input type="hidden" name="post_id" value="<?= $row['post_id'] ?>">
                                <button type="submit">Post Comment</button>
                            </form>
                        </div>
                    </div>
                    <!-- Display Comments -->
                    <div class="comments">
                        <?php
                        // Get comments for this post
                        $comment_sql = "SELECT Comments.content, Users.username, Comments.created_at 
                    FROM Comments 
                    JOIN Users ON Comments.user_id = Users.user_id 
                    WHERE Comments.post_id = ? 
                    ORDER BY Comments.created_at ASC";
                        $comment_stmt = $conn->prepare($comment_sql);
                        $comment_stmt->bind_param("i", $row['post_id']);  // Use $row['post_id'] instead of $post['post_id']
                        $comment_stmt->execute();
                        $comment_result = $comment_stmt->get_result();

                        if ($comment_result->num_rows > 0):
                            while ($comment = $comment_result->fetch_assoc()): ?>
                                <div class="comment">
                                    <p><strong><?= htmlspecialchars($comment['username']) ?>:</strong> <?= htmlspecialchars($comment['content']) ?></p>
                                    <small>Posted on: <?= date("F j, Y, g:i a", strtotime($comment['created_at'])) ?></small>
                                </div>
                            <?php endwhile;
                        else: ?>
                            <p>No comments yet.</p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="no-posts">No posts available. Be the first to post something!</p>
        <?php endif; ?>
    </div>
</body>

</html>

<?php include 'footer.php'; ?>