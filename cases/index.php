<?php
require_once __DIR__ . '/../connection.php';
session_start();
$isLoggedIn = isset($_SESSION['user_id']);

$q = "SELECT c.*, (SELECT COUNT(*) FROM mediations m WHERE m.case_id = c.id) AS mediations_count FROM cases c ORDER BY created_at DESC";
$res = mysqli_query($con, $q);
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Cases</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <style>.card{box-shadow:0 2px 8px rgba(0,0,0,0.08);}</style>
</head>
<body class="bg-gray-100 min-h-screen">
  <?php include __DIR__ . '/_header.php'; ?>
  <main class="max-w-6xl mx-auto p-6">
    <div class="flex justify-between items-center mb-4">
      <h2 class="text-2xl font-semibold">Cases & Incidents</h2>
      <a href="create.php" class="bg-green-700 text-white px-3 py-1 rounded">New Case</a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <?php if(mysqli_num_rows($res) == 0): ?>
        <div class="bg-white p-6 rounded card">No cases found.</div>
      <?php else: while($r = mysqli_fetch_assoc($res)): ?>
        <div class="bg-white p-4 rounded card">
          <div class="flex justify-between">
            <div>
              <h3 class="font-bold text-lg"><?php echo htmlspecialchars($r['case_number']); ?> - <?php echo htmlspecialchars($r['category']); ?></h3>
              <div class="text-sm text-gray-600"><?php echo htmlspecialchars($r['complainant']); ?> vs <?php echo htmlspecialchars($r['respondent']); ?></div>
              <div class="text-sm text-gray-500">Status: <?php echo htmlspecialchars($r['status']); ?> • <?php echo htmlspecialchars(date('M d, Y', strtotime($r['incident_date']))); ?></div>
            </div>
            <div class="text-right">
              <a class="text-blue-600" href="view.php?id=<?php echo $r['id']; ?>">View</a>
              <?php if($isLoggedIn): ?>
                <a class="ml-2 text-green-600" href="edit.php?id=<?php echo $r['id']; ?>">Edit</a>
                <a class="ml-2 text-red-600" href="delete.php?id=<?php echo $r['id']; ?>" onclick="return confirm('Delete case?')">Delete</a>
              <?php endif; ?>
            </div>
          </div>
          <div class="mt-3 text-gray-700"><?php echo nl2br(htmlspecialchars(substr($r['description'],0,250))); ?><?php echo (strlen($r['description'])>250)?'...':''; ?></div>
          <div class="mt-3 text-sm text-gray-600">Mediations: <?php echo $r['mediations_count']; ?></div>
        </div>
      <?php endwhile; endif; ?>
    </div>
  </main>
</body>
</html>
