<?php
session_start();
require_once 'connection.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id > 0) {
    mysqli_query($con, "DELETE FROM bulletins WHERE id = {$id} LIMIT 1");
}

header('Location: bulletinBoard.php');
exit;
