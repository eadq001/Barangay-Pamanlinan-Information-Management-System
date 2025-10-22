<?php
require_once __DIR__ . '/../connection.php';

$sql = "ALTER TABLE bulletins ADD COLUMN event_time TIME NULL AFTER event_date";

if (mysqli_query($con, $sql)) {
    echo "Successfully added event_time column to bulletins table\n";
} else {
    echo "Error adding column: " . mysqli_error($con) . "\n";
}