<?php
require_once __DIR__ . '/../connection.php';
session_start();
$isLoggedIn = isset($_SESSION['user_id']);

$q = "SELECT c.*, 
             (SELECT COUNT(*) FROM mediations m WHERE m.case_id = c.id) AS mediations_count 
      FROM cases c 
      ORDER BY created_at DESC";
$res = mysqli_query($con, $q);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Cases & Incidents | Barangay Pamanlinan</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link rel="icon" href="../pamanlinan.png" type="image/png">
  <style>
    body {
      background: linear-gradient(to bottom right, #f4f9f9, #e8f6f7);
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .card {
      transition: all 0.3s ease;
    }
    .card:hover {
      transform: translateY(-4px);
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
    }
    .btn {
      transition: 0.3s;
    }
    .btn:hover {
      opacity: 0.9;
    }
  </style>
</head>

<body class="min-h-screen text-gray-800">

  <?php include __DIR__ . '/_header.php'; ?>

  <main class="max-w-6xl mx-auto p-6">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 border-b pb-4">
      <div>
        <h2 class="text-3xl font-semibold text-gray-700">📂 Cases & Incidents</h2>
        <p class="text-gray-500 text-sm mt-1">Track, manage, and review recorded incidents within the barangay.</p>
      </div>

      <?php if ($isLoggedIn): ?>
      <a href="create.php" class="btn mt-4 md:mt-0 bg-teal-700 text-black px-5 py-2 rounded-lg shadow hover:bg-teal-800">
        + New Case
      </a>
      <?php endif; ?>
    </div>

    <!-- Cases Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <?php if (mysqli_num_rows($res) == 0): ?>
        <div class="bg-white rounded-lg p-6 shadow-sm text-center col-span-2">
          <p class="text-gray-500">No cases found.</p>
        </div>
      <?php else: while ($r = mysqli_fetch_assoc($res)): ?>
        <div class="bg-white p-5 rounded-lg card shadow-sm border border-gray-100">
          <div class="flex justify-between items-start">
            <div>
              <h3 class="font-bold text-lg text-teal-700">
                <?php echo htmlspecialchars($r['case_number']); ?> - <?php echo htmlspecialchars($r['category']); ?>
              </h3>
              <p class="text-sm text-gray-600 mt-1">
                <span class="font-medium text-gray-700">Complainant:</span> <?php echo htmlspecialchars($r['complainant']); ?><br>
                <span class="font-medium text-gray-700">Respondent:</span> <?php echo htmlspecialchars($r['respondent']); ?>
              </p>
              <p class="text-sm text-gray-500 mt-2">
                <span class="font-medium text-gray-700">Status:</span> <?php echo htmlspecialchars($r['status']); ?> • 
                <span class="italic"><?php echo htmlspecialchars(date('M d, Y', strtotime($r['incident_date']))); ?></span>
              </p>
            </div>

            <div class="text-right">
              <a class="text-blue-600 font-medium hover:underline" href="view.php?id=<?php echo $r['id']; ?>">View</a>
              <?php if ($isLoggedIn): ?>
                <a class="ml-2 text-green-600 font-medium hover:underline" href="edit.php?id=<?php echo $r['id']; ?>">Edit</a>
                <a class="ml-2 text-red-600 font-medium hover:underline" href="delete.php?id=<?php echo $r['id']; ?>" onclick="return confirm('Are you sure you want to delete this case?')">Delete</a>
              <?php endif; ?>
            </div>
          </div>

          <p class="mt-4 text-gray-700 text-sm leading-relaxed">
            <?php echo nl2br(htmlspecialchars(substr($r['description'], 0, 250))); ?>
            <?php if (strlen($r['description']) > 250) echo '...'; ?>
          </p>

          <div class="mt-4 flex justify-between items-center text-sm text-gray-600 border-t pt-3">
            <span>🧾 Mediations: <strong><?php echo $r['mediations_count']; ?></strong></span>
            <span class="text-gray-400">Created: <?php echo date('M d, Y', strtotime($r['created_at'])); ?></span>
          </div>
        </div>
      <?php endwhile; endif; ?>
    </div>
  </main>

  <!-- Footer -->
  <footer class="text-center py-4 text-gray-500 text-sm border-t mt-10">
    © <?php echo date('Y'); ?> Barangay Pamanlinan | Case Management System
  </footer>

</body>
</html>
