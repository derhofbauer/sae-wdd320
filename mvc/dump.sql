-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: db
-- Erstellungszeit: 16. Jun 2021 um 15:27
-- Server-Version: 10.5.9-MariaDB-1:10.5.9+maria~focal
-- PHP-Version: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `mvc`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `crdate` timestamp NOT NULL DEFAULT current_timestamp(),
  `tstamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Daten für Tabelle `categories`
--

INSERT INTO `categories` (`id`, `title`, `slug`, `description`, `crdate`, `tstamp`, `deleted_at`) VALUES
(1, 'Category #1', 'category-1', '<p>Category #1 Description</p>', '2021-04-27 15:18:53', '2021-06-15 13:59:31', NULL),
(2, 'Category #2', 'category-2', 'Category #2 Description', '2021-04-27 15:18:53', '2021-04-29 14:05:51', NULL);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `author` int(11) NOT NULL,
  `content` text DEFAULT NULL,
  `post_id` int(11) NOT NULL,
  `rating` int(5) UNSIGNED DEFAULT NULL,
  `parent` int(11) DEFAULT NULL,
  `crdate` timestamp NOT NULL DEFAULT current_timestamp(),
  `tstamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Daten für Tabelle `comments`
--

INSERT INTO `comments` (`id`, `author`, `content`, `post_id`, `rating`, `parent`, `crdate`, `tstamp`, `deleted_at`) VALUES
(1, 1, '<p>42</p>', 3, 4, NULL, '2021-06-10 14:29:04', '2021-06-16 14:54:45', NULL),
(2, 1, '<p><strong>Hello World!</strong></p>', 2, 4, NULL, '2021-06-10 14:33:44', '2021-06-16 14:54:45', NULL),
(3, 1, '<p>Works?!</p>', 1, 1, NULL, '2021-06-10 14:43:39', '2021-06-16 14:42:48', NULL),
(4, 1, '<p>Antwort auf 42</p>', 1, NULL, 1, '2021-06-10 15:14:00', '2021-06-10 15:14:00', NULL),
(5, 1, '<p>43</p>', 1, NULL, 1, '2021-06-10 15:24:12', '2021-06-10 15:24:12', NULL),
(6, 1, '<p>Wahnsinns Post!</p>', 1, 5, NULL, '2021-06-16 14:16:30', '2021-06-16 14:16:30', NULL);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `favourites`
--

CREATE TABLE `favourites` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Daten für Tabelle `favourites`
--

