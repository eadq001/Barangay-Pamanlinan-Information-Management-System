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
    $event_time = !empty($_POST['event_time']) ? mysqli_real_escape_string($con, $_POST['event_time']) : null;

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
      $uploadDir = __DIR__ . '/uploads/bulletins/';
      if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true) && !is_dir($uploadDir)) {
          $errors[] = 'Failed to create upload directory.';
        }
      }
      $dest = $uploadDir . $filename;
      if (empty($errors)) {
        if (move_uploaded_file($_FILES['image']['tmp_name'], $dest)) {
          $new_image_path = 'uploads/bulletins/' . $filename;
        } else {
          $errors[] = 'Failed to move uploaded file. Check server permissions.';
        }
      }
    } else {
      $errors[] = 'Invalid image (only JPG/PNG/GIF, max 2MB).';
    }
  }

  if (empty($errors)) {
    $e_date_sql = $event_date ? "event_date = '{$event_date}'," : "event_date = NULL,";
    $e_time_sql = $event_time ? "event_time = '{$event_time}'," : "event_time = NULL,";
    $img_sql = $new_image_path ? "image_path = '" . mysqli_real_escape_string($con, $new_image_path) . "'," : "image_path = NULL,";
    $sql = "UPDATE bulletins SET title = '{$title}', content = '{$content}', category = '{$category}', {$e_date_sql} {$e_time_sql} {$img_sql} updated_at = NOW() WHERE id = {$id} LIMIT 1";
        if (mysqli_query($con, $sql)) {
            header('Location: bulletinBoard.php');
            exit;
        } else {
            $errors[] = 'Failed to update post: ' . mysqli_error($con);
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Bulletin</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Inter', sans-serif; }
    .card { box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06); transition: all 0.3s ease; }
    .card:hover { box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -2px rgba(0,0,0,0.05); }
  </style>
</head>
<body class="bg-gray-50 min-h-screen">
  <header class="bg-gradient-to-r from-green-600 to-green-700 text-white shadow-lg">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center h-16">
        <div class="flex items-center space-x-2">
          <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
          </svg>
          <h1 class="text-xl font-bold tracking-tight">Edit Bulletin</h1>
        </div>
        <nav class="flex items-center space-x-4">
          <a class="hover:text-green-100 transition-colors duration-200 font-medium" href="pamanlinan.php">Dashboard</a>
          <a class="hover:text-green-100 transition-colors duration-200 font-medium" href="bulletinBoard.php">Bulletin Board</a>
          <a class="hover:text-green-100 transition-colors duration-200 font-medium" href="cases/index.php">Cases</a>
          <a class="hover:text-green-100 transition-colors duration-200 font-medium" href="logout.php">Logout</a>
        </nav>
      </div>
    </div>
  </header>

  <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <section class="mb-8">
      <div class="flex justify-between items-center">
        <h2 class="text-3xl font-bold text-gray-900">Edit Post</h2>
        <div>
          <a href="bulletinBoard.php" class="inline-flex items-center text-sm text-green-600 hover:text-green-700 transition-colors duration-200">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Bulletin Board
          </a>
        </div>
      </div>
    </section>

    <?php if($errors): ?>
      <div class="bg-red-100 border border-red-300 text-red-700 p-4 rounded-lg mb-6">
        <?php foreach($errors as $e) echo '<div class="mb-1">'.htmlspecialchars($e).'</div>'; ?>
      </div>
    <?php endif; ?>

    <section class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      <div class="lg:col-span-2">
        <form method="post" enctype="multipart/form-data" class="bg-white p-8 rounded-xl card">
          <div class="space-y-6">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Title</label>
              <input 
                name="title" 
                value="<?php echo htmlspecialchars($post['title']); ?>"
                required
                class="w-full border border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 p-3" 
                placeholder="Enter bulletin title"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
              <select 
                name="category" 
                class="w-full border border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 p-3"
              >
                <option value="announcement" <?php echo $post['category']=='announcement'?'selected':''; ?>>Announcement</option>
                <option value="advisory" <?php echo $post['category']=='advisory'?'selected':''; ?>>Advisory</option>
                <option value="event" <?php echo $post['category']=='event'?'selected':''; ?>>Event</option>
              </select>
            </div>

            <div class="grid grid-colspan-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Event Date (optional)</label>
                <input 
                  name="event_date" 
                  type="date" 
                  value="<?php echo htmlspecialchars($post['event_date']); ?>"
                  class="w-full border border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 p-3" 
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Event Time (optional)</label>
                <input 
                  name="event_time" 
                  type="time"
                  value="<?php echo htmlspecialchars($post['event_time']); ?>"
                  class="w-full border border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 p-3" 
                />

                <div class = "grid grid-cols-2" >
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Content</label>
              <textarea 
                name="content" 
                rows="20" 
                required
                class="w-full border border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 p-3"
                placeholder="Enter bulletin content..."
              ><?php echo htmlspecialchars($post['content']); ?></textarea>
            </div>

            <?php if (!empty($post['image_path'])): ?>
              <div class="rounded-lg overflow-hidden p-4">
                <div class="mb-4 mt-3">
                  <img 
                    src="<?php echo htmlspecialchars($post['image_path']); ?>" 
                    class="max-w-sm rounded-lg mx-auto" 
                    alt="Current post image" 
                  />
                </div>
                <label class="flex items-center space-x-2 text-sm text-red-600">
                  <input type="checkbox" name="remove_image" value="1" class="rounded border-red-300 text-red-600 focus:ring-red-500" />
                  <span>Remove current image</span>
                </label>
              </div>
            <?php endif; ?>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                <?php echo !empty($post['image_path']) ? 'Replace Image' : 'Add Image'; ?> (optional)
              </label>
              <div class="mt-1 flex items-center">
                <input id="bulletin_image" type="file" name="image" accept="image/*" class="hidden" />
                <label 
                  for="bulletin_image" 
                  class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 cursor-pointer"
                >
                  <svg class="-ml-1 mr-2 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                  </svg>
                  Choose image
                </label>
                <span id="bulletin_image_name" class="ml-3 text-sm text-gray-500">No file chosen</span>
              </div>
              <p class="mt-1 text-sm text-gray-500">
                Allowed formats: JPG, PNG, GIF (max. 2MB)
              </p>
            </div>

            <div class="flex items-center justify-end space-x-3 border-t pt-6">
              <a 
                href="bulletinBoard.php" 
                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
              >
                Cancel
              </a>
              <button 
                type="submit"
                class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
              >
                <svg class="h-4 w-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                </svg>
                Update Post
              </button>
            </div>
          </div>
        </form>
      </div>
      </div>

      <aside class="space-y-6">
        <!-- Edit Guidelines Card -->
        <div class="bg-white rounded-xl card p-6">
          <div class="flex items-center space-x-2 mb-4">
            <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <h4 class="text-lg font-bold text-gray-900">Editing Guidelines</h4>
          </div>
          <ul class="space-y-3 text-sm text-gray-600">
            <li class="flex items-start">
              <svg class="h-5 w-5 text-green-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
              </svg>
              <span>Review all information before updating</span>
            </li>
            <li class="flex items-start">
              <svg class="h-5 w-5 text-green-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
              </svg>
              <span>You can change or remove images</span>
            </li>
            <li class="flex items-start">
              <svg class="h-5 w-5 text-green-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
              </svg>
              <span>Event dates can be in the past</span>
            </li>
          </ul>
        </div>

        <!-- Post History Card -->
        <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl card p-6">
          <div class="flex items-center space-x-2 mb-4">
            <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <h4 class="text-lg font-bold text-gray-900">Post History</h4>
          </div>
          <dl class="space-y-2 text-sm">
            <div>
              <dt class="text-gray-500">Created</dt>
              <dd class="font-medium text-gray-900"><?php echo date('M d, Y', strtotime($post['created_at'])); ?></dd>
            </div>
            <?php if($post['updated_at']): ?>
            <div>
              <dt class="text-gray-500">Last Updated</dt>
              <dd class="font-medium text-gray-900"><?php echo date('M d, Y', strtotime($post['updated_at'])); ?></dd>
            </div>
            <?php endif; ?>
          </dl>
        </div>
      </aside>
    </section>
  </main>

  <footer class="bg-white border-t mt-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
      <div class="text-center text-sm text-gray-500">
        © <?php echo date('Y'); ?> Barangay Pamanlinan. All rights reserved.
      </div>
    </div>
  </footer>

  <script>
    (function(){
      // File input handler
      var inp = document.getElementById('bulletin_image');
      var nameSpan = document.getElementById('bulletin_image_name');
      inp.addEventListener('change', function(){
        var name = (this.files && this.files.length) ? this.files[0].name : 'No file chosen';
        nameSpan.textContent = name;
      });

      // Event date validation
      var dateInput = document.querySelector('input[type="date"]');
      var originalDate = dateInput.value; // Store the original date if it exists
      var today = new Date().toISOString().split('T')[0];
      
      dateInput.addEventListener('input', function() {
        var selectedDate = this.value;
        
        // If there was no original date and user selects a past date, reset to today
        if (!originalDate && selectedDate < today) {
          this.value = today;
        }
        // If there was an original date:
        // - Allow keeping the original date
        // - For new dates, don't allow dates before today
        else if (originalDate && selectedDate !== originalDate && selectedDate < today) {
          this.value = today;
        }
      });

      // Set min attribute to today for new date selections
      // but only if there wasn't an original date
      if (!originalDate) {
        dateInput.setAttribute('min', today);
      }
    })();
  </script>
</body>
</html>
