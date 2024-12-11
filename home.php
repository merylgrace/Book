<?php
session_start();
include 'connect.php';
include 'header.php';

// Handle new post creation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['content'])) {
    $content = mysqli_real_escape_string($conn, $_POST['content']);
    $book_url = !empty($_POST['book_url']) ? mysqli_real_escape_string($conn, $_POST['book_url']) : NULL;
    $user_id = $_SESSION['user_id'];

    // Insert the new post into the database
    $sql = "INSERT INTO Posts (user_id, content, book_url, created_at) VALUES (?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iss", $user_id, $content, $book_url);
    $stmt->execute();
    $stmt->close();
}

// Get the posts
$sql = "SELECT post_id, book_url, content, created_at FROM Posts ORDER BY created_at DESC";
$result = $conn->query($sql);

// Function to check if a user has liked a post
function isLiked($post_id, $user_id) {
    global $conn;
    $sql = "SELECT * FROM Likes WHERE user_id = ? AND post_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $user_id, $post_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->num_rows > 0; // Returns true if liked, false if not
}

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
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        /* Post Creation Form */
        .post-form {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 30px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .post-form textarea {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            margin-bottom: 10px;
        }

        .post-form input {
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

        /* Post Styles */
        .post {
            background-color: #fff;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .post h4 {
            color: #007BFF;
        }

        .post a {
            color: #007BFF;
            text-decoration: none;
        }

        .post-actions {
            margin-top: 10px;
        }

        .post-actions button {
            background-color: #007BFF;
            color: white;
            border: none;
            padding: 8px 15px;
            margin-right: 10px;
            border-radius: 5px;
            cursor: pointer;
        }

        .post-actions button:hover {
            background-color: #0056b3;
        }

        .liked {
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

        .comments {
            margin-top: 20px;
        }

        .comment {
            padding: 10px;
            background-color: #f1f1f1;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        .comment p {
            margin: 0;
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
        <!-- Post Creation Form -->
        <div class="post-form">
            <h2>Create a New Post</h2>
            <form action="home.php" method="POST">
                <textarea name="content" placeholder="What's on your mind?" required></textarea><br>
                <input type="text" name="book_url" placeholder="Enter a Book URL (optional)"><br>
                <button type="submit">Post</button>
            </form>
        </div>

        <h2>Recent Posts</h2>

        <!-- Posts -->
        <?php if ($result->num_rows > 0): ?>
            <?php while ($post = $result->fetch_assoc()): ?>
                <div class="post">
                    <!-- Display Book URL if available -->
                    <?php if (!empty($post['book_url'])): ?>
                        <h4><a href="<?= htmlspecialchars($post['book_url']) ?>" target="_blank"><?= htmlspecialchars($post['book_url']) ?></a></h4>
                    <?php else: ?>
                        <h4>No Book URL Provided</h4>
                    <?php endif; ?>

                    <!-- Display Post Content -->
                    <p><?= htmlspecialchars($post['content']) ?></p>
                    <p>Posted on: <?= $post['created_at'] ?></p>

                    <!-- Post Actions -->
                    <div class="post-actions">
                        <!-- Like button -->
                        <?php if (isLiked($post['post_id'], $_SESSION['user_id'])): ?>
                            <button class="liked" disabled>Liked</button>
                        <?php else: ?>
                            <button onclick="window.location.href='like.php?post_id=<?= $post['post_id'] ?>'">Like</button>
                        <?php endif; ?>
                        <!-- Comment Button -->
                        <button type="button" onclick="toggleCommentForm(<?= $post['post_id'] ?>)">Comment</button>

                        <!-- Comment Form -->
                        <div id="comment-form-<?= $post['post_id'] ?>" class="comment-form" style="display:none;">
                            <form action="post_comment.php" method="POST">
                                <textarea name="comment_content" placeholder="Write your comment here..." required></textarea><br>
                                <input type="hidden" name="post_id" value="<?= $post['post_id'] ?>">
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
                        $comment_stmt->bind_param("i", $post['post_id']);
                        $comment_stmt->execute();
                        $comment_result = $comment_stmt->get_result();

                        if ($comment_result->num_rows > 0):
                            while ($comment = $comment_result->fetch_assoc()): ?>
                                <div class="comment">
                                    <p><strong><?= htmlspecialchars($comment['username']) ?>:</strong> <?= htmlspecialchars($comment['content']) ?></p>
                                    <small>Posted on: <?= $comment['created_at'] ?></small>
                                </div>
                            <?php endwhile;
                        else: ?>
                            <p>No comments yet.</p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No posts found.</p>
        <?php endif; ?>
    </div>
</body>

</html>

<?php include 'footer.php'; ?>