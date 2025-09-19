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

    if (!$title) $errors[] = 'Title is required.';
    if (!$content) $errors[] = 'Content is required.';

    if (empty($errors)) {
        $author_id = $_SESSION['user_id'];
        $e_date_sql = $event_date ? "'{$event_date}'" : 'NULL';
        $sql = "INSERT INTO bulletins (title, content, category, event_date, author_id) VALUES ('{$title}', '{$content}', '{$category}', {$e_date_sql}, {$author_id})";
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

    <form method="post" class="bg-white p-6 rounded">
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

      <div class="flex gap-2">
        <button class="bg-green-600 text-white px-4 py-2 rounded">Save</button>
        <a href="bulletinBoard.php" class="px-4 py-2 border rounded">Cancel</a>
      </div>
    </form>
  </div>
</body>
</html>
