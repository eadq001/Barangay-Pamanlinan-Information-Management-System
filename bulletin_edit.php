<?php
session_start();
require_once 'connection.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$res = mysqli_query($con, "SELECT * FROM bulletins WHERE id = {$id} LIMIT 1");
$post = mysqli_fetch_assoc($res);
if (!$post) {
    echo "Post not found.";
    exit;
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = mysqli_real_escape_string($con, $_POST['title'] ?? '');
    $content = mysqli_real_escape_string($con, $_POST['content'] ?? '');
    $category = mysqli_real_escape_string($con, $_POST['category'] ?? 'announcement');
    $event_date = !empty($_POST['event_date']) ? mysqli_real_escape_string($con, $_POST['event_date']) : null;

    if (!$title) $errors[] = 'Title is required.';
    if (!$content) $errors[] = 'Content is required.';

    if (empty($errors)) {
        $e_date_sql = $event_date ? "event_date = '{$event_date}'," : "event_date = NULL,";
        $sql = "UPDATE bulletins SET title = '{$title}', content = '{$content}', category = '{$category}', {$e_date_sql} updated_at = NOW() WHERE id = {$id} LIMIT 1";
        if (mysqli_query($con, $sql)) {
            header('Location: bulletinBoard.php');
            exit;
        } else {
            $errors[] = 'Failed to update post: ' . mysqli_error($con);
        }
    }
}

?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Edit Bulletin</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
  <div class="max-w-3xl mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">Edit Bulletin Post</h1>
    <?php if($errors): ?>
      <div class="bg-red-100 border border-red-300 p-3 mb-4">
        <?php foreach($errors as $e) echo '<div>'.htmlspecialchars($e).'</div>'; ?>
      </div>
    <?php endif; ?>

    <form method="post" class="bg-white p-6 rounded">
      <label class="block mb-2">Title</label>
      <input name="title" value="<?php echo htmlspecialchars($post['title']); ?>" class="w-full border p-2 rounded mb-3" />

      <label class="block mb-2">Category</label>
      <select name="category" class="w-full border p-2 rounded mb-3">
        <option value="announcement" <?php echo $post['category']=='announcement'?'selected':''; ?>>Announcement</option>
        <option value="advisory" <?php echo $post['category']=='advisory'?'selected':''; ?>>Advisory</option>
        <option value="event" <?php echo $post['category']=='event'?'selected':''; ?>>Event</option>
      </select>

      <label class="block mb-2">Event Date (optional)</label>
      <input name="event_date" type="date" value="<?php echo htmlspecialchars($post['event_date']); ?>" class="w-full border p-2 rounded mb-3" />

      <label class="block mb-2">Content</label>
      <textarea name="content" rows="8" class="w-full border p-2 rounded mb-3"><?php echo htmlspecialchars($post['content']); ?></textarea>

      <div class="flex gap-2">
        <button class="bg-yellow-600 text-white px-4 py-2 rounded">Update</button>
        <a href="bulletinBoard.php" class="px-4 py-2 border rounded">Cancel</a>
      </div>
    </form>
  </div>
</body>
</html>
