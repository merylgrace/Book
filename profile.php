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

        .profile-info h3 {
            color: #007BFF;
        }

        .profile-info p {
            font-size: 1.1em;
            margin-bottom: 10px;
        }

        .profile-image {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            margin-top: 10px;
        }

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

        .post {
            background-color: #fff;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .post h4 {
            color: #007BFF;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Profile</h2>

        <div class="profile-info">
            <h3>Username: <?= htmlspecialchars($user['username']) ?></h3>
            <p>Email: <?= htmlspecialchars($user['email']) ?></p>
            <p>Bio: <?= htmlspecialchars($user['bio']) ?></p>
            <?php if (!empty($user['profile_image'])): ?>
                <img src="uploads/<?= $user['profile_image'] ?>" alt="Profile Image" class="profile-image">
            <?php else: ?>
                <p>No profile image uploaded.</p>
            <?php endif; ?>
            <p>Member Since: <?= date("F j, Y", strtotime($user['created_at'])) ?></p>
        </div>

        <div class="posts">
            <h3>Your Posts</h3>
            <?php if ($posts_result->num_rows > 0): ?>
                <?php while ($post = $posts_result->fetch_assoc()): ?>
                    <div class="post">
                        <!-- Display book URL (if exists) -->
                        <?php if ($post['book_url']): ?>
                            <h4><a href="<?= htmlspecialchars($post['book_url']) ?>" target="_blank">Book URL</a></h4>
                        <?php else: ?>
                            <h4>No Book URL Provided</h4>
                        <?php endif; ?>
                        <p><?= htmlspecialchars($post['content']) ?></p>
                        <p>Posted on: <?= $post['created_at'] ?></p>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>You haven't posted anything yet!</p>
            <?php endif; ?>
        </div>

        <div class="profile-actions">
            <a href="editprof.php">Edit Profile</a>
        </div>
    </div>
</body>

</html>

<?php include 'footer.php'; ?>