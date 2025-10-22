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
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($post['title']); ?> - Bulletin View</title>
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
          <h1 class="text-xl font-bold tracking-tight">View Bulletin</h1>
        </div>
        <nav class="flex items-center space-x-4">
          <a class="hover:text-green-100 transition-colors duration-200 font-medium" href="pamanlinan.php">Dashboard</a>
          <a class="hover:text-green-100 transition-colors duration-200 font-medium" href="bulletinBoard.php">Bulletin Board</a>
          <a class="hover:text-green-100 transition-colors duration-200 font-medium" href="cases/index.php">Cases</a>
          <?php if(isset($_SESSION['user_id'])): ?>
            <a class="hover:text-green-100 transition-colors duration-200 font-medium" href="logout.php">Logout</a>
          <?php else: ?>
            <a class="bg-white text-green-700 px-4 py-2 rounded-lg shadow-sm hover:bg-green-50 transition-colors duration-200 font-medium" href="login.php">Login</a>
          <?php endif; ?>
        </nav>
      </div>
    </div>
  </header>

  <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <section class="mb-8">
      <div class="flex justify-between items-center">
        <h2 class="text-3xl font-bold text-gray-900"><?php echo htmlspecialchars($post['title']); ?></h2>
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

    <section class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      <div class="lg:col-span-2">
        <article class="bg-white p-8 rounded-xl card">
          <div class="flex items-center space-x-2 text-sm text-gray-500 mb-6">
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
              <?php echo htmlspecialchars(ucfirst($post['category'])); ?>
            </span>
           
            <?php if($post['event_date']): ?>
              <span>•</span>
              <div class="flex items-center text-green-600">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span>Event: <?php echo htmlspecialchars(date('M d, Y', strtotime($post['event_date']))); ?>
                <?php if($post['event_time']): ?>
                  at <?php echo date('g:i A', strtotime($post['event_time'])); ?>
                <?php endif; ?>
                </span>
              </div>
            <?php endif; ?>
          </div>

          <?php if (!empty($post['image_path'])): ?>
            <div class="mb-6 rounded-lg overflow-hidden">
              <img 
                src="<?php echo htmlspecialchars($post['image_path']); ?>" 
                alt="Image for <?php echo htmlspecialchars($post['title']); ?>" 
                class="w-full h-auto object-cover rounded-lg" 
              />
            </div>
          <?php endif; ?>

          <div class="prose prose-green max-w-none">
            <?php echo nl2br(htmlspecialchars($post['content'])); ?>
          </div>

          <?php if(isset($_SESSION['user_id'])): ?>
            <div class="mt-8 flex items-center space-x-4 border-t pt-6">
              <a 
                href="bulletin_edit.php?id=<?php echo $post['id']; ?>" 
                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
              >
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edit Post
              </a>
              <button 
                type="button"
                onclick="openDeleteModal(<?php echo $post['id']; ?>, '<?php echo addslashes($post['title']); ?>')"
                class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
              >
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                Delete Post
              </button>
            </div>
          <?php endif; ?>
        </article>
      </div>

      <aside class="space-y-6">
        <!-- Post Information Card -->
        <div class="bg-white rounded-xl card p-6">
          <div class="flex items-center space-x-2 mb-4">
            <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <h4 class="text-lg font-bold text-gray-900">Post Information</h4>
          </div>
          <dl class="space-y-3">
            <div>
              <dt class="text-sm font-medium text-gray-500">Posted on</dt>
              <dd class="mt-1 text-sm text-gray-900"><?php echo date('F j, Y \a\t g:i A', strtotime($post['created_at'])); ?></dd>
            </div>
            <div>
              <dt class="text-sm font-medium text-gray-500">Category</dt>
              <dd class="mt-1 text-sm text-gray-900"><?php echo ucfirst(htmlspecialchars($post['category'])); ?></dd>
            </div>
            <?php if($post['event_date']): ?>
            <div>
              <dt class="text-sm font-medium text-gray-500">Event Details</dt>
              <dd class="mt-1 text-sm text-gray-900">
                <?php echo date('l, F j, Y', strtotime($post['event_date'])); ?>
                <?php if($post['event_time']): ?>
                  at <?php echo date('g:i A', strtotime($post['event_time'])); ?>
                <?php endif; ?>
              </dd>
            </div>
            <?php endif; ?>
          </dl>
        </div>

    </section>
  </main>

  <footer class="bg-white border-t mt-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
      <div class="text-center text-sm text-gray-500">
        © <?php echo date('Y'); ?> Barangay Pamanlinan. All rights reserved.
      </div>
    </div>
  </footer>

  <!-- Delete Confirmation Modal -->
  <div id="deleteModal" class="fixed z-10 inset-0 hidden">
    <!-- Modal Backdrop -->
    <div class="absolute inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
    
    <!-- Modal Content -->
    <div class="fixed inset-0 z-10 overflow-y-auto">
      <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
        <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
          <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
            <div class="sm:flex sm:items-start">
              <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                </svg>
              </div>
              <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                <h3 class="text-lg font-semibold leading-6 text-gray-900" id="modal-title">Delete Bulletin</h3>
                <div class="mt-2">
                  <p class="text-sm text-gray-500">Are you sure you want to delete this bulletin? This action cannot be undone.</p>
                  <p class="mt-2 text-sm font-medium text-gray-900" id="deleteItemTitle"></p>
                </div>
              </div>
            </div>
          </div>
          <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
            <a id="confirmDelete" href="#" 
               class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:ml-3 sm:w-auto">
              Delete
            </a>
            <button type="button" 
                    onclick="closeDeleteModal()"
                    class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">
              Cancel
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    function openDeleteModal(id, title) {
      document.getElementById('deleteModal').classList.remove('hidden');
      document.getElementById('deleteItemTitle').textContent = title;
      document.getElementById('confirmDelete').href = 'bulletin_delete.php?id=' + id;
    }

    function closeDeleteModal() {
      document.getElementById('deleteModal').classList.add('hidden');
    }

    // Close modal when clicking outside
    document.getElementById('deleteModal').addEventListener('click', function(e) {
      if (e.target === this) {
        closeDeleteModal();
      }
    });

    // Close modal on escape key press
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape' && !document.getElementById('deleteModal').classList.contains('hidden')) {
        closeDeleteModal();
      }
    });
  </script>
</body>
</html>
