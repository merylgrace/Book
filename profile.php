<?php
session_start();
include 'connect.php';
include 'header.php';

// Get user details
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM Users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Get the user's posts
$sql = "SELECT post_id, book_url, content, created_at FROM Posts WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$posts_result = $stmt->get_result();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Book Lovers Social Media</title>
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
            align-items: center; /* Align items vertically in the center */
            justify-content: space-between; /* Space between the profile image, username, and follow button */
        }

        /* Profile image style */
        .profile-header img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            margin-right: 20px; /* Space between image and username */
        }

        /* Username style */
        .profile-header .username {
            color: #007BFF;
            font-size: 1.5em;
            text-align: left; /* Left-align the username */
            flex-grow: 1; /* Allow the username div to grow and take available space */
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
    </style>
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

            <!-- Follow Button -->
            <div>
                <a href="follow.php?user_id=<?= $user['user_id'] ?>" class="follow-link">Follow</a>
            </div>
        </div>

        <!-- Profile Information -->
        <div class="profile-info">
            <p>Email: <?= htmlspecialchars($user['email']) ?></p>
            <p>Bio: <?= htmlspecialchars($user['bio']) ?></p>
            <p>Member Since: <?= date("F j, Y", strtotime($user['created_at'])) ?></p>
        </div>

        <!-- User Posts -->
        <div class="posts">
            <h3>Your Posts</h3>
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
                            <button onclick="window.location.href='like.php?post_id=<?= $post['post_id'] ?>'">Like</button>
                            <button onclick="window.location.href='comment.php?post_id=<?= $post['post_id'] ?>'">Comment</button>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>You haven't posted anything yet!</p>
            <?php endif; ?>
        </div>

        <!-- Edit Profile Button -->
        <div class="profile-actions">
            <a href="editprof.php">Edit Profile</a>
        </div>
    </div>
</body>

</html>

<?php include 'footer.php'; ?>