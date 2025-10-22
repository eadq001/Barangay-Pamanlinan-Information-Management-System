<?php
session_start();
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$pdo = new PDO('mysql:host=localhost;dbname=pamanlinan_db', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

// ======================== QUERIES ========================
$stmt = $pdo->query("SELECT purok_name, COUNT(*) AS count FROM people GROUP BY purok_name ORDER BY purok_name");
$purokData = $stmt->fetchAll(PDO::FETCH_ASSOC);
$purokNames = array_column($purokData, 'purok_name');
$purokCounts = array_column($purokData, 'count');

$stmt = $pdo->query("SELECT purok_name, COUNT(DISTINCT household_id) AS household_count FROM people GROUP BY purok_name ORDER BY purok_name");
$householdCounts = array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'household_count');

$stmt = $pdo->query("SELECT purok_name, COUNT(DISTINCT family_id) AS family_count FROM people GROUP BY purok_name ORDER BY purok_name");
$familyCounts = array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'family_count');

$stmt = $pdo->query("SELECT purok_name, COUNT(*) AS toilet_count FROM people WHERE toilet='Yes' GROUP BY purok_name ORDER BY purok_name");
$toiletCounts = array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'toilet_count');

$stmt = $pdo->query("
    SELECT purok_name,
    SUM(CASE WHEN sex_name='Male' THEN 1 ELSE 0 END) AS male_count,
    SUM(CASE WHEN sex_name='Female' THEN 1 ELSE 0 END) AS female_count
    FROM people GROUP BY purok_name ORDER BY purok_name
");
$genderData = $stmt->fetchAll(PDO::FETCH_ASSOC);
$maleCounts = array_column($genderData, 'male_count');
$femaleCounts = array_column($genderData, 'female_count');

$stmt = $pdo->query("SELECT purok_name, COUNT(*) AS osy_count FROM people WHERE school_youth='Yes' GROUP BY purok_name ORDER BY purok_name");
$osyCounts = array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'osy_count');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Barangay Dashboard</title>
<link rel="shortcut icon" href="pamanlinan.png" type="image/x-icon">
<link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');

