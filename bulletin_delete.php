<?php
session_start();
require_once 'connection.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id > 0) {
    // fetch image path to remove file
    $res = mysqli_query($con, "SELECT image_path FROM bulletins WHERE id = {$id} LIMIT 1");
    $row = mysqli_fetch_assoc($res);
    if (!empty($row['image_path']) && file_exists(__DIR__ . '/' . $row['image_path'])) {
        @unlink(__DIR__ . '/' . $row['image_path']);
    }
    mysqli_query($con, "DELETE FROM bulletins WHERE id = {$id} LIMIT 1");
}

header('Location: bulletinBoard.php');
exit;
