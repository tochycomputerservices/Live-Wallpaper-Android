-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 15, 2020 at 11:08 PM
-- Server version: 5.7.23
-- PHP Version: 7.1.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `wall_updated_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories_table`
--

CREATE TABLE `categories_table` (
  `id` int(11) NOT NULL,
  `media_id` int(11) DEFAULT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `position` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `colors_table`
--

CREATE TABLE `colors_table` (
  `id` int(11) NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `code` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `position` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `comment_table`
--

CREATE TABLE `comment_table` (
  `id` int(11) NOT NULL,
  `wallpaper_id` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `content` longtext COLLATE utf8_unicode_ci NOT NULL,
  `created` datetime NOT NULL,
  `enabled` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `device_table`
--

CREATE TABLE `device_table` (
  `id` int(11) NOT NULL,
  `token` longtext COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fos_user_table`
--

CREATE TABLE `fos_user_table` (
  `id` int(11) NOT NULL,
  `username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `username_canonical` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email_canonical` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  `salt` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `last_login` datetime DEFAULT NULL,
  `locked` tinyint(1) NOT NULL,
  `expired` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL,
  `confirmation_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password_requested_at` datetime DEFAULT NULL,
  `roles` longtext COLLATE utf8_unicode_ci NOT NULL COMMENT '(DC2Type:array)',
  `credentials_expired` tinyint(1) NOT NULL,
  `credentials_expire_at` datetime DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `code` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `facebook` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `instagram` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `twitter` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `emailo` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `token` longtext COLLATE utf8_unicode_ci,
  `image` longtext COLLATE utf8_unicode_ci NOT NULL,
  `trusted` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `fos_user_table`
--

INSERT INTO `fos_user_table` (`id`, `username`, `username_canonical`, `email`, `email_canonical`, `enabled`, `salt`, `password`, `last_login`, `locked`, `expired`, `expires_at`, `confirmation_token`, `password_requested_at`, `roles`, `credentials_expired`, `credentials_expire_at`, `name`, `code`, `facebook`, `instagram`, `twitter`, `emailo`, `type`, `token`, `image`, `trusted`) VALUES
(1, 'ADMIN', 'admin', 'ADMIN', 'admin', 1, 'djtfgbufxr4gwk4k0gss4sgs4k48wc4', '$2y$13$djtfgbufxr4gwk4k0gss4ekodAwfJ3IP01OyKvMD.stoxgr6MMa2S', '2019-05-20 21:31:51', 0, 0, NULL, NULL, NULL, 'a:1:{i:0;s:10:\"ROLE_ADMIN\";}', 0, NULL, 'Admin', '', NULL, NULL, NULL, NULL, 'email', NULL, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSq06v_YeFtM-5YtnSiHIP1vqUsYva3WqKPmXNzb_tpCzwk6E6W', 0);

-- --------------------------------------------------------

--
-- Table structure for table `gallery_table`
--

CREATE TABLE `gallery_table` (
  `id` int(11) NOT NULL,
  `titre` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `medias_gallerys_table`
--

CREATE TABLE `medias_gallerys_table` (
  `gallery_id` int(11) NOT NULL,
  `media_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `media_table`
--

CREATE TABLE `media_table` (
  `id` int(11) NOT NULL,
  `titre` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `extension` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `date` datetime NOT NULL,
  `enabled` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `packs_table`
--

CREATE TABLE `packs_table` (
  `id` int(11) NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  `created` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rate_table`
--

CREATE TABLE `rate_table` (
  `id` int(11) NOT NULL,
  `wallpaper_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `value` int(11) NOT NULL,
  `review` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `settings_table`
--

CREATE TABLE `settings_table` (
  `id` int(11) NOT NULL,
  `firebasekey` longtext COLLATE utf8_unicode_ci NOT NULL,
  `publisherid` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `appid` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `rewardedadmobid` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `banneradmobid` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `bannerfacebookid` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `bannertype` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `nativeadmobid` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `nativefacebookid` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `nativeitem` int(11) DEFAULT NULL,
  `nativetype` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `interstitialadmobid` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `interstitialfacebookid` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `interstitialtype` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `interstitialclick` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `settings_table`
--

INSERT INTO `settings_table` (`id`, `firebasekey`, `publisherid`, `appid`, `rewardedadmobid`, `banneradmobid`, `bannerfacebookid`, `bannertype`, `nativeadmobid`, `nativefacebookid`, `nativeitem`, `nativetype`, `interstitialadmobid`, `interstitialfacebookid`, `interstitialtype`, `interstitialclick`) VALUES
(1, '1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `slide_table`
--

CREATE TABLE `slide_table` (
  `id` int(11) NOT NULL,
  `wallpaper_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `media_id` int(11) DEFAULT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `url` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `position` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `support_table`
--

CREATE TABLE `support_table` (
  `id` int(11) NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `subject` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `message` longtext COLLATE utf8_unicode_ci NOT NULL,
  `created` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tags_table`
--

CREATE TABLE `tags_table` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `search` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_followers`
--

CREATE TABLE `user_followers` (
  `user_id` int(11) NOT NULL,
  `follower_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `version_table`
--

CREATE TABLE `version_table` (
  `id` int(11) NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `features` longtext COLLATE utf8_unicode_ci NOT NULL,
  `code` int(11) NOT NULL,
  `enabled` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wallpapers_table`
--

CREATE TABLE `wallpapers_table` (
  `id` int(11) NOT NULL,
  `media_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `video_id` int(11) DEFAULT NULL,
  `title` longtext COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `color` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `size` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `resolution` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8_unicode_ci,
  `created` datetime NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  `review` tinyint(1) NOT NULL,
  `premium` tinyint(1) NOT NULL,
  `comment` tinyint(1) NOT NULL,
  `tags` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `shares` int(11) NOT NULL,
  `sets` int(11) NOT NULL,
  `downloads` int(11) NOT NULL,
  `views` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wallpapers_tags_table`
--

CREATE TABLE `wallpapers_tags_table` (
  `wallpaper_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wallpaper_categories_table`
--

CREATE TABLE `wallpaper_categories_table` (
  `wallpaper_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wallpaper_colors_table`
--

CREATE TABLE `wallpaper_colors_table` (
  `wallpaper_id` int(11) NOT NULL,
  `color_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wallpaper_packs_table`
--

CREATE TABLE `wallpaper_packs_table` (
  `wallpaper_id` int(11) NOT NULL,
  `pack_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories_table`
--
ALTER TABLE `categories_table`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_F22A9A0BEA9FDD75` (`media_id`);

--
-- Indexes for table `colors_table`
--
ALTER TABLE `colors_table`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `comment_table`
--
ALTER TABLE `comment_table`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_5FB317B7488626AA` (`wallpaper_id`),
  ADD KEY `IDX_5FB317B7A76ED395` (`user_id`);

--
-- Indexes for table `device_table`
--
ALTER TABLE `device_table`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fos_user_table`
--
ALTER TABLE `fos_user_table`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_C3D4D4BD92FC23A8` (`username_canonical`),
  ADD UNIQUE KEY `UNIQ_C3D4D4BDA0D96FBF` (`email_canonical`);

--
-- Indexes for table `gallery_table`
--
ALTER TABLE `gallery_table`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `medias_gallerys_table`
--
ALTER TABLE `medias_gallerys_table`
  ADD PRIMARY KEY (`gallery_id`,`media_id`),
  ADD KEY `IDX_CC965DCE4E7AF8F` (`gallery_id`),
  ADD KEY `IDX_CC965DCEEA9FDD75` (`media_id`);

--
-- Indexes for table `media_table`
--
ALTER TABLE `media_table`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `packs_table`
--
ALTER TABLE `packs_table`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rate_table`
--
ALTER TABLE `rate_table`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_66699665488626AA` (`wallpaper_id`),
  ADD KEY `IDX_66699665A76ED395` (`user_id`);

--
-- Indexes for table `settings_table`
--
ALTER TABLE `settings_table`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `slide_table`
--
ALTER TABLE `slide_table`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_77A059652B36786B` (`title`),
  ADD KEY `IDX_77A05965488626AA` (`wallpaper_id`),
  ADD KEY `IDX_77A0596512469DE2` (`category_id`),
  ADD KEY `IDX_77A05965EA9FDD75` (`media_id`);

--
-- Indexes for table `support_table`
--
ALTER TABLE `support_table`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tags_table`
--
ALTER TABLE `tags_table`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_followers`
--
ALTER TABLE `user_followers`
  ADD PRIMARY KEY (`user_id`,`follower_id`),
  ADD KEY `IDX_84E87043A76ED395` (`user_id`),
  ADD KEY `IDX_84E87043AC24F853` (`follower_id`);

--
-- Indexes for table `version_table`
--
ALTER TABLE `version_table`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wallpapers_table`
--
ALTER TABLE `wallpapers_table`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_AC0BF4CEEA9FDD75` (`media_id`),
  ADD KEY `IDX_AC0BF4CEA76ED395` (`user_id`),
  ADD KEY `IDX_AC0BF4CE29C1004E` (`video_id`);

--
-- Indexes for table `wallpapers_tags_table`
--
ALTER TABLE `wallpapers_tags_table`
  ADD PRIMARY KEY (`wallpaper_id`,`tag_id`),
  ADD KEY `IDX_DB788280488626AA` (`wallpaper_id`),
  ADD KEY `IDX_DB788280BAD26311` (`tag_id`);

--
-- Indexes for table `wallpaper_categories_table`
--
ALTER TABLE `wallpaper_categories_table`
  ADD PRIMARY KEY (`wallpaper_id`,`category_id`),
  ADD KEY `IDX_8F98E063488626AA` (`wallpaper_id`),
  ADD KEY `IDX_8F98E06312469DE2` (`category_id`);

--
-- Indexes for table `wallpaper_colors_table`
--
ALTER TABLE `wallpaper_colors_table`
  ADD PRIMARY KEY (`wallpaper_id`,`color_id`),
  ADD KEY `IDX_319AE588488626AA` (`wallpaper_id`),
  ADD KEY `IDX_319AE5887ADA1FB5` (`color_id`);

--
-- Indexes for table `wallpaper_packs_table`
--
ALTER TABLE `wallpaper_packs_table`
  ADD PRIMARY KEY (`wallpaper_id`,`pack_id`),
  ADD KEY `IDX_6BF9188D488626AA` (`wallpaper_id`),
  ADD KEY `IDX_6BF9188D1919B217` (`pack_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories_table`
--
ALTER TABLE `categories_table`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `colors_table`
--
ALTER TABLE `colors_table`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `comment_table`
--
ALTER TABLE `comment_table`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `device_table`
--
ALTER TABLE `device_table`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fos_user_table`
--
ALTER TABLE `fos_user_table`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `gallery_table`
--
ALTER TABLE `gallery_table`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `media_table`
--
ALTER TABLE `media_table`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `packs_table`
--
ALTER TABLE `packs_table`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rate_table`
--
ALTER TABLE `rate_table`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `settings_table`
--
ALTER TABLE `settings_table`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `slide_table`
--
ALTER TABLE `slide_table`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `support_table`
--
ALTER TABLE `support_table`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tags_table`
--
ALTER TABLE `tags_table`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `version_table`
--
ALTER TABLE `version_table`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wallpapers_table`
--
ALTER TABLE `wallpapers_table`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `categories_table`
--
ALTER TABLE `categories_table`
  ADD CONSTRAINT `FK_F22A9A0BEA9FDD75` FOREIGN KEY (`media_id`) REFERENCES `media_table` (`id`);

--
-- Constraints for table `comment_table`
--
ALTER TABLE `comment_table`
  ADD CONSTRAINT `FK_5FB317B7488626AA` FOREIGN KEY (`wallpaper_id`) REFERENCES `wallpapers_table` (`id`),
  ADD CONSTRAINT `FK_5FB317B7A76ED395` FOREIGN KEY (`user_id`) REFERENCES `fos_user_table` (`id`);

--
-- Constraints for table `medias_gallerys_table`
--
ALTER TABLE `medias_gallerys_table`
  ADD CONSTRAINT `FK_CC965DCE4E7AF8F` FOREIGN KEY (`gallery_id`) REFERENCES `gallery_table` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_CC965DCEEA9FDD75` FOREIGN KEY (`media_id`) REFERENCES `media_table` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `rate_table`
--
ALTER TABLE `rate_table`
  ADD CONSTRAINT `FK_66699665488626AA` FOREIGN KEY (`wallpaper_id`) REFERENCES `wallpapers_table` (`id`),
  ADD CONSTRAINT `FK_66699665A76ED395` FOREIGN KEY (`user_id`) REFERENCES `fos_user_table` (`id`);

--
-- Constraints for table `slide_table`
--
ALTER TABLE `slide_table`
  ADD CONSTRAINT `FK_77A0596512469DE2` FOREIGN KEY (`category_id`) REFERENCES `categories_table` (`id`),
  ADD CONSTRAINT `FK_77A05965488626AA` FOREIGN KEY (`wallpaper_id`) REFERENCES `wallpapers_table` (`id`),
  ADD CONSTRAINT `FK_77A05965EA9FDD75` FOREIGN KEY (`media_id`) REFERENCES `media_table` (`id`);

--
-- Constraints for table `user_followers`
--
ALTER TABLE `user_followers`
  ADD CONSTRAINT `FK_84E87043A76ED395` FOREIGN KEY (`user_id`) REFERENCES `fos_user_table` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_84E87043AC24F853` FOREIGN KEY (`follower_id`) REFERENCES `fos_user_table` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `wallpapers_table`
--
ALTER TABLE `wallpapers_table`
  ADD CONSTRAINT `FK_AC0BF4CE29C1004E` FOREIGN KEY (`video_id`) REFERENCES `media_table` (`id`),
  ADD CONSTRAINT `FK_AC0BF4CEA76ED395` FOREIGN KEY (`user_id`) REFERENCES `fos_user_table` (`id`),
  ADD CONSTRAINT `FK_AC0BF4CEEA9FDD75` FOREIGN KEY (`media_id`) REFERENCES `media_table` (`id`);

--
-- Constraints for table `wallpapers_tags_table`
--
ALTER TABLE `wallpapers_tags_table`
  ADD CONSTRAINT `FK_DB788280488626AA` FOREIGN KEY (`wallpaper_id`) REFERENCES `wallpapers_table` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_DB788280BAD26311` FOREIGN KEY (`tag_id`) REFERENCES `tags_table` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `wallpaper_categories_table`
--
ALTER TABLE `wallpaper_categories_table`
  ADD CONSTRAINT `FK_8F98E06312469DE2` FOREIGN KEY (`category_id`) REFERENCES `categories_table` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_8F98E063488626AA` FOREIGN KEY (`wallpaper_id`) REFERENCES `wallpapers_table` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `wallpaper_colors_table`
--
ALTER TABLE `wallpaper_colors_table`
  ADD CONSTRAINT `FK_319AE588488626AA` FOREIGN KEY (`wallpaper_id`) REFERENCES `wallpapers_table` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_319AE5887ADA1FB5` FOREIGN KEY (`color_id`) REFERENCES `colors_table` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `wallpaper_packs_table`
--
ALTER TABLE `wallpaper_packs_table`
  ADD CONSTRAINT `FK_6BF9188D1919B217` FOREIGN KEY (`pack_id`) REFERENCES `packs_table` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_6BF9188D488626AA` FOREIGN KEY (`wallpaper_id`) REFERENCES `wallpapers_table` (`id`) ON DELETE CASCADE;
