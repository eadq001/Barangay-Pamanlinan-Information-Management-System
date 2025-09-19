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

  // Handle image upload or removal
  $new_image_path = $post['image_path'];
  if (isset($_POST['remove_image']) && $_POST['remove_image'] == '1') {
    // remove existing
    if (!empty($post['image_path']) && file_exists(__DIR__ . '/' . $post['image_path'])) {
      @unlink(__DIR__ . '/' . $post['image_path']);
    }
    $new_image_path = null;
  }

  if (!empty($_FILES['image']['name'])) {
    $allowed = ['image/jpeg','image/png','image/gif'];
    if (in_array($_FILES['image']['type'], $allowed) && $_FILES['image']['size'] <= 2 * 1024 * 1024) {
      // remove old if exists
      if (!empty($post['image_path']) && file_exists(__DIR__ . '/' . $post['image_path'])) {
        @unlink(__DIR__ . '/' . $post['image_path']);
      }
      $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
      $filename = uniqid('b_') . '.' . $ext;
      $dest = __DIR__ . '/uploads/bulletins/' . $filename;
      if (move_uploaded_file($_FILES['image']['tmp_name'], $dest)) {
        $new_image_path = 'uploads/bulletins/' . $filename;
      }
    } else {
      $errors[] = 'Invalid image (only JPG/PNG/GIF, max 2MB).';
    }
  }

  if (empty($errors)) {
    $e_date_sql = $event_date ? "event_date = '{$event_date}'," : "event_date = NULL,";
    $img_sql = $new_image_path ? "image_path = '" . mysqli_real_escape_string($con, $new_image_path) . "'," : "image_path = NULL,";
    $sql = "UPDATE bulletins SET title = '{$title}', content = '{$content}', category = '{$category}', {$e_date_sql} {$img_sql} updated_at = NOW() WHERE id = {$id} LIMIT 1";
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

  <form method="post" enctype="multipart/form-data" class="bg-white p-6 rounded">
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

      <?php if (!empty($post['image_path'])): ?>
        <div class="mb-3">
          <img src="<?php echo htmlspecialchars($post['image_path']); ?>" style="max-width:200px; display:block;" alt="current image" />
          <label><input type="checkbox" name="remove_image" value="1" /> Remove image</label>
        </div>
      <?php endif; ?>

      <label class="block mb-2">Replace Image (optional)</label>
      <input type="file" name="image" accept="image/*" class="mb-3" />

      <div class="flex gap-2">
        <button class="bg-yellow-600 text-white px-4 py-2 rounded">Update</button>
        <a href="bulletinBoard.php" class="px-4 py-2 border rounded">Cancel</a>
      </div>
    </form>
  </div>
</body>
</html>
