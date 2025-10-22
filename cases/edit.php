<?php
require_once __DIR__ . '/../connection.php';
session_start();
if(!isset($_SESSION['user_id'])){ header('Location: ../login.php'); exit; }
$id = intval($_GET['id'] ?? 0);
$q = mysqli_query($con, "SELECT * FROM cases WHERE id={$id} LIMIT 1");
if(mysqli_num_rows($q) == 0){ echo 'Case not found'; exit; }
$case = mysqli_fetch_assoc($q);

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $complainant = mysqli_real_escape_string($con, $_POST['complainant']);
    $respondent = mysqli_real_escape_string($con, $_POST['respondent']);
    $incident_date = $_POST['incident_date'] ?: null;
    $category = mysqli_real_escape_string($con, $_POST['category']);
    $description = mysqli_real_escape_string($con, $_POST['description']);
    $status = mysqli_real_escape_string($con, $_POST['status']);

    $old_status = $case['status'];
    $u = "UPDATE cases SET complainant='{$complainant}', respondent='{$respondent}', incident_date=".($incident_date?"'{$incident_date}'":"NULL").", category='{$category}', description='{$description}', status='{$status}', updated_at=NOW() WHERE id={$id}";
    if(mysqli_query($con, $u)){
        if($old_status !== $status){
            mysqli_query($con, "UPDATE case_status_counts SET count = count - 1 WHERE status='".mysqli_real_escape_string($con,$old_status)."'");
            mysqli_query($con, "UPDATE case_status_counts SET count = count + 1 WHERE status='".mysqli_real_escape_string($con,$status)."'");
        }
        header('Location: view.php?id='.$id); exit;
    } else { $error = mysqli_error($con); }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Edit Case</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen">
  <?php include __DIR__ . '/_header.php'; ?>
  <main class="max-w-3xl mx-auto p-6">
    <h2 class="text-xl font-semibold mb-4">Edit Case</h2>
    <?php if(!empty($error)): ?><div class="bg-red-100 text-red-700 p-2 mb-3"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
    <form method="post">
      <div class="mb-2"><label class="block">Complainant<input name="complainant" required value="<?php echo htmlspecialchars($case['complainant']); ?>" class="w-full p-2 border" /></label></div>
      <div class="mb-2"><label class="block">Respondent<input name="respondent" value="<?php echo htmlspecialchars($case['respondent']); ?>" class="w-full p-2 border" /></label></div>
      <div class="mb-2"><label class="block">Incident Date<input type="date" name="incident_date" value="<?php echo htmlspecialchars($case['incident_date']); ?>" class="w-full p-2 border" /></label></div>
      <div class="mb-2"><label class="block">Category<input name="category" value="<?php echo htmlspecialchars($case['category']); ?>" class="w-full p-2 border" /></label></div>
      <div class="mb-2"><label class="block">Description<textarea name="description" class="w-full p-2 border"><?php echo htmlspecialchars($case['description']); ?></textarea></label></div>
      <div class="mb-2"><label class="block">Status<select name="status" class="w-full p-2 border"><option <?php if($case['status']=='Open') echo 'selected'; ?>>Open</option><option <?php if($case['status']=='Resolved') echo 'selected'; ?>>Resolved</option><option <?php if($case['status']=='Endorsed') echo 'selected'; ?>>Endorsed</option></select></label></div>
      <div class="mt-3"><button class="bg-green-700 text-white px-3 py-1 rounded">Save</button> <a href="view.php?id=<?php echo $id; ?>" class="ml-2">Cancel</a></div>
    </form>
  </main>
</body>
</html>
