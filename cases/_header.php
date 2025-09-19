<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../connection.php';
$isLoggedIn = isset($_SESSION['user_id']);
?>
<header class="bg-green-700 text-white p-4">
  <div class="max-w-6xl mx-auto flex justify-between items-center">
    <h1 class="text-xl font-bold">Barangay Case Management</h1>
    <nav class="space-x-4">
      <a class="font-semibold" href="../pamanlinan.php">Dashboard</a>
      <a class="font-semibold" href="../bulletinBoard.php">Bulletins</a>
      <?php if($isLoggedIn): ?>
        <a class="bg-white text-green-700 px-3 py-1 rounded" href="create.php">Create Case</a>
        <a class="" href="../logout.php">Logout</a>
      <?php else: ?>
        <a class="bg-white text-green-700 px-3 py-1 rounded" href="../login.php">Login</a>
      <?php endif; ?>
    </nav>
  </div>
</header>
