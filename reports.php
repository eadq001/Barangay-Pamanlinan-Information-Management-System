<?php
// --- Start session and connect to database ---
session_start();

$conn = new mysqli("localhost", "root", "", "pamanlinan_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// --- Redirect if not logged in ---
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
<title>Reports & Analytics | Barangay Pamanlinan</title>
<link rel="shortcut icon" href="pamanlinan.png" type="image/x-icon">
<style>
:root {
  --primary: #0b6b2d;
  --primary-dark: #034b13;
  --background: #f3f7f4;
  --white: #ffffff;
  --text: #333333;
  --shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
}

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

/* ===== HEADER ===== */
header {
  background: var(--primary);
  color: var(--white);
  padding: 1.2rem 2rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  box-shadow: var(--shadow);
}
header h1 {
  font-size: 1.5rem;
}
nav a {
  color: var(--white);
  text-decoration: none;
  margin-left: 1.5rem;
  font-weight: 600;
  transition: 0.3s;
}
nav a:hover {
  color: #ccebc5;
}

/* ===== MAIN CONTENT ===== */
main {
  flex: 1;
  padding: 2rem 3%;
}

h2 {
  color: var(--primary-dark);
  margin-bottom: 1rem;
  font-size: 1.8rem;
}

.reports-section {
  background: var(--white);
  padding: 2rem;
  border-radius: 15px;
  box-shadow: var(--shadow);
  line-height: 1.7;
}

.reports-section ul {
  margin-left: 1.5rem;
  list-style-type: disc;
}

.reports-section li {
  margin-bottom: 0.8rem;
  font-size: 1.05rem;
}

/* ===== FOOTER ===== */
footer {
  background: var(--primary);
  color: var(--white);
  text-align: center;
  padding: 1rem;
  font-size: 0.9rem;
}
</style>
</head>
<body>

<header>
  <h1>Barangay Pamanlinan Information System</h1>
  <nav>
    <a href="pamanlinan.php">Dashboard</a>
    <a href="list.php">Records</a>
    <a href="reports.php">Reports</a>
    <a href="services_form.php">Services</a>
    <a href="logout.php">Logout</a>
  </nav>
</header>

<main>
  <h2>Reports & Analytics</h2>
  <section class="reports-section">
    <p>The Reports & Analytics module provides comprehensive insights into various aspects of barangay data, promoting transparency and informed decision-making.</p>
    <ul>
      <li><strong>Demographic Reports:</strong> Includes population details, age distribution, employment, and other demographic trends.</li>
      <li><strong>Health Statistics:</strong> Tracks common illnesses, immunization rates, and other public health indicators.</li>
      <li><strong>Crime and Incident Statistics:</strong> Records and analyzes local incidents and crime patterns.</li>
      <li><strong>Financial and Project Reports:</strong> Displays barangay financial statements, fund allocations, and project updates.</li>
    </ul>
  </section>
</main>

<footer>
  &copy; 2025 Barangay Pamanlinan | All Rights Reserved
</footer>

</body>
</html>
