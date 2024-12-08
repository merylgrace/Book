<?php
session_start();
include 'connect.php';
include 'header.php';

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $bio = $_POST['bio'];

    // Handle profile image upload
    if ($_FILES['profile_image']['error'] == 0) {
        $image = $_FILES['profile_image'];
        $image_name = time() . '_' . basename($image['name']);
        $image_path = 'uploads/' . $image_name;
        move_uploaded_file($image['tmp_name'], $image_path);
    } else {
        // If no image uploaded, use the current image
        $image_name = $_POST['current_image'];
    }

    $sql = "UPDATE Users SET username = ?, email = ?, bio = ?, profile_image = ? WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $username, $email, $bio, $image_name, $user_id);

    if ($stmt->execute()) {
        header("Location: profile.php");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Get user details
$sql = "SELECT * FROM Users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - Book Lovers Social Media</title>
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
        form {
            margin-top: 20px;
        }
        form label {
            display: block;
            margin-bottom: 5px;
        }
        form input,
        form textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        form button {
            background: #007BFF;
            color: white;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
        }
        form button:hover {
            background: #0056b3;
        }
        .profile-image-preview {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Edit Profile</h2>

        <form method="POST" action="" enctype="multipart/form-data">
            <label for="username">Username:</label>
            <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>

            <label for="email">Email:</label>
            <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

            <label for="bio">Bio:</label>
            <textarea name="bio" rows="5"><?= htmlspecialchars($user['bio']) ?></textarea>

            <label for="profile_image">Profile Image:</label>
            <input type="file" name="profile_image" accept="image/*">

            <?php if (!empty($user['profile_image'])): ?>
                <img src="uploads/<?= $user['profile_image'] ?>" alt="Profile Image" class="profile-image-preview">
                <input type="hidden" name="current_image" value="<?= $user['profile_image'] ?>">
            <?php endif; ?>

            <button type="submit">Save Changes</button>
        </form>

        <div class="profile-actions">
            <a href="profile.php">Back to Profile</a>
        </div>
    </div>
</body>
</html>

<?php include 'footer.php'; ?>