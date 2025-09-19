<?php
require_once __DIR__ . '/../connection.php';
session_start();
if(!isset($_SESSION['user_id'])){ header('Location: ../login.php'); exit; }
$id = intval($_GET['id'] ?? 0);
$q = mysqli_query($con, "SELECT status FROM cases WHERE id={$id} LIMIT 1");
if(mysqli_num_rows($q) == 0){ header('Location: index.php'); exit; }
$s = mysqli_fetch_assoc($q)['status'];
if(mysqli_query($con, "DELETE FROM cases WHERE id={$id}")){
    mysqli_query($con, "UPDATE case_status_counts SET count = count - 1 WHERE status='".mysqli_real_escape_string($con,$s)."'");
}
header('Location: index.php'); exit;
