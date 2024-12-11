-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 11, 2024 at 05:27 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `final`
--

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `comment_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`comment_id`, `post_id`, `user_id`, `content`, `created_at`) VALUES
(1, 1, 2, 'Will add this to my to read list!', '2024-12-11 14:47:08');

-- --------------------------------------------------------

--
-- Table structure for table `follows`
--

CREATE TABLE `follows` (
  `follow_id` int(11) NOT NULL,
  `follower_id` int(11) NOT NULL,
  `followed_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `follows`
--

INSERT INTO `follows` (`follow_id`, `follower_id`, `followed_id`, `created_at`) VALUES
(1, 2, 1, '2024-12-11 13:45:26'),
(2, 3, 2, '2024-12-11 15:39:43'),
(3, 3, 1, '2024-12-11 15:39:56'),
(4, 4, 2, '2024-12-11 16:04:35'),
(5, 4, 1, '2024-12-11 16:04:39');

-- --------------------------------------------------------

--
-- Table structure for table `likes`
--

CREATE TABLE `likes` (
  `like_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `likes`
--

INSERT INTO `likes` (`like_id`, `post_id`, `user_id`, `created_at`) VALUES
(1, 1, 2, '2024-12-11 13:58:44'),
(3, 2, 2, '2024-12-11 14:43:25'),
(4, 2, 3, '2024-12-11 15:39:46'),
(5, 1, 3, '2024-12-11 15:50:56'),
(6, 2, 4, '2024-12-11 16:04:42'),
(7, 1, 4, '2024-12-11 16:04:45');

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `book_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`post_id`, `user_id`, `content`, `created_at`, `book_url`) VALUES
(1, 1, 'Just finished reading Divine by Karen Kingsbury!\r\n\r\nThis book. 💔✨ \r\n\r\nWords can’t even begin to describe how powerful, emotional, and healing this story is. \r\nMary Madison’s journey from unimaginable pain to redemption and hope left me absolutely in awe. \r\nHer courage, her faith, and her mission to help women like her is nothing short of inspiring. 🙏\r\n\r\nKaren Kingsbury weaves a tale of brokenness, faith, and the kind of love that only comes from above. \r\nPeggy Madison’s unshakable prayers, Mary’s transformation, and Emma Randall’s search for hope all come together in a way that’s beautifully life-changing.\r\n\r\nIf you’re looking for a story about grace, forgiveness, and the kind of strength that can only come from faith, this is a MUST-read. 📖💕\r\n\r\nHave tissues handy because this one will tug at your heartstrings in the best way possible.\r\n\r\nHave you read Divine yet? Let’s chat about it!', '2024-12-09 15:03:57', 'https://www.karenkingsbury.com/divine'),
(2, 2, 'If you enjoy thrilling mysteries with a legal twist, The Reversal by Michael Connelly is for you! Follow defense lawyer Mickey Haller as he tries to help a man who claims he was wrongly convicted of murder. The deeper Haller digs, the more secrets and danger he uncovers.\r\n\r\nThis fast-paced book is full of twists and surprises that will keep you hooked until the end. Ready for a page-turning adventure?', '2024-12-11 13:42:59', 'https://www.michaelconnelly.com/writing/thereversal/'),
(3, 4, 'If you\'re looking for a thrilling fantasy read, A Court of Thorns and Roses by Sarah J. Maas is the perfect choice! 🌹\r\n\r\nFeyre, a young woman, kills a faerie and is taken to a magical land as punishment. There, she discovers a world full of mystery, danger, and a romance she never saw coming. With fae, adventure, and secrets around every corner, this book will keep you turning pages!\r\n\r\nPerfect for fans of magic, romance, and action. 📖✨', '2024-12-11 16:17:59', 'https://sarahjmaas.com/a-court-of-thorns-roses-series/'),
(4, 3, 'The 5 AM Club by Robin Sharma has truly been a life-changing read for me. It taught me how to make the most of my mornings, set positive intentions for the day, and improve my focus. The simple routine has helped me feel more energized and motivated, and I’ve noticed a huge boost in my productivity!\r\n\r\nIf you’re looking for a gentle way to create positive changes in your life, I highly recommend giving it a try. 🌅📖\r\n\r\nReady to take control of your mornings and transform your days? Read The 5 AM Club now and see the difference for yourself!', '2024-12-11 16:25:26', 'https://www.robinsharma.com/books/the-5am-club');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `bio` text DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `password`, `bio`, `profile_image`, `created_at`) VALUES
(1, 'mryl', 'luntianmerylgrace@gmail.com', '$2y$10$.ObvrPkwsB1TGUehxtKse.y.rywXPSVFMsrJDYqFRs68/clRxkmTu', 'big rom-com, action, and adventure reader', '1733922731_cat.jpg', '2024-12-09 15:00:11'),
(2, 'xxraymundxx', 'raymundmacapundag113@gmail.com', '$2y$10$DHDcNqbJBwgc/KLKBb5zbe30o8xGy7cxim1fnx7cfb3XIPhjSCGl2', 'mystery crime kinda guy', '1733924317_kuya raymund.jpg', '2024-12-11 13:31:40'),
(3, 'melvrey', 'melvin.reyes122000@gmail.com', '$2y$10$Jo.5bVmBZFwlzWmDSi.FoOrKREO5ovW0Bpd2x1WYeVRceT3Ns2JgO', 'self-help books are my cup of tea', '1733931000_kuya melvin.jpg', '2024-12-11 15:25:45'),
(4, 'jane', 'louellajanebaslao@gmail.com', '$2y$10$XcmAlIIC6ic7SLJzFOlvWuqpKx4QEglLRIoFa2yACAWoUne3Q2IPS', 'I love fantasy all the way ', '1733933194_jane.jpg', '2024-12-11 16:04:18');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`comment_id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `follows`
--
ALTER TABLE `follows`
  ADD PRIMARY KEY (`follow_id`),
  ADD UNIQUE KEY `follower_id` (`follower_id`,`followed_id`),
  ADD KEY `followed_id` (`followed_id`);

--
-- Indexes for table `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`like_id`),
  ADD UNIQUE KEY `post_id` (`post_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`post_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `comment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `follows`
--
ALTER TABLE `follows`
  MODIFY `follow_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `likes`
--
ALTER TABLE `likes`
  MODIFY `like_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `post_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`post_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `follows`
--
ALTER TABLE `follows`
  ADD CONSTRAINT `follows_ibfk_1` FOREIGN KEY (`follower_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `follows_ibfk_2` FOREIGN KEY (`followed_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `likes`
--
ALTER TABLE `likes`
  ADD CONSTRAINT `likes_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`post_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `likes_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
