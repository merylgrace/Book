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
    <textarea name="content" id="content" required></textarea>
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

<?php include 'footer.php'; ?>