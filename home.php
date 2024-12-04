<?php
include 'connect.php';
include 'header.php';

$sql = "SELECT Posts.post_id, Users.username, Books.title, Posts.content, Posts.created_at 
        FROM Posts 
        JOIN Users ON Posts.user_id = Users.user_id 
        JOIN Books ON Posts.book_id = Books.book_id 
        ORDER BY Posts.created_at DESC";
$result = $conn->query($sql);
?>

<div class="posts">
    <h2>Recent Posts</h2>
    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="post">
                <h3><?= htmlspecialchars($row['title']) ?></h3>
                <p>By: <?= htmlspecialchars($row['username']) ?></p>
                <p><?= htmlspecialchars($row['content']) ?></p>
                <p>Posted on: <?= $row['created_at'] ?></p>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No posts yet!</p>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>