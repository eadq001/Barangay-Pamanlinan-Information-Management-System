<?php
require_once 'connection.php';
session_start();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$res = mysqli_query($con, "SELECT * FROM bulletins WHERE id = {$id} LIMIT 1");
$post = mysqli_fetch_assoc($res);
if (!$post) {
    echo "Post not found.";
    exit;
}

?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title><?php echo htmlspecialchars($post['title']); ?></title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
  <div class="max-w-4xl mx-auto p-6">
    <a href="bulletinBoard.php" class="text-blue-600">&larr; Back</a>
    <h1 class="text-3xl font-bold mt-4"><?php echo htmlspecialchars($post['title']); ?></h1>
    <div class="text-sm text-gray-600">Category: <?php echo htmlspecialchars($post['category']); ?> | Date: <?php echo htmlspecialchars($post['event_date'] ?: $post['created_at']); ?></div>
    <div class="mt-6 bg-white p-6 rounded text-gray-800"><?php echo nl2br(htmlspecialchars($post['content'])); ?></div>
  </div>
</body>
</html>
