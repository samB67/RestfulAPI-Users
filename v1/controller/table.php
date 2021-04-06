<?php

require_once('Database.php');
require_once('../model/Response.php');

// Check database connection...
$db = new Database();
if (!$db->isConnected()){
    error_log("Connection error - ".$db->getError());
    $response = new Response();
    $response->setHttpStatusCode(500);
    $response->setSuccess(false);
    $response->addMessage("Database conection error");
    $response->send();
    exit();
}
// Create tables query...
$db->query("CREATE TABLE `access_tokens` (
  `id` int(11) NOT NULL,
  `access_token` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `access_tokens` (`id`, `access_token`, `created_at`) VALUES
(1, 'Y2Y3YTE0NDgyODdjMTgxODVlZDllZDAzMmQ3YTJiODQwZWQ2MjY1YThjNDIxMGRiMTYxNzcyODc1MA==', '2021-04-06 18:06:06'),
(2, 'MWZkMzIzYjE3MWYxMWI0YTdjZWJkYjk1N2UzZGJkN2I5YzRjOGQ3OGVjMmY1MWU3MTYxNzcyODc3OQ==', '2021-04-06 18:06:28');

CREATE TABLE `users` (
  `id` bigint(20) NOT NULL,
  `first_name` char(50) NOT NULL,
  `last_name` char(50) NOT NULL,
  `username` char(20) NOT NULL,
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `dark_mode` tinyint(4) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `users` (`id`, `first_name`, `last_name`, `username`, `date_created`, `dark_mode`) VALUES
(1, 'John', 'Willams', 'Jsmith', '2021-04-05 10:52:14', 0),
(2, 'Bob', 'Prime', 'Bprime', '2021-04-05 10:52:14', 1),
(3, 'Pete', 'Clock', 'Pclock', '2021-04-05 10:53:39', 0),
(4, 'Sonic', 'Hero', 'Sheross', '2021-04-05 10:53:03', 1),
(5, 'Johhny', 'Willams', 'JohnWilliams', '2021-04-06 19:11:33', 1);

ALTER TABLE `access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `access_token` (`access_token`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `access_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `users`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

");

$db->execute();

// if error occurred return response...
if ($db->getError() != ''){
    $response = new Response();
    $response->setHttpStatusCode(400);
    $response->setSuccess(false);
    $response->addMessage("Database not created");
    $response->send();
    exit;
}

// if no errors return success response...
$response = new Response();
$response->setHttpStatusCode(201);
$response->setSuccess(true);
$response->addMessage("Database created successfully");
$response->send();
exit;