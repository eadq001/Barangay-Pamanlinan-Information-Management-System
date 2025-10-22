<?php
$conn = new mysqli("localhost", "root", "", "pamanlinan_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$id = $_GET['id'] ?? 0;
$result = $conn->query("SELECT * FROM services WHERE id = $id");
$data = $result->fetch_assoc();

if (!$data) {
    die("Record not found.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>View Service Record</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
:root {
  --primary: #0b6b2d;
  --primary-dark: #034b13;
  --background: #f4f7f5;
  --white: #fff;
  --text: #333;
  --shadow: 0 4px 10px rgba(0,0,0,0.1);
}

/* Reset */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: "Segoe UI", Arial, sans-serif;
}

body {
  background: var(--background);
  color: var(--text);
  padding: 40px;
}

/* Header */
.header {
  text-align: center;
  background: var(--primary);
  color: var(--white);
  padding: 1.5rem;
  border-radius: 10px;
  box-shadow: var(--shadow);
  margin-bottom: 30px;
}
.header h1 {
  font-size: 1.8rem;
  margin-bottom: 5px;
}
.header p {
  font-size: 0.9rem;
  opacity: 0.9;
}

/* Table container */
.table-container {
  background: var(--white);
  border-radius: 12px;
  box-shadow: var(--shadow);
  padding: 2rem;
  max-width: 900px;
  margin: 0 auto;
  border-top: 6px solid var(--primary);
}

table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 1rem;
}

th, td {
  padding: 12px 15px;
  text-align: left;
  border-bottom: 1px solid #ddd;
}

th {
  background-color: #f1f8f3;
  color: var(--primary);
  width: 30%;
  font-weight: 600;
}

td {
  color: #444;
}

/* Buttons */
.buttons {
  text-align: center;
  margin-top: 30px;
}
button {
  background: var(--primary);
  color: white;
  border: none;
  border-radius: 8px;
  padding: 10px 20px;
  font-size: 1rem;
  cursor: pointer;
  margin: 0 10px;
  transition: 0.3s;
  box-shadow: var(--shadow);
}
button:hover {
  background: var(--primary-dark);
}

/* Print Mode */
@media print {
  .buttons, .header {
    display: none;
  }
  body {
    padding: 0;
    background: white;
  }
  .table-container {
    box-shadow: none;
    border: none;
  }
}
</style>
</head>
<body onload="window.print()">

<div class="header">
  <h1><i class="fa-solid fa-hand-holding-heart"></i> Service Record Details</h1>
  <p>Barangay Pamanlinan Information System</p>
</div>

<div class="table-container">
  <table>
    <tr><th>Full Name</th><td><?= htmlspecialchars($data['first_name'].' '.$data['middle_name'].' '.$data['last_name'].' '.$data['ext_name']) ?></td></tr>
    <tr><th>Sex</th><td><?= htmlspecialchars($data['sex_name']) ?></td></tr>
    <tr><th>Birthdate</th><td><?= htmlspecialchars($data['date_of_birth']) ?></td></tr>
    <tr><th>Civil Status</th><td><?= htmlspecialchars($data['civil_status']) ?></td></tr>
    <tr><th>Place of Birth</th><td><?= htmlspecialchars($data['place_of_birth']) ?></td></tr>
    <tr><th>Address</th><td><?= htmlspecialchars($data['street_name'].' '.$data['purok_name']) ?></td></tr>
    <tr><th>Contact No.</th><td><?= htmlspecialchars($data['cellphone_no']) ?></td></tr>
    <tr><th>Valid ID</th><td><?= htmlspecialchars($data['valid_id']) ?></td></tr>
    <tr><th>Type of ID</th><td><?= htmlspecialchars($data['type_id']) ?></td></tr>
    <tr><th>Service Category</th><td><?= htmlspecialchars($data['service_category']) ?></td></tr>
    <tr><th>Sub-Service</th><td><?= htmlspecialchars($data['sub_service']) ?></td></tr>
    <tr><th>Date of Service</th><td><?= htmlspecialchars($data['service_date']) ?></td></tr>
  </table>

  <div class="buttons">
    <button onclick="window.location.href='pamanlinan.php'"><i class="fa-solid fa-arrow-left"></i> Back to Dashboard</button>
    <button onclick="window.print()"><i class="fa-solid fa-print"></i> Print Again</button>
  </div>
</div>

</body>
</html>
