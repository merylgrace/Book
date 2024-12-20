<?php
session_start();
include 'connect.php';
include 'header.php';

// Check if 'user_id' is passed in the URL, if not use the logged-in user's ID
if (isset($_GET['user_id'])) {
    $profile_user_id = $_GET['user_id'];
} else {
    $profile_user_id = $_SESSION['user_id']; // Default to logged-in user's profile
}

// Get user details for the profile
$sql = "SELECT * FROM Users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $profile_user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Get the user's posts
$sql = "SELECT post_id, book_url, content, created_at FROM Posts WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $profile_user_id);
$stmt->execute();
$posts_result = $stmt->get_result();
$stmt->close();

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
    <title>Book Lovers Social Media</title>
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

        /* Profile Header with Flexbox */
        .profile-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        /* Profile image style */
        .profile-header img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            margin-right: 20px;
        }

        /* Username style */
        .profile-header .username {
            color: #007BFF;
            font-size: 1.5em;
            text-align: left;
            flex-grow: 1;
        }

        /* Follow button style */
        .follow-link {
            background-color: #007BFF;
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
        }

        .follow-link:hover {
            background-color: #0056b3;
        }

        .profile-info p {
            font-size: 1.1em;
            margin-bottom: 10px;
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

        /* Post Actions Buttons */
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

        /* Edit Profile Button */
        .profile-actions a {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .profile-actions a:hover {
            background-color: #0056b3;
        }

        /* Comment Form */
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
        <!-- Profile Header -->
        <div class="profile-header">
            <!-- Profile Image -->
            <div>
                <?php if (!empty($user['profile_image'])): ?>
                    <img src="uploads/<?= $user['profile_image'] ?>" alt="Profile Image">
                <?php endif; ?>
            </div>

            <!-- Username -->
            <div class="username">
                <h3><?= htmlspecialchars($user['username']) ?></h3>
            </div>

            <?php
            $current_user_id = $_SESSION['user_id']; // Get the logged-in user's ID

            // Check if the current user is already following the target user
            $checkFollowQuery = "SELECT * FROM Follows WHERE follower_id = ? AND followed_id = ?";
            $stmt = $conn->prepare($checkFollowQuery);
            $stmt->bind_param("ii", $current_user_id, $profile_user_id); // Corrected to use the correct profile_user_id
            $stmt->execute();
            $followResult = $stmt->get_result();

            // Display Follow/Following button
            if ($followResult->num_rows > 0): // User is already following
            ?>
                <span class="follow-link">Following</span>
            <?php else: // User is not following ?>
                <a href="follow.php?followed_user_id=<?= $profile_user_id ?>" class="follow-link">Follow</a>
            <?php endif; ?>
        </div>

        <!-- Profile Information -->
        <div class="profile-info">
            <p>Email: <?= htmlspecialchars($user['email']) ?></p>
            <p>Bio: <?= htmlspecialchars($user['bio']) ?></p>
            <p>Member Since: <?= date("F j, Y", strtotime($user['created_at'])) ?></p>
        </div>

        <!-- User Posts -->
        <div class="posts">
            <h3>Posts</h3>
            <?php if ($posts_result->num_rows > 0): ?>
                <?php while ($post = $posts_result->fetch_assoc()): ?>
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
                <p>This user hasn't posted anything yet.</p>
            <?php endif; ?>
        </div>

        <!-- Edit Profile Button -->
        <?php if ($_SESSION['user_id'] == $user['user_id']): ?>
            <div class="profile-actions">
                <a href="editprof.php">Edit Profile</a>
            </div>
        <?php endif; ?>
    </div>
</body>

</html>

<?php include 'footer.php'; ?>