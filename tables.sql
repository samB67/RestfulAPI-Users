-- phpMyAdmin SQL Dump
-- version 4.9.7
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: Apr 06, 2021 at 07:56 PM
-- Server version: 5.7.32
-- PHP Version: 7.4.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `jdi`
--

-- --------------------------------------------------------

--
-- Table structure for table `access_tokens`
--

CREATE TABLE `access_tokens` (
                                 `id` int(11) NOT NULL,
                                 `access_token` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
                                 `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `access_tokens`
--

INSERT INTO `access_tokens` (`id`, `access_token`, `created_at`) VALUES
(1, 'Y2Y3YTE0NDgyODdjMTgxODVlZDllZDAzMmQ3YTJiODQwZWQ2MjY1YThjNDIxMGRiMTYxNzcyODc1MA==', '2021-04-06 18:06:06'),
(2, 'MWZkMzIzYjE3MWYxMWI0YTdjZWJkYjk1N2UzZGJkN2I5YzRjOGQ3OGVjMmY1MWU3MTYxNzcyODc3OQ==', '2021-04-06 18:06:28');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
                         `id` bigint(20) NOT NULL,
                         `first_name` char(50) NOT NULL,
                         `last_name` char(50) NOT NULL,
                         `username` char(20) NOT NULL,
                         `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                         `dark_mode` tinyint(4) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `username`, `date_created`, `dark_mode`) VALUES
(1, 'John', 'Willams', 'Jsmith', '2021-04-05 10:52:14', 0),
(2, 'Bob', 'Prime', 'Bprime', '2021-04-05 10:52:14', 1),
(3, 'Pete', 'Clock', 'Pclock', '2021-04-05 10:53:39', 0),
(4, 'Sonic', 'Hero', 'Sheross', '2021-04-05 10:53:03', 1),
(5, 'Johhny', 'Willams', 'JohnWilliams', '2021-04-06 19:11:33', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `access_tokens`
--
ALTER TABLE `access_tokens`
    ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `access_token` (`access_token`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
    ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `access_tokens`
--
ALTER TABLE `access_tokens`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
    MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
