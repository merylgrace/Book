<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
include 'connect.php';
include 'header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $book_id = $_POST['book_id'];
    $content = $_POST['content'];

    $stmt = $conn->prepare("INSERT INTO Posts (user_id, book_id, content, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("iis", $user_id, $book_id, $content);

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

        header {
            background-color: #007BFF;
            color: white;
            padding: 10px 20px;
            text-align: center;
        }

        header nav {
            margin-top: 10px;
        }

        header nav a {
            color: white;
            margin: 0 10px;
            text-decoration: none;
        }

        header nav a:hover {
            text-decoration: underline;
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
    <header>
        <h1>Book Lovers</h1>
        <nav>
            <a href="index.php">Home</a>
            <a href="profile.php">Profile</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <div class="container">
        <form method="POST" action="">
            <label for="book_id">Book:</label>
            <select name="book_id" id="book_id" required>
                <?php
                $bookQuery = "SELECT book_id, title FROM Books";
                $bookResult = $conn->query($bookQuery);
                while ($book = $bookResult->fetch_assoc()) {
                    echo "<option value='{$book['book_id']}'>{$book['title']}</option>";
                }
                ?>
            </select>
            <label for="content">Post Content:</label>
            <textarea name="content" id="content" rows="5" required></textarea>
            <button type="submit">Post</button>
        </form>

        <div class="posts">
            <h2>Recent Posts</h2>
            <?php
            $sql = "SELECT Posts.post_id, Users.username, Books.title, Posts.content, Posts.created_at 
                    FROM Posts 
                    JOIN Users ON Posts.user_id = Users.user_id 
                    JOIN Books ON Posts.book_id = Books.book_id 
                    ORDER BY Posts.created_at DESC";
            $result = $conn->query($sql);

            if ($result->num_rows > 0):
                while ($row = $result->fetch_assoc()):
            ?>
                    <div class="post">
                        <h3><?= htmlspecialchars($row['title']) ?></h3>
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