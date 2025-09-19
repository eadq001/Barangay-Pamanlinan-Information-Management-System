<?php
// Run this script once to create the bulletins table and seed a sample post.
require_once __DIR__ . '/../connection.php';

$table_sql = "CREATE TABLE IF NOT EXISTS `bulletins` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(255) NOT NULL,
    `content` TEXT NOT NULL,
    `category` VARCHAR(100) NOT NULL,
    `event_date` DATE DEFAULT NULL,
    `author_id` INT DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

if (mysqli_query($con, $table_sql)) {
    // check if there are any rows
    $res = mysqli_query($con, "SELECT COUNT(*) as c FROM `bulletins`");
    $row = mysqli_fetch_assoc($res);
    if ($row['c'] == 0) {
        $title = mysqli_real_escape_string($con, 'Welcome to the Barangay Bulletin');
        $content = mysqli_real_escape_string($con, 'This is a sample announcement to show the bulletin board subsystem. You can create, edit, and delete posts when logged in.');
        $category = 'announcement';
        $event_date = date('Y-m-d');

        $insert_sql = "INSERT INTO `bulletins` (`title`,`content`,`category`,`event_date`,`author_id`) VALUES ('{$title}','{$content}','{$category}','{$event_date}', NULL)";
        mysqli_query($con, $insert_sql);
        echo "Sample post created.\n";
    }
    echo "Migration completed successfully.\n";
} else {
    echo "Failed to create table: " . mysqli_error($con) . "\n";
}
