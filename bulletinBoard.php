<?php
session_start();
require_once 'connection.php';

$isLoggedIn = isset($_SESSION['user_id']);

$query = "SELECT * FROM `bulletins` ORDER BY created_at DESC";
$result = mysqli_query($con, $query);

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Bulletin Board</title>
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
          <h1 class="text-xl font-bold tracking-tight">Barangay Bulletin Board</h1>
        </div>
        <nav class="flex items-center space-x-4">
          <a class="hover:text-green-100 transition-colors duration-200 font-medium" href="pamanlinan.php">Dashboard</a>
          <a class="hover:text-green-100 transition-colors duration-200 font-medium" href="cases/index.php">Cases</a>
          <?php if($isLoggedIn): ?>
            <a class="bg-white text-green-700 px-4 py-2 rounded-lg shadow-sm hover:bg-green-50 transition-colors duration-200 font-medium" href="bulletin_create.php">Create Post</a>
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
        <h2 class="text-3xl font-bold text-gray-900">Announcements & Notices</h2>
        <div>
          <a href="bulletinBoard.php" class="inline-flex items-center text-sm text-green-600 hover:text-green-700 transition-colors duration-200">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            Refresh
          </a>
        </div>
      </div>
    </section>

    <section class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      <div class="lg:col-span-2">
        <?php if(mysqli_num_rows($result) == 0): ?>
          <div class="bg-white p-8 rounded-lg card text-center text-gray-500">
            <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
            <p class="text-lg">No posts found.</p>
          </div>
        <?php else: ?>
          <?php while($row = mysqli_fetch_assoc($result)): ?>
            <article class="bg-white rounded-xl card mb-6 overflow-hidden">
              <?php if (!empty($row['image_path'])): ?>
                <div class="aspect-w-16 aspect-h-9 relative">
                  <img src="<?php echo htmlspecialchars($row['image_path']); ?>" 
                       alt="Post image for <?php echo htmlspecialchars($row['title']); ?>" 
                       class="w-full h-64 object-cover" />
                </div>
              <?php endif; ?>
              
              <div class="p-6">
                <div class="flex items-start justify-between">
                  <div class="flex-1">
                    <h3 class="text-xl font-bold text-gray-900 mb-2">
                      <?php echo htmlspecialchars($row['title']); ?>
                    </h3>
                    <div class="flex items-center space-x-2 text-sm text-gray-500 mb-4">
                      <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        <?php echo htmlspecialchars(ucfirst($row['category'])); ?>
                      </span>
                      <span>•</span>
                      <time datetime="<?php echo $row['created_at']; ?>">
                        <?php echo htmlspecialchars(date('M d, Y', strtotime($row['created_at']))); ?>
                      </time>
                    </div>
                  </div>
                </div>

                <div class="prose prose-green max-w-none text-gray-600">
                  <p><?php echo nl2br(htmlspecialchars(substr($row['content'], 0, 800))); ?>
                  <?php if(strlen($row['content']) > 800): ?>
                    <span class="text-green-600">...</span>
                  <?php endif; ?></p>
                </div>

                <div class="mt-6 flex items-center space-x-4 text-sm">
                  <a href="bulletin_view.php?id=<?php echo $row['id']; ?>" 
                     class="inline-flex items-center text-green-600 hover:text-green-700 font-medium transition-colors duration-200">
                    <span>Read more</span>
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                  </a>
                  <?php if($isLoggedIn): ?>
                    <a href="bulletin_edit.php?id=<?php echo $row['id']; ?>" 
                       class="text-gray-500 hover:text-gray-700 transition-colors duration-200">Edit</a>
                    <a href="bulletin_delete.php?id=<?php echo $row['id']; ?>" 
                       class="text-red-600 hover:text-red-700 transition-colors duration-200"
                       onclick="return confirm('Are you sure you want to delete this post?')">Delete</a>
                  <?php endif; ?>
                </div>
              </div>
            </article>
          <?php endwhile; ?>
        <?php endif; ?>
      </div>

      <aside class="space-y-6">
        <!-- Upcoming Events Card -->
        <div class="bg-white rounded-xl card p-6">
          <div class="flex items-center justify-between mb-6">
            <h4 class="text-lg font-bold text-gray-900">Upcoming Events</h4>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
              Next 5
            </span>
          </div>
          
          <?php
            $ev_q = mysqli_query($con, "SELECT id, title, event_date FROM bulletins WHERE event_date IS NOT NULL ORDER BY event_date ASC LIMIT 5");
            if(mysqli_num_rows($ev_q) == 0): 
          ?>
            <div class="text-center py-4 text-gray-500">
              <svg class="w-8 h-8 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
              <p>No upcoming events</p>
            </div>
          <?php else: ?>
            <div class="space-y-4">
              <?php while($e = mysqli_fetch_assoc($ev_q)): ?>
                <div class="flex items-start space-x-4 p-3 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                  <div class="flex-shrink-0 w-12 text-center">
                    <div class="text-xs font-medium text-gray-500"><?php echo date('M', strtotime($e['event_date'])); ?></div>
                    <div class="text-xl font-bold text-green-600"><?php echo date('d', strtotime($e['event_date'])); ?></div>
                  </div>
                  <div class="min-w-0 flex-1">
                    <a href="bulletin_view.php?id=<?php echo $e['id']; ?>" class="block">
                      <p class="text-sm font-medium text-gray-900 hover:text-green-600 truncate transition-colors duration-200">
                        <?php echo htmlspecialchars($e['title']); ?>
                      </p>
                      <p class="text-xs text-gray-500">
                        <?php echo date('l', strtotime($e['event_date'])); ?>
                      </p>
                    </a>
                  </div>
                </div>
              <?php endwhile; ?>
            </div>
          <?php endif; ?>
        </div>

        <!-- Quick Stats Card -->
        <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl card p-6">
          <h4 class="text-lg font-bold text-gray-900 mb-4">Bulletin Stats</h4>
          <?php
            $stats_q = mysqli_query($con, "SELECT 
              COUNT(*) as total,
              COUNT(CASE WHEN DATE(created_at) = CURDATE() THEN 1 END) as today,
              COUNT(CASE WHEN event_date IS NOT NULL THEN 1 END) as events
              FROM bulletins");
            $stats = mysqli_fetch_assoc($stats_q);
          ?>
          <div class="grid grid-cols-3 gap-4 text-center">
            <div class="p-3">
              <div class="text-2xl font-bold text-green-600"><?php echo $stats['total']; ?></div>
              <div class="text-xs text-gray-600">Total Posts</div>
            </div>
            <div class="p-3">
              <div class="text-2xl font-bold text-green-600"><?php echo $stats['today']; ?></div>
              <div class="text-xs text-gray-600">Today's Posts</div>
            </div>
            <div class="p-3">
              <div class="text-2xl font-bold text-green-600"><?php echo $stats['events']; ?></div>
              <div class="text-xs text-gray-600">Events</div>
            </div>
          </div>
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
</body>
</html>