INSERT INTO `favourites` (`id`, `user_id`, `post_id`) VALUES
(12, 1, 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `files`
--

CREATE TABLE `files` (
  `id` int(11) NOT NULL,
  `path` text NOT NULL,
  `name` text NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `alttext` text DEFAULT NULL,
  `caption` text DEFAULT NULL,
  `is_avatar` tinyint(1) DEFAULT 0,
  `author` int(11) NOT NULL,
  `path_deleted` text DEFAULT NULL COMMENT 'Path of softdeleted file',
  `crdate` timestamp NOT NULL DEFAULT current_timestamp(),
  `tstamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Daten für Tabelle `files`
--

INSERT INTO `files` (`id`, `path`, `name`, `title`, `alttext`, `caption`, `is_avatar`, `author`, `path_deleted`, `crdate`, `tstamp`, `deleted_at`) VALUES
(1, 'uploads/avatars', '1621957968_37844315_454803461597516_8815318794768482304_n (1).jpg', NULL, NULL, NULL, 1, 1, NULL, '2021-05-25 15:52:48', '2021-05-25 15:52:48', NULL),
(2, 'uploads', 'pimp-rollator.jpg', 'Pimp Rollator', 'Pimp Rollator', 'Fancy Rollator', 0, 1, NULL, '2021-05-27 13:08:57', '2021-05-27 13:11:53', NULL),
(3, 'uploads', '1622123240_sonika-agarwal-EjPZ18c5Psw-unsplash.jpg', NULL, NULL, NULL, 0, 1, '', '2021-05-27 13:47:20', '2021-05-27 15:36:05', NULL),
(4, 'uploads', '1622123240_lisanto-Us9M_Ju3_EY-unsplash.jpg', NULL, NULL, NULL, 0, 1, '', '2021-05-27 13:47:20', '2021-05-27 15:36:05', NULL),
(5, 'uploads', '1622123240_muhammadh-saamy-RR8ibEoYdpk-unsplash.jpg', NULL, NULL, NULL, 0, 1, '', '2021-05-27 13:47:20', '2021-05-27 15:36:05', NULL);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `password-resets`
--

CREATE TABLE `password-resets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(64) NOT NULL,
  `crdate` timestamp NOT NULL DEFAULT current_timestamp(),
  `tstamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Daten für Tabelle `password-resets`
--

INSERT INTO `password-resets` (`id`, `user_id`, `token`, `crdate`, `tstamp`, `deleted_at`) VALUES
(1, 1, 'asdlkhsad', '2021-06-16 13:36:51', '2021-06-16 13:36:51', NULL),
(2, 1, 'e81e30d049f8b0eaef56f544ea06c285228cdb7550cf99b12dd1e1383a395f4f', '2021-06-16 13:37:10', '2021-06-16 13:37:10', NULL),
(3, 1, 'aeca2cb9f42c06ba724c68f64f5c58aba2cd0be8d623ed06d0bb4898bd734d8f', '2021-06-16 13:37:40', '2021-06-16 13:52:10', '2021-06-16 13:52:10');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `content` text DEFAULT NULL,
  `author` int(11) NOT NULL,
  `crdate` timestamp NOT NULL DEFAULT current_timestamp(),
  `tstamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Daten für Tabelle `posts`
--

INSERT INTO `posts` (`id`, `title`, `slug`, `content`, `author`, `crdate`, `tstamp`, `deleted_at`) VALUES
(1, 'Blog Post #1', 'blog-post-1', '<p><strong>Some funky post!</strong></p>', 1, '2021-04-22 13:28:09', '2021-06-15 14:02:57', NULL),
(2, 'Blog Post #2', 'blog-post-2', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium. Integer tincidunt. Cras dapibus. Vivamus elementum semper nisi. Aenean vulputate eleifend tellus. Aenean leo ligula, porttitor eu, consequat vitae, eleifend ac, enim. Aliquam lorem ante, dapibus in, viverra quis, feugiat a, tellus. Phasellus viverra nulla ut metus varius laoreet. Quisque rutrum. Aenean imperdiet. Etiam ultricies nisi vel augue. Curabitur ullamcorper ultricies nisi. Nam eget dui. Etiam rhoncus. Maecenas tempus, tellus eget condimentum rhoncus, sem quam semper libero, sit amet adipiscing sem neque sed ipsum. Nam quam nunc, blandit vel, luctus pulvinar, hendrerit id, lorem. Maecenas nec odio et ante tincidunt tempus. Donec vitae sapien ut libero venenatis faucibus. Nullam quis ante. Etiam sit amet orci eget eros faucibus tincidunt. Duis leo. Sed fringilla mauris sit amet nibh. Donec sodales sagittis magna. Sed consequat, leo eget bibendum sodales, augue velit cursus nunc,', 1, '2021-04-22 13:28:09', '2021-04-27 13:47:45', NULL),
(3, 'Blog Post #3', 'blog-post-3', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium. Integer tincidunt. Cras dapibus. Vivamus elementum semper nisi. Aenean vulputate eleifend tellus. Aenean leo ligula, porttitor eu, consequat vitae, eleifend ac, enim. Aliquam lorem ante, dapibus in, viverra quis, feugiat a, tellus. Phasellus viverra nulla ut metus varius laoreet. Quisque rutrum. Aenean imperdiet. Etiam ultricies nisi vel augue. Curabitur ullamcorper ultricies nisi. Nam eget dui. Etiam rhoncus. Maecenas tempus, tellus eget condimentum rhoncus, sem quam semper libero, sit amet adipiscing sem neque sed ipsum. Nam quam nunc, blandit vel, luctus pulvinar, hendrerit id, lorem. Maecenas nec odio et ante tincidunt tempus. Donec vitae sapien ut libero venenatis faucibus. Nullam quis ante. Etiam sit amet orci eget eros faucibus tincidunt. Duis leo. Sed fringilla mauris sit amet nibh. Donec sodales sagittis magna. Sed consequat, leo eget bibendum sodales, augue velit cursus nunc,', 1, '2021-04-22 13:28:09', '2021-04-27 13:47:45', NULL),
(4, 'Blog Post #4', 'blog-post-4', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium. Integer tincidunt. Cras dapibus. Vivamus elementum semper nisi. Aenean vulputate eleifend tellus. Aenean leo ligula, porttitor eu, consequat vitae, eleifend ac, enim. Aliquam lorem ante, dapibus in, viverra quis, feugiat a, tellus. Phasellus viverra nulla ut metus varius laoreet. Quisque rutrum. Aenean imperdiet. Etiam ultricies nisi vel augue. Curabitur ullamcorper ultricies nisi. Nam eget dui. Etiam rhoncus. Maecenas tempus, tellus eget condimentum rhoncus, sem quam semper libero, sit amet adipiscing sem neque sed ipsum. Nam quam nunc, blandit vel, luctus pulvinar, hendrerit id, lorem. Maecenas nec odio et ante tincidunt tempus. Donec vitae sapien ut libero venenatis faucibus. Nullam quis ante. Etiam sit amet orci eget eros faucibus tincidunt. Duis leo. Sed fringilla mauris sit amet nibh. Donec sodales sagittis magna. Sed consequat, leo eget bibendum sodales, augue velit cursus nunc,', 1, '2021-04-22 13:28:09', '2021-04-27 14:41:11', NULL);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `posts_categories_mm`
--

CREATE TABLE `posts_categories_mm` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Daten für Tabelle `posts_categories_mm`
--

INSERT INTO `posts_categories_mm` (`id`, `post_id`, `category_id`) VALUES
(1, 1, 1),
(2, 3, 1),
(3, 2, 2),
(4, 2, 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `posts_files_mm`
--

CREATE TABLE `posts_files_mm` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `file_id` int(11) NOT NULL,
  `sort` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Daten für Tabelle `posts_files_mm`
--

INSERT INTO `posts_files_mm` (`id`, `post_id`, `file_id`, `sort`) VALUES
(1, 1, 2, NULL),
(2, 1, 4, NULL);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `shares`
--

CREATE TABLE `shares` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `recipient` text DEFAULT NULL COMMENT 'Format: Arhut Dent <arthur.dent@galaxy.com>',
  `posts` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Snapshot der Posts zum Zeitpunkt der Erstellung des Shares'
) ;

--
-- Daten für Tabelle `shares`
--

INSERT INTO `shares` (`id`, `user_id`, `recipient`, `posts`, `message`, `status`, `crdate`, `tstamp`) VALUES
(1, 1, NULL, NULL, NULL, 'storno', '2021-06-02 14:10:22', '2021-06-08 14:02:14'),
(2, 1, 'Alexander Hofbauer <hofbauer.alexander@gmail.com>', '[{\"id\":1,\"title\":\"Blog Post #1\",\"slug\":\"blog-post-1\",\"content\":\"Some funky post!\",\"author\":1,\"crdate\":\"2021-04-22 13:28:09\",\"tstamp\":\"2021-05-12 14:10:15\",\"deleted_at\":null}]', NULL, 'progress', '2021-06-02 14:13:50', '2021-06-08 14:21:08'),
(3, 1, 'Alexander Hofbauer <hofbauer.alexander@gmail.com>', '[{\"id\":1,\"title\":\"Blog Post #1\",\"slug\":\"blog-post-1\",\"content\":\"Some funky post!\",\"author\":1,\"crdate\":\"2021-04-22 13:28:09\",\"tstamp\":\"2021-05-12 14:10:15\",\"deleted_at\":null}]', '42', 'open', '2021-06-02 14:25:40', '2021-06-08 14:55:39'),
(4, 1, 'Alexander Hofbauer <a@b.com>', '[{\"id\":1,\"title\":\"Blog Post #1\",\"slug\":\"blog-post-1\",\"content\":\"Some funky post!\",\"author\":1,\"crdate\":\"2021-04-22 13:28:09\",\"tstamp\":\"2021-05-12 14:10:15\",\"deleted_at\":null}]', '42', 'open', '2021-06-02 15:18:04', '2021-06-02 15:18:09');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `avatar` int(11) DEFAULT NULL COMMENT 'file_id',
  `is_admin` tinyint(1) NOT NULL DEFAULT 0,
  `crdate` timestamp NOT NULL DEFAULT current_timestamp(),
  `tstamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Daten für Tabelle `users`
--

INSERT INTO `users` (`id`, `email`, `username`, `password`, `avatar`, `is_admin`, `crdate`, `tstamp`, `deleted_at`) VALUES
(1, 'arthur.dent@galaxy.com', 'adent', '$2y$12$TMLZZda8/PjXraWJWpobsu3.tYiO0IqagsdpwV2ZAsBxvemcrk3vi', 1, 1, '2021-04-22 13:27:28', '2021-06-16 13:53:21', NULL),
(2, 'ford.prefect@galaxy.com', 'fprefect', '$2y$10$iCQYwYKrwBbidBWTHEDZ0eo9ti7Aw.43Wxqg6nfgwb7XcKVZ64q/i', NULL, 0, '2021-05-11 13:36:58', '2021-05-11 13:39:54', NULL);

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indizes für die Tabelle `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `parent` (`parent`),
  ADD KEY `comments_ibfk_3` (`author`);

--
-- Indizes für die Tabelle `favourites`
--
ALTER TABLE `favourites`
  ADD PRIMARY KEY (`id`),
  ADD KEY `favourites_ibfk_1` (`post_id`),
  ADD KEY `favourites_ibfk_2` (`user_id`);

--
-- Indizes für die Tabelle `files`
--
ALTER TABLE `files`
  ADD PRIMARY KEY (`id`),
  ADD KEY `author` (`author`);

--
-- Indizes für die Tabelle `password-resets`
--
ALTER TABLE `password-resets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `user_id` (`user_id`);

--
-- Indizes für die Tabelle `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `author` (`author`);

--
-- Indizes für die Tabelle `posts_categories_mm`
--
ALTER TABLE `posts_categories_mm`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `post_id` (`post_id`);

--
-- Indizes für die Tabelle `posts_files_mm`
--
ALTER TABLE `posts_files_mm`
  ADD PRIMARY KEY (`id`),
  ADD KEY `file_id` (`file_id`),
  ADD KEY `post_id` (`post_id`);

--
-- Indizes für die Tabelle `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `avatar` (`avatar`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT für Tabelle `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT für Tabelle `favourites`
--
ALTER TABLE `favourites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT für Tabelle `files`
--
ALTER TABLE `files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT für Tabelle `password-resets`
--
ALTER TABLE `password-resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT für Tabelle `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT für Tabelle `posts_categories_mm`
--
ALTER TABLE `posts_categories_mm`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT für Tabelle `posts_files_mm`
--
ALTER TABLE `posts_files_mm`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT für Tabelle `shares`
--
ALTER TABLE `shares`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`parent`) REFERENCES `comments` (`id`),
  ADD CONSTRAINT `comments_ibfk_3` FOREIGN KEY (`author`) REFERENCES `users` (`id`);

--
-- Constraints der Tabelle `favourites`
--
ALTER TABLE `favourites`
  ADD CONSTRAINT `favourites_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `favourites_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints der Tabelle `files`
--
ALTER TABLE `files`
  ADD CONSTRAINT `files_ibfk_1` FOREIGN KEY (`author`) REFERENCES `users` (`id`);

--
-- Constraints der Tabelle `password-resets`
--
ALTER TABLE `password-resets`
  ADD CONSTRAINT `password-resets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints der Tabelle `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`author`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints der Tabelle `posts_categories_mm`
--
ALTER TABLE `posts_categories_mm`
  ADD CONSTRAINT `posts_categories_mm_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
  ADD CONSTRAINT `posts_categories_mm_ibfk_2` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`);

--
-- Constraints der Tabelle `posts_files_mm`
--
ALTER TABLE `posts_files_mm`
  ADD CONSTRAINT `posts_files_mm_ibfk_1` FOREIGN KEY (`file_id`) REFERENCES `files` (`id`),
  ADD CONSTRAINT `posts_files_mm_ibfk_2` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`);

--
-- Constraints der Tabelle `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`avatar`) REFERENCES `files` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
