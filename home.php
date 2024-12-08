<?php
session_start();
include 'connect.php';
include 'header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $book_url = $_POST['book_url']; // Get the book URL from the form
    $content = $_POST['content'];

    $stmt = $conn->prepare("INSERT INTO Posts (user_id, book_url, content, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("iss", $user_id, $book_url, $content); // Bind the book URL

    if ($stmt->execute()) {
        echo "<p>Post created successfully!</p>";
    } else {
        echo "<p>Error: " . $stmt->error . "</p>";
    }
    $stmt->close();
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
            width: 60%;
            margin: 50px auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        form {
            background: white;
            border: 1px solid #ddd;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        form label {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
        }

        form textarea,
        form select,
        form button {
            width: 100%;
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        form button {
            background: #007BFF;
            color: white;
            font-weight: bold;
            border: none;
            cursor: pointer;
        }

        form button:hover {
            background: #0056b3;
        }

        .posts {
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .post {
            border-bottom: 1px solid #ddd;
            margin-bottom: 15px;
            padding-bottom: 15px;
        }

        .post:last-child {
            border-bottom: none;
        }

        .post h3 {
            color: #007BFF;
        }

        .post p {
            margin: 5px 0;
            color: #555;
        }

        .post p:last-child {
            font-size: 0.9em;
            color: #888;
        }
    </style>
</head>

<body>
    <div class="container">
        <form method="POST" action="">
            <label for="book_url">Book URL (optional):</label>
            <input type="url" name="book_url" id="book_url" placeholder="Enter the URL to the book" />

            <label for="content">Post Content:</label>
            <textarea name="content" id="content" rows="5" required></textarea>

            <button type="submit">Post</button>
        </form>

        <div class="posts">
            <h2>Recent Posts</h2>
            <?php
            $sql = "SELECT Posts.post_id, Users.username, Posts.book_url, Posts.content, Posts.created_at 
                    FROM Posts 
                    JOIN Users ON Posts.user_id = Users.user_id 
                    ORDER BY Posts.created_at DESC";
            $result = $conn->query($sql);

            if ($result->num_rows > 0):
                while ($row = $result->fetch_assoc()):
            ?>
                    <div class="post">
                        <h3>
                            <?php if ($row['book_url']): ?>
                                <a href="<?= htmlspecialchars($row['book_url']) ?>" class="book-link" target="_blank"><?= htmlspecialchars($row['book_url']) ?></a>
                            <?php else: ?>
                                No Book URL Provided
                            <?php endif; ?>
                        </h3>
                        <p>By: <?= htmlspecialchars($row['username']) ?></p>
                        <p><?= htmlspecialchars($row['content']) ?></p>
                        <p>Posted on: <?= $row['created_at'] ?></p>
                    </div>
                <?php
                endwhile;
            else:
                ?>
                <p>No posts yet!</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

<?php include 'footer.php'; ?>