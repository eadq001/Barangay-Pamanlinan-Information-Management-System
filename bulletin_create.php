<?php
session_start();
require_once 'connection.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $title = mysqli_real_escape_string($con, $_POST['title'] ?? '');
  $content = mysqli_real_escape_string($con, $_POST['content'] ?? '');
  $category = mysqli_real_escape_string($con, $_POST['category'] ?? 'announcement');
  $event_date = !empty($_POST['event_date']) ? mysqli_real_escape_string($con, $_POST['event_date']) : null;

  // Handle upload
  $image_path = null;
  if (!empty($_FILES['image']['name'])) {
    $allowed = ['image/jpeg','image/png','image/gif'];
    if (in_array($_FILES['image']['type'], $allowed) && $_FILES['image']['size'] <= 2 * 1024 * 1024) {
      $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
      $filename = uniqid('b_') . '.' . $ext;
      $uploadDir = __DIR__ . '/uploads/bulletins/';
      // ensure directory exists
      if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true) && !is_dir($uploadDir)) {
          $errors[] = 'Failed to create upload directory.';
        }
      }
      $dest = $uploadDir . $filename;
      if (empty($errors)) {
        if (move_uploaded_file($_FILES['image']['tmp_name'], $dest)) {
          $image_path = 'uploads/bulletins/' . $filename;
        } else {
          $errors[] = 'Failed to move uploaded file. Check server permissions.';
        }
      }
    } else {
      $errors[] = 'Invalid image (only JPG/PNG/GIF, max 2MB).';
    }
  }

    if (!$title) $errors[] = 'Title is required.';
    if (!$content) $errors[] = 'Content is required.';

  if (empty($errors)) {
    $author_id = $_SESSION['user_id'];
    $e_date_sql = $event_date ? "'{$event_date}'" : 'NULL';
    $img_sql = $image_path ? "'" . mysqli_real_escape_string($con, $image_path) . "'" : 'NULL';
    $sql = "INSERT INTO bulletins (title, content, category, event_date, author_id, image_path) VALUES ('{$title}', '{$content}', '{$category}', {$e_date_sql}, {$author_id}, {$img_sql})";
        if (mysqli_query($con, $sql)) {
            header('Location: bulletinBoard.php');
            exit;
        } else {
            $errors[] = 'Failed to save post: ' . mysqli_error($con);
        }
    }
}

?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Create Bulletin</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
  <div class="max-w-3xl mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">Create Bulletin Post</h1>
    <?php if($errors): ?>
      <div class="bg-red-100 border border-red-300 p-3 mb-4">
        <?php foreach($errors as $e) echo '<div>'.htmlspecialchars($e).'</div>'; ?>
      </div>
    <?php endif; ?>

  <form method="post" enctype="multipart/form-data" class="bg-white p-6 rounded">
      <label class="block mb-2">Title</label>
      <input name="title" class="w-full border p-2 rounded mb-3" />

      <label class="block mb-2">Category</label>
      <select name="category" class="w-full border p-2 rounded mb-3">
        <option value="announcement">Announcement</option>
        <option value="advisory">Advisory</option>
        <option value="event">Event</option>
      </select>

      <label class="block mb-2">Event Date (optional)</label>
      <input name="event_date" type="date" class="w-full border p-2 rounded mb-3" />

  <label class="block mb-2">Content</label>
  <textarea name="content" rows="8" class="w-full border p-2 rounded mb-3"></textarea>

  <label class="block mb-2">Image (optional)</label>
  <div class="mb-3">
    <input id="bulletin_image" type="file" name="image" accept="image/*" class="hidden" />
    <label for="bulletin_image" class="inline-flex items-center px-4 py-2 bg-white border rounded cursor-pointer text-sm">
      Choose image
    </label>
    <span id="bulletin_image_name" class="ml-3 text-sm text-gray-600">No file chosen</span>
  </div>

  <script>
    (function(){
      var inp = document.getElementById('bulletin_image');
      var nameSpan = document.getElementById('bulletin_image_name');
      inp.addEventListener('change', function(){
        var name = (this.files && this.files.length) ? this.files[0].name : 'No file chosen';
        nameSpan.textContent = name;
      });
    })();
  </script>

      <div class="flex gap-2">
        <button class="bg-green-600 text-white px-4 py-2 rounded">Save</button>
        <a href="bulletinBoard.php" class="px-4 py-2 border rounded">Cancel</a>
      </div>
    </form>
  </div>
</body>
</html>
