<?php
// Run once to add image_path column to bulletins table
require_once __DIR__ . '/../connection.php';

$sql = "ALTER TABLE `bulletins` ADD COLUMN IF NOT EXISTS `image_path` VARCHAR(255) DEFAULT NULL";

// mysqli doesn't support IF NOT EXISTS for ADD COLUMN; check first
$check = mysqli_query($con, "SHOW COLUMNS FROM `bulletins` LIKE 'image_path'");
if (mysqli_num_rows($check) == 0) {
    if (mysqli_query($con, "ALTER TABLE `bulletins` ADD `image_path` VARCHAR(255) DEFAULT NULL")) {
        echo "image_path column added.\n";
    } else {
        echo "Failed to add column: " . mysqli_error($con) . "\n";
    }
} else {
    echo "image_path column already exists.\n";
}
