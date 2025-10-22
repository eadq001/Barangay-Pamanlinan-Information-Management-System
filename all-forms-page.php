<?php
// --- Start session and connect to your database ---
session_start();

$conn = new mysqli("localhost", "root", "", "pamanlinan_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// --- Example: check if logged in (optional) ---
if (empty($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Barangay Pamanlinan Dashboard</title>
<link rel="shortcut icon" href="pamanlinan.png" type="image/x-icon">

<!-- Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
/* ====== COLOR VARIABLES ====== */
:root {
  --primary: #0b6b2d;
  --primary-dark: #034b13;
  --background: #f3f7f4;
  --white: #ffffff;
  --text: #333333;
  --shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
}

/* ====== RESET ====== */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: "Segoe UI", Arial, sans-serif;
}

body {
  background: var(--background);
  color: var(--text);
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}

/* ====== HEADER ====== */
header {
  background: var(--primary);
  color: var(--white);
  padding: 1.2rem 2rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  box-shadow: var(--shadow);
  position: sticky;
  top: 0;
  z-index: 1000;
}
header h1 {
  font-size: 1.5rem;
  letter-spacing: 1px;
  display: flex;
  align-items: center;
  gap: 10px;
}
header h1 img {
  width: 45px;
  height: 45px;
}
nav a {
  color: var(--white);
  text-decoration: none;
  margin-left: 1.5rem;
  font-weight: 600;
  transition: 0.3s;
}
nav a:hover {
  color: gold;
  text-decoration: underline;
}

/* ====== MAIN DASHBOARD ====== */
main {
  flex: 1;
  padding: 2rem 3%;
}

.dashboard {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
  gap: 1.5rem;
  margin-top: 1.5rem;
}

/* ====== CARD DESIGN ====== */
.card {
  background: var(--white);
  border-radius: 15px;
  box-shadow: var(--shadow);
  text-align: center;
  padding: 2rem 1.5rem;
  border-top: 6px solid var(--primary);
  cursor: pointer;
  transition: all 0.3s ease;
}
.card:hover {
  transform: translateY(-6px);
  box-shadow: 0 6px 14px rgba(0, 0, 0, 0.15);
}
.card i {
  font-size: 3rem;
  color: var(--primary);
  margin-bottom: 1rem;
}
.card h3 {
  color: var(--primary);
  margin-bottom: 0.5rem;
}
.card p {
  color: #555;
  font-size: 0.95rem;
}

/* ====== FOOTER ====== */
footer {
  background: var(--primary);
  color: var(--white);
  text-align: center;
  padding: 1rem;
  font-size: 0.9rem;
  margin-top: 2rem;
}
</style>
</head>
<body>

<header>
  <h1><img src="pamanlinan.png" alt="Logo"> Barangay Pamanlinan Information Management Forms</h1>
  <nav>
    <a href="pamanlinan.php">DASHBOARD</a>
    <a href="logout.php">LOGOUT</a>
  </nav>
</header>

<main>
  <section class="dashboard">
    <!-- Registration Form -->
    <div class="card" onclick="window.location.href='add.php'">
      <i class="fa-solid fa-id-card"></i>
      <h3>Registration Form</h3>
      <p>Register new residents and maintain accurate demographic information.</p>
    </div>

    <!-- Services Management -->
    <div class="card" onclick="window.location.href='services.php'">
      <i class="fa-solid fa-hand-holding-heart"></i>
      <h3>Services Management</h3>
      <p>Record and manage health, social, and relief services provided by the barangay.</p>
    </div>

    <!-- Household Profiling -->
    <div class="card" onclick="window.location.href='household.php'">
      <i class="fa-solid fa-house-user"></i>
      <h3>Household Profiling</h3>
      <p>Monitor household demographics and manage residential information.</p>
    </div>

    <!-- Population Records -->
    <div class="card" onclick="window.location.href='list.php'">
      <i class="fa-solid fa-users"></i>
      <h3>Population Records</h3>
      <p>Keep accurate and updated personal and demographic records of residents.</p>
    </div>

    <!-- Cases & Incidents -->
    <div class="card" onclick="window.location.href='cases/index.php'">
      <i class="fa-solid fa-exclamation-triangle"></i>
      <h3>Cases and Incidents</h3>
      <p>Record barangay cases, disputes, and incident reports for documentation and follow-up.</p>
    </div>

    <!-- Bulletin Board -->
    <div class="card" onclick="window.location.href='bulletinBoard.php'">
      <i class="fa-solid fa-bullhorn"></i>
      <h3>Bulletin Board</h3>
      <p>Post barangay announcements, news, and public advisories for residents.</p>
    </div>

    <!-- Reports -->
    <div class="card" onclick="window.location.href='reports.php'">
      <i class="fa-solid fa-chart-column"></i>
      <h3>Reports</h3>
      <p>Generate reports and analytics for transparency and decision-making.</p>
    </div>

    <!-- Settings -->
 
</main>

<footer>
  &copy; 2025 Barangay Pamanlinan | All Rights Reserved
</footer>

</body>
</html>