*{margin:0;padding:0;box-sizing:border-box;font-family:'Inter',sans-serif;}
body{display:flex;min-height:100vh;background:#eef2f3;color:#333;}

/* ===== SIDEBAR ===== */
.sidebar{
  width:260px;background:linear-gradient(180deg,#035d4a,#048f77);
  color:#fff;display:flex;flex-direction:column;
  position:fixed;top:0;bottom:0;left:0;
  box-shadow:3px 0 10px rgba(0,0,0,0.15);
}
.sidebar .logo{text-align:center;padding:20px 10px;border-bottom:1px solid rgba(255,255,255,0.2);}
.sidebar .logo img{width:150px;border-radius:8px;}
.sidebar h2{font-size:1.1em;font-weight:600;margin-top:5px;}
.sidebar ul{list-style:none;margin-top:25px;padding:0;}
.sidebar ul li{margin:6px 0;}
.sidebar ul li a{
  display:flex;align-items:center;gap:10px;padding:12px 20px;
  color:#fff;text-decoration:none;border-radius:8px;transition:0.3s;font-weight:500;
}
.sidebar ul li a:hover,.sidebar ul li a.active{background:rgba(255,255,255,0.15);}
.sidebar ul li a i{font-size:1.3em;}

/* ===== MAIN ===== */
.main{margin-left:260px;padding:30px;width:100%;}

/* ===== HEADER ===== */
.header{
  display:flex;align-items:center;justify-content:space-between;
  background:#fff;border-radius:14px;padding:20px 30px;
  box-shadow:0 3px 10px rgba(0,0,0,0.1);
}
.header h1{font-size:1.9em;color:#035d4a;font-weight:700;}
.header span{color:#666;font-size:0.95em;}

/* ===== CARDS ===== */
.cards{
  display:grid;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));
  gap:25px;margin-top:30px;
}
.card{
  background:rgba(255,255,255,0.9);
  border-radius:14px;padding:20px;
  box-shadow:0 4px 12px rgba(0,0,0,0.1);
  backdrop-filter:blur(10px);
  transition:transform 0.25s ease,box-shadow 0.25s ease;
}
.card:hover{transform:translateY(-4px);box-shadow:0 8px 18px rgba(0,0,0,0.15);}
.card h2{text-align:center;font-size:1.1em;margin-bottom:15px;color:#036c56;}
.total{text-align:center;margin-top:15px;font-weight:600;color:#025647;}

/* ===== EXPORT SECTION ===== */
.export-section{
  margin:50px auto;text-align:center;padding:30px;background:#fff;
  border-radius:14px;box-shadow:0 3px 10px rgba(0,0,0,0.1);max-width:650px;
}
.export-section h3{font-size:1.3rem;color:#035d4a;margin-bottom:15px;}
select,button{
  padding:10px 15px;font-size:1em;margin:10px;border:1px solid #ccc;
  border-radius:8px;outline:none;
}
button{
  background:#035d4a;color:#fff;border:none;cursor:pointer;transition:background 0.3s;
}
button:hover{background:#049c82;}
button i{margin-right:6px;}

@media(max-width:768px){
  .sidebar{width:200px;}
  .main{margin-left:200px;}
}
@media(max-width:600px){
  .sidebar{display:none;}
  .main{margin:0;padding:20px;}
}

.export-section {
  max-width: 400px;
  margin: 30px auto;
  padding: 25px;
  border-radius: 12px;
  background: linear-gradient(135deg, #e8f5e9, #c8e6c9);
  box-shadow: 0 4px 10px rgba(0,0,0,0.15);
  text-align: center;
}
.export-section h3 {
  color: #2e7d32;
  font-size: 20px;
  margin-bottom: 20px;
}
.export-section select.dropdown {
  width: 100%;
  padding: 10px;
  border-radius: 6px;
  border: 1px solid #ccc;
  margin-bottom: 15px;
  font-size: 15px;
}
.btn-export {
  background-color: #2e7d32;
  color: white;
  border: none;
  padding: 10px 18px;
  border-radius: 6px;
  cursor: pointer;
  font-size: 15px;
  transition: 0.3s;
}
.btn-export:hover {
  background-color: #1b5e20;
}
</style>
</head>
<body>

<!-- Sidebar -->
<!-- Sidebar -->
<aside class="sidebar">
  <div class="logo">
    <img src="pamanlinan-logo.png" alt="Barangay Logo">
    <h2>Barangay Pamanlinan</h2>
  </div>
  <ul>
    <li><a href="list.php"><i class="ri-database-2-line"></i>Main Records</a></li>
    <li><a href="all-forms-page.php"><i class="ri-file-list-line"></i>Forms</a></li>
    <li><a href="ageGroup.php"><i class="ri-user-heart-line"></i>Age Group</a></li>
    <li><a href="disabilitiesGroup.php"><i class="ri-wheelchair-line"></i>Disabilities</a></li>
    <li><a href="deceased.php"><i class="ri-emotion-sad-line"></i>Deceased</a></li>

    <!-- 🆕 Added Menu Items -->
    <li><a href="bulletinBoard.php"><i class="ri-megaphone-line"></i>Bulletin</a></li>
    <li><a href="cases/index.php"><i class="ri-briefcase-line"></i>Cases</a></li>

    <li><a href="logout.php"><i class="ri-logout-circle-line"></i>Logout</a></li>
  </ul>
</aside>


<!-- Main -->
<div class="main">
  <div class="header">
    <div>
      <h1>Barangay Dashboard</h1>
      <span>Comprehensive Data Analytics Overview</span>
    </div>
  </div>

  <div class="cards">
    <div class="card">
      <h2>Population per Purok</h2>
      <canvas id="purokChart"></canvas>
      <div class="total">Total Population: <?= array_sum($purokCounts) ?></div>
    </div>

    <div class="card">
      <h2>Household per Purok</h2>
      <canvas id="householdChart"></canvas>
      <div class="total">Total Households: <?= array_sum($householdCounts) ?></div>
    </div>

    <div class="card">
      <h2>Families per Purok</h2>
      <canvas id="familyChart"></canvas>
      <div class="total">Total Families: <?= array_sum($familyCounts) ?></div>
    </div>

    <div class="card">
      <h2>Households with Toilet</h2>
      <canvas id="toiletChart"></canvas>
      <div class="total">With Toilet: <?= array_sum($toiletCounts) ?></div>
    </div>

    <div class="card">
      <h2>Genders per Purok</h2>
      <canvas id="genderChart"></canvas>
      <div class="total">Male: <?= array_sum($maleCounts) ?> | Female: <?= array_sum($femaleCounts) ?></div>
    </div>

    <div class="card">
      <h2>Out-of-School Youth</h2>
      <canvas id="osyChart"></canvas>
      <div class="total">Total OSY: <?= array_sum($osyCounts) ?></div>
    </div>
  </div>

  <div class="export-section">
  <h3>📊 Export Chart Data</h3>
  <form id="exportForm">
    <select id="chartSelect" name="chartSelect" class="dropdown">
      <option value="all">All Charts</option>
      <option value="purok">Population per Purok</option>
      <option value="household">Household per Purok</option>
      <option value="families">Families per Purok</option>
      <option value="toilet">Households with Toilet</option>
      <option value="gender">Genders per Purok</option>
      <option value="osy">Out-of-School Youth</option>
    </select>
    <button type="button" id="exportBtn" class="btn-export">
      <i class="ri-file-excel-2-line"></i> Export to Excel
    </button>
  </form>
</div>
<script>
// CHARTS
const labels = <?= json_encode($purokNames) ?>;
new Chart(document.getElementById('purokChart'), {
  type:'bar',
  data:{labels,datasets:[{label:'Population',data:<?= json_encode($purokCounts) ?>,backgroundColor:'rgba(3,140,115,0.6)',borderColor:'#02735E',borderWidth:2}]}
});
new Chart(document.getElementById('householdChart'), {
  type:'line',
  data:{labels,datasets:[{label:'Households',data:<?= json_encode($householdCounts) ?>,borderColor:'#04AA6D',fill:false,tension:0.3}]}
});
new Chart(document.getElementById('familyChart'), {
  type:'pie',
  data:{labels,datasets:[{label:'Families',data:<?= json_encode($familyCounts) ?>,backgroundColor:['#50C878','#FFD166','#EF476F','#06D6A0','#118AB2','#073B4C']}]}
});
new Chart(document.getElementById('toiletChart'), {
  type:'doughnut',
  data:{labels,datasets:[{label:'Toilets',data:<?= json_encode($toiletCounts) ?>,backgroundColor:['#06D6A0','#FFD166','#118AB2','#EF476F','#073B4C','#50C878']}]}
});
new Chart(document.getElementById('genderChart'), {
  type:'bar',
  data:{labels,datasets:[
    {label:'Male',data:<?= json_encode($maleCounts) ?>,backgroundColor:'rgba(54,162,235,0.6)'},
    {label:'Female',data:<?= json_encode($femaleCounts) ?>,backgroundColor:'rgba(255,99,132,0.6)'}
  ]},
  options:{scales:{x:{stacked:true},y:{stacked:true}}}
});
new Chart(document.getElementById('osyChart'), {
  type:'radar',
  data:{labels,datasets:[{label:'Out-of-School Youth',data:<?= json_encode($osyCounts) ?>,backgroundColor:'rgba(153,102,255,0.2)',borderColor:'#9966FF',pointBackgroundColor:'#9966FF'}]}
});



document.getElementById('exportBtn').addEventListener('click', function() {
  const selectedChart = document.getElementById('chartSelect').value;

  // Send to export PHP script
  window.location.href = 'export_chart.php?chart=' + encodeURIComponent(selectedChart);
});



</script>
</body>
</html>
