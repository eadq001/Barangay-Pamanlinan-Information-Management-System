<?php
require_once __DIR__ . '/../connection.php';
session_start();
$id = intval($_GET['id'] ?? 0);
$q = mysqli_query($con, "SELECT * FROM cases WHERE id={$id} LIMIT 1");
if(mysqli_num_rows($q) == 0){ echo 'Case not found'; exit; }
$case = mysqli_fetch_assoc($q);
$med_q = mysqli_query($con, "SELECT * FROM mediations WHERE case_id={$id} ORDER BY meeting_date DESC");

// detect if mediations table has a `new_status` column (safe for older DBs)
$has_new_status = false;
$col_check = mysqli_query($con, "SHOW COLUMNS FROM mediations LIKE 'new_status'");
if($col_check && mysqli_num_rows($col_check) > 0){ $has_new_status = true; }

// handle mediation submission
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])){
  $mediator = mysqli_real_escape_string($con, $_POST['mediator']);
  $meeting_date = $_POST['meeting_date'] ?: null;
  $outcome = mysqli_real_escape_string($con, $_POST['outcome']);
  // build insert and include new_status column only if it exists in DB
  $insert_cols = "case_id, mediator, meeting_date, outcome";
  $insert_vals = "{$id},'{$mediator}',".($meeting_date?"'{$meeting_date}'":"NULL").",'{$outcome}'";
  if($has_new_status){
    $insert_cols .= ", new_status";
    $new_status_post = $_POST['new_status'] ?? '';
    if(!empty($new_status_post) && in_array($new_status_post, ['Open','Resolved','Endorsed'])){
      $insert_vals .= ", '".mysqli_real_escape_string($con, $new_status_post)."'";
    } else {
      $insert_vals .= ", NULL";
    }
  }
  $insert = "INSERT INTO mediations ({$insert_cols}) VALUES ({$insert_vals})";
  if(mysqli_query($con, $insert)){
    // optionally update case status (based on submitted new_status)
    if(!empty($_POST['new_status']) && in_array($_POST['new_status'], ['Open','Resolved','Endorsed'])){
      $new_status = mysqli_real_escape_string($con, $_POST['new_status']);
      if($new_status !== $case['status']){
        mysqli_query($con, "UPDATE case_status_counts SET count = count - 1 WHERE status='".mysqli_real_escape_string($con,$case['status'])."'");
        mysqli_query($con, "UPDATE case_status_counts SET count = count + 1 WHERE status='".mysqli_real_escape_string($con,$new_status)."'");
        mysqli_query($con, "UPDATE cases SET status='{$new_status}' WHERE id={$id}");
      }
    }
    header('Location: view.php?id='.$id); exit;
  } else {
    $med_error = mysqli_error($con);
  }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>View Case</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen">
  <?php include __DIR__ . '/_header.php'; ?>
  <main class="max-w-3xl mx-auto p-6 bg-white rounded">
    <h2 class="text-xl font-semibold"><?php echo htmlspecialchars($case['case_number']); ?> - <?php echo htmlspecialchars($case['category']); ?></h2>
    <div class="text-sm text-gray-600"><?php echo htmlspecialchars($case['complainant']); ?> vs <?php echo htmlspecialchars($case['respondent']); ?></div>
    <div class="mt-3"><?php echo nl2br(htmlspecialchars($case['description'])); ?></div>
    <div class="mt-3 text-sm text-gray-600">Status: <?php echo htmlspecialchars($case['status']); ?></div>

    <section class="mt-6">
      <h3 class="font-semibold">Mediations</h3>
      <?php if(mysqli_num_rows($med_q) == 0): ?>
        <div class="text-sm text-gray-600">No mediations recorded.</div>
      <?php else: while($m = mysqli_fetch_assoc($med_q)): ?>
        <div class="border p-2 mt-2">
          <?php
            $mediator = htmlspecialchars($m['mediator'] ?? '');
            $meeting_raw = $m['meeting_date'] ?? null;
            $meeting_fmt = $meeting_raw ? date('M d, Y H:i', strtotime($meeting_raw)) : 'N/A';
            $outcome = nl2br(htmlspecialchars($m['outcome'] ?? ''));
            $m_new_status = '';
            if(!empty($m['new_status'])){ $m_new_status = htmlspecialchars($m['new_status']); }
          ?>
          <div class="text-sm text-gray-600"><?php echo $mediator; ?> — <?php echo $meeting_fmt; ?> <?php if($m_new_status): ?><span class="ml-2 px-2 py-0.5 bg-green-100 text-green-800 text-xs rounded"><?php echo $m_new_status; ?></span><?php endif; ?></div>
          <div class="mt-1"><?php echo $outcome; ?></div>
        </div>
      <?php endwhile; endif; ?>
    </section>

    <?php if(isset($_SESSION['user_id'])): ?>
      <section class="mt-6">
        <h3 class="font-semibold">Add Mediation</h3>
        <?php if(!empty($med_error)): ?><div class="bg-red-100 text-red-700 p-2"><?php echo htmlspecialchars($med_error); ?></div><?php endif; ?>
        <form method="post" class="mt-2">
          <div class="mb-2"><label class="block">Mediator<input name="mediator" class="w-full p-2 border" required /></label></div>
          <div class="mb-2"><label class="block">Meeting Date<input type="datetime-local" name="meeting_date" class="w-full p-2 border" /></label></div>
          <div class="mb-2"><label class="block">Outcome<textarea name="outcome" class="w-full p-2 border"></textarea></label></div>
          <?php if($has_new_status): ?>
            <div class="mb-2"><label class="block">New Case Status<select name="new_status" class="w-full p-2 border"><option value="">(no change)</option><option>Open</option><option>Resolved</option><option>Endorsed</option></select></label></div>
          <?php else: ?>
            <input type="hidden" name="new_status" value="" />
          <?php endif; ?>
          <div><button class="bg-blue-600 text-white px-3 py-1 rounded">Save Mediation</button></div>
        </form>
      </section>
    <?php endif; ?>
  </main>
</body>
</html>
