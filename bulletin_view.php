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
  <div class="max-w-3xl mx-auto p-6">
    <a href="bulletinBoard.php" class="text-blue-600">&larr; Back</a>
    <div class="bg-white p-6 rounded mt-4">
      <div>
        <div class="flex justify-between items-start">
          <div>
            <div class="font-semibold text-xl"><?php echo htmlspecialchars($post['title']); ?></div>
            <div class="text-sm text-gray-500"><?php echo htmlspecialchars(date('M d, Y', strtotime($post['created_at']))); ?></div>
          </div>
          <div class="text-sm text-gray-600">
            <?php if(isset($_SESSION['user_id'])): ?>
              <a href="bulletin_edit.php?id=<?php echo $post['id']; ?>" class="mr-2">Edit</a>
              <a href="bulletin_delete.php?id=<?php echo $post['id']; ?>" class="text-red-600" onclick="return confirm('Delete this post?')">Delete</a>
            <?php endif; ?>
          </div>
        </div>

        <?php if (!empty($post['image_path'])): ?>
          <div class="mt-4 mb-4">
            <img src="<?php echo htmlspecialchars($post['image_path']); ?>" alt="post image" class="w-full object-cover rounded" style="max-height:800px;" />
          </div>
        <?php endif; ?>

        <div class="text-gray-800"><?php echo nl2br(htmlspecialchars($post['content'])); ?></div>
      </div>
    </div>
  </div>
</body>
</html>
