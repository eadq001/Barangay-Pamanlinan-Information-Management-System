<?php
session_start();
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Database connection
$pdo = new PDO('mysql:host=localhost;dbname=pamanlinan_db', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

// Queries
// 1. Population per Purok
$stmt = $pdo->query("SELECT purok_name, COUNT(*) as count FROM people GROUP BY purok_name ORDER BY purok_name");
$purokData = $stmt->fetchAll(PDO::FETCH_ASSOC);
$purokNames = array_column($purokData, 'purok_name');
$purokCounts = array_column($purokData, 'count');

// 2. Household per Purok
$stmt = $pdo->query("SELECT purok_name, COUNT(DISTINCT household_id) as household_count FROM people GROUP BY purok_name ORDER BY purok_name");
$householdData = $stmt->fetchAll(PDO::FETCH_ASSOC);
$householdCounts = array_column($householdData, 'household_count');

// 3. Families per Purok
$stmt = $pdo->query("SELECT purok_name, COUNT(DISTINCT family_id) as family_count FROM people GROUP BY purok_name ORDER BY purok_name");
$familyData = $stmt->fetchAll(PDO::FETCH_ASSOC);
$familyCounts = array_column($familyData, 'family_count');

// 4. Households with Toilet
$stmt = $pdo->query("SELECT purok_name, COUNT(*) as toilet_count FROM people WHERE toilet='Yes' GROUP BY purok_name ORDER BY purok_name");
$toiletData = $stmt->fetchAll(PDO::FETCH_ASSOC);
$toiletCounts = array_column($toiletData, 'toilet_count');

// 5. Genders per Purok
$stmt = $pdo->query("SELECT purok_name,
 SUM(CASE WHEN sex_name='Male' THEN 1 ELSE 0 END) as male_count,
 SUM(CASE WHEN sex_name='Female' THEN 1 ELSE 0 END) as female_count
 FROM people GROUP BY purok_name ORDER BY purok_name");
$genderData = $stmt->fetchAll(PDO::FETCH_ASSOC);
$maleCounts = array_column($genderData, 'male_count');
$femaleCounts = array_column($genderData, 'female_count');

// 6. Out-of-School Youth
$stmt = $pdo->query("SELECT purok_name, COUNT(*) as osy_count FROM people WHERE school_youth='Yes' GROUP BY purok_name ORDER BY purok_name");
$osyData = $stmt->fetchAll(PDO::FETCH_ASSOC);
$osyCounts = array_column($osyData, 'osy_count');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Barangay Dashboard</title>
  <link rel="shortcut icon" href="pamanlinan.png" type="image/x-icon">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
  <style>
    *{margin:0;padding:0;box-sizing:border-box;font-family:"Segoe UI",sans-serif;}
    body{display:flex;min-height:100vh;background:#f5f7fa;}
    /* Sidebar */
    .sidebar{
      width:250px;background:#057570;color:#fff;flex-shrink:0;
      display:flex;flex-direction:column;position:fixed;top:0;bottom:0;left:0;
    }
    .sidebar .logo{
      padding:10px;text-align:center;background:#04615f;
    }
    .sidebar .logo img{
      width:180px;   /* 🔹 adjust logo width */
      height:auto;   /* keep proportions */
      max-width:100%;
    }
    .sidebar ul{list-style:none;
        padding:10px;}

    .sidebar ul li{margin:8px 0;}

    .sidebar ul li a{
      display:block;
      padding:10px 15px;
      color:#fff;
      text-decoration:none;
      border-radius:6px;transition:0.3s;
      text-shadow: 2px 1px 1px rgba(0, 0, 0, 0.4);
      text-align: center;
      text-wrap-style: pretty; /* 🔹 ensure text wraps nicely */
      font-weight: bold;
      font-family: Arial, Helvetica, sans-serif
    }
    .sidebar ul li a:hover{background:#05928f;}
    /* Main */
    .main{margin-left:250px;padding:20px;flex:1;}
    .header{
      display:flex;align-items:center;justify-content:space-between;
      margin-bottom:20px;padding:10px 20px;background:#fff;border-radius:10px;
      box-shadow:0 2px 6px rgba(0,0,0,0.1);
    }
    .header h1{font-size:2.4em;color:#057570;}
    /* Cards */
    .cards{
      display:grid;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));
      gap:20px;margin-top:20px;
    }
    .card{
      background:#fff;padding:20px;border-radius:12px;
      box-shadow:0 2px 8px rgba(0,0,0,0.08);
    }
    .card h2{text-align:center;font-size:1.1em;margin-bottom:10px;color:#333;}
    .total{text-align:center;margin-top:12px;font-weight:bold;
      color:#057570;font-size:1.05em;}
    /* Export */
    .export-section{
      margin:30px auto;text-align:center;padding:20px;background:#fff;
      border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,0.08);max-width:600px;
    }
    select,button{
      padding:8px 14px;font-size:1em;margin:6px;
      border:1px solid #ccc;border-radius:6px;
    }
    button{background:#057570;color:#fff;border:none;cursor:pointer;transition:0.3s;}
    button:hover{background:#05928f;}
    @media(max-width:768px){.sidebar{width:200px;}.main{margin-left:200px;}}
    @media(max-width:600px){.sidebar{display:none;}.main{margin:0;}}
  </style>
</head>
<body>

<!-- Sidebar -->
<aside class="sidebar">
  <div class="logo">
    <img src="pamanlinan-logo.png" alt="Barangay Logo">
  </div>
  <ul>
    <li><a href="household-familly-records.html">HOUSEHOLD & FAMILY RECORDS</a></li>
    <li><a href="Barangay-Services-management.html">SERVICES MANAGEMENT</a></li>
    <li><a href="ageGroup.php">AGE GROUP</a></li>
    <li><a href="disabilitiesGroup.php">DISABILITIES</a></li>
    <li><a href="deceased.php">DECEASED</a></li>
    <li><a href="list.php">MAIN LISTS</a></li>
    <li><a href="add.php">REGISTRATION FORM</a></li>
    <li><a href="logout.php">LOGOUT</a></li>
  </ul>
</aside>

<!-- Main -->
<div class="main">
  <div class="header">
    <h1>BARANGAY PAMANLINAN DASHBOARD</h1>
  </div>

  <div class="cards">
    <div class="card">
      <h2>Population per Purok</h2>
      <canvas id="purokChart"></canvas>
      <div class="total"><?php echo "Total Population: " . array_sum($purokCounts); ?></div>
    </div>

    <div class="card">
      <h2>Household per Purok</h2>
      <canvas id="householdChart"></canvas>
      <div class="total"><?php echo "Total Households: " . array_sum($householdCounts); ?></div>
    </div>

    <div class="card">
      <h2>Families per Purok</h2>
      <canvas id="familyChart"></canvas>
      <div class="total"><?php echo "Total Families: " . array_sum($familyCounts); ?></div>
    </div>

    <div class="card">
      <h2>Households with Toilet</h2>
      <canvas id="toiletChart"></canvas>
      <div class="total"><?php echo "Total Households with Toilet: " . array_sum($toiletCounts); ?></div>
    </div>

    <div class="card">
      <h2>Genders per Purok</h2>
      <canvas id="genderChart"></canvas>
      <div class="total">
        <?php echo "Total Male: " . array_sum($maleCounts) . " | Total Female: " . array_sum($femaleCounts); ?>
      </div>
    </div>

    <div class="card">
      <h2>Out-of-School Youth per Purok</h2>
      <canvas id="osyChart"></canvas>
      <div class="total"><?php echo "Total OSY: " . array_sum($osyCounts); ?></div>
    </div>
  </div>

  <div class="export-section">
    <h3>Export Chart Data to Excel</h3>
    <form id="exportForm">
      <select id="chartSelect" name="chartSelect">
        <option value="all">All Charts</option>
        <option value="purok">Population per Purok</option>
        <option value="household">Household per Purok</option>
        <option value="families">Families per Purok</option>
        <option value="toilet">Households with Toilet</option>
        <option value="gender">Genders per Purok</option>
        <option value="osy">Out-of-School Youth</option>
      </select>
      <button type="button" id="exportBtn">Export to Excel</button>
    </form>
  </div>
</div>

<script>
// Population (Bar)
new Chart(document.getElementById('purokChart'), {
  type:'bar',
  data:{labels:<?php echo json_encode($purokNames); ?>,
    datasets:[{label:'Population',data:<?php echo json_encode($purokCounts); ?>,
      backgroundColor:'rgba(6,182,212,0.7)',borderColor:'rgba(6,182,212,1)',borderWidth:2}]}
});

// Household (Line)
new Chart(document.getElementById('householdChart'), {
  type:'line',
  data:{labels:<?php echo json_encode($purokNames); ?>,
    datasets:[{label:'Households',data:<?php echo json_encode($householdCounts); ?>,
      fill:false,borderColor:'rgba(34,197,94,1)',tension:0.3}]}
});

// Families (Pie)
new Chart(document.getElementById('familyChart'), {
  type:'pie',
  data:{labels:<?php echo json_encode($purokNames); ?>,
    datasets:[{label:'Families',data:<?php echo json_encode($familyCounts); ?>,
      backgroundColor:['#60a5fa','#f87171','#34d399','#fbbf24','#a78bfa','#fb7185']}]}
});

// Toilets (Doughnut)
new Chart(document.getElementById('toiletChart'), {
  type:'doughnut',
  data:{labels:<?php echo json_encode($purokNames); ?>,
    datasets:[{label:'Toilets',data:<?php echo json_encode($toiletCounts); ?>,
      backgroundColor:['#10b981','#f59e0b','#3b82f6','#ef4444','#6366f1','#ec4899']}]}
});

// Genders (Stacked Bar)
new Chart(document.getElementById('genderChart'), {
  type:'bar',
  data:{labels:<?php echo json_encode($purokNames); ?>,
    datasets:[
      {label:'Male',data:<?php echo json_encode($maleCounts); ?>,
        backgroundColor:'rgba(59,130,246,0.7)'},
      {label:'Female',data:<?php echo json_encode($femaleCounts); ?>,
        backgroundColor:'rgba(236,72,153,0.7)'}
    ]},
  options:{scales:{x:{stacked:true},y:{stacked:true}}}
});

// Out-of-School Youth (Radar)
new Chart(document.getElementById('osyChart'), {
  type:'radar',
  data:{labels:<?php echo json_encode($purokNames); ?>,
    datasets:[{label:'OSY',data:<?php echo json_encode($osyCounts); ?>,
      backgroundColor:'rgba(168,85,247,0.2)',borderColor:'rgba(168,85,247,1)',pointBackgroundColor:'rgba(168,85,247,1)'}]}
});
</script>
</body>
</html>
