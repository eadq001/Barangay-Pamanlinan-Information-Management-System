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
  <style>
    .card { box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
  </style>
</head>
<body class="bg-gray-100 min-h-screen">
  <header class="bg-green-700 text-white p-4">
    <div class="max-w-6xl mx-auto flex justify-between items-center">
      <h1 class="text-xl font-bold">Barangay Bulletin Board</h1>
      <nav class="space-x-4">
        <a class="font-semibold" href="pamanlinan.php">Dashboard</a>
        <?php if($isLoggedIn): ?>
          <a class="bg-white text-green-700 px-3 py-1 rounded" href="bulletin_create.php">Create Post</a>
          <a class="" href="logout.php">Logout</a>
        <?php else: ?>
          <a class="bg-white text-green-700 px-3 py-1 rounded" href="login.php">Login</a>
        <?php endif; ?>
      </nav>
    </div>
  </header>

  <main class="max-w-6xl mx-auto p-6">
    <section class="mb-6">
      <div class="flex justify-between items-center">
        <h2 class="text-2xl font-semibold">Announcements & Notices</h2>
        <div>
          <a href="bulletinBoard.php" class="text-sm text-gray-600">Refresh</a>
        </div>
      </div>
    </section>

    <section class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div class="md:col-span-2">
        <?php if(mysqli_num_rows($result) == 0): ?>
          <div class="bg-white p-6 rounded card">No posts found.</div>
        <?php else: ?>
          <?php while($row = mysqli_fetch_assoc($result)): ?>
            <article class="bg-white p-4 rounded mb-4 card">
              <div>
                <div>
                  <h3 class="text-lg font-bold"><?php echo htmlspecialchars($row['title']); ?></h3>
                  <div class="text-sm text-gray-500">Category: <?php echo htmlspecialchars($row['category']); ?> • <?php echo htmlspecialchars(date('M d, Y', strtotime($row['created_at']))); ?></div>
                </div>

                <?php if (!empty($row['image_path'])): ?>
                  <div class="mt-3 rounded overflow-hidden">
                    <img src="<?php echo htmlspecialchars($row['image_path']); ?>" alt="post image" class="w-full object-cover" style="max-height:600px;" />
                  </div>
                <?php endif; ?>

                <div class="mt-3 text-gray-700">
                  <p><?php echo nl2br(htmlspecialchars(substr($row['content'], 0, 800))); ?><?php echo (strlen($row['content'])>800)?'...':''; ?></p>
                </div>

                <div class="mt-3 text-sm text-gray-600">
                  <a href="bulletin_view.php?id=<?php echo $row['id']; ?>" class="mr-3">View</a>
                  <?php if($isLoggedIn): ?>
                    <a href="bulletin_edit.php?id=<?php echo $row['id']; ?>" class="mr-3">Edit</a>
                    <a href="bulletin_delete.php?id=<?php echo $row['id']; ?>" class="text-red-600" onclick="return confirm('Delete this post?')">Delete</a>
                  <?php endif; ?>
                </div>
              </div>
            </article>
          <?php endwhile; ?>
        <?php endif; ?>
      </div>

      <aside class="bg-white p-6 rounded card">
        <h4 class="font-semibold mb-2">Quick Calendar</h4>
        <div class="text-sm text-gray-600">(Calendar stub / events list)</div>
        <ul class="mt-3 list-disc list-inside text-gray-700">
          <?php
            // List upcoming events (event_date not null)
            $ev_q = mysqli_query($con, "SELECT id, title, event_date FROM bulletins WHERE event_date IS NOT NULL ORDER BY event_date ASC LIMIT 5");
            while($e = mysqli_fetch_assoc($ev_q)){
                echo '<li><a class="text-blue-600" href="bulletin_view.php?id='. $e['id'] .'">'.htmlspecialchars($e['title']).' ('.htmlspecialchars($e['event_date']).')</a></li>';
            }
          ?>
        </ul>
      </aside>
    </section>
  </main>

</body>
</html>
