<?php
$conn = new mysqli("localhost", "root", "", "pamanlinan_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$id = $_GET['id'] ?? 0;
$result = $conn->query("SELECT * FROM household_housing WHERE id = $id");
$data = $result->fetch_assoc();

if (!$data) {
    die("Record not found.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>View Household Survey</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
:root {
  --primary: #0b6b2d;
  --primary-dark: #034b13;
  --background: #f4f7f5;
  --white: #ffffff;
  --text: #333;
  --shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
}

/* Reset & Base */
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
  background: var(--primary);
  color: var(--white);
  text-align: center;
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

/* Container */
.container {
  background: var(--white);
  border-radius: 12px;
  box-shadow: var(--shadow);
  padding: 2rem;
  max-width: 950px;
  margin: 0 auto;
  border-top: 6px solid var(--primary);
}

/* Table Design */
table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 1rem;
}
th, td {
  padding: 12px 15px;
  text-align: left;
  border-bottom: 1px solid #e5e5e5;
}
th {
  background: #f1f8f3;
  color: var(--primary);
  width: 35%;
  font-weight: 600;
}
td {
  color: #444;
  background-color: #fcfcfc;
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
  transition: all 0.3s;
  box-shadow: var(--shadow);
}
button:hover {
  background: var(--primary-dark);
}

/* Print Mode */
@media print {
  .header, .buttons {
    display: none;
  }
  body {
    padding: 0;
    background: white;
  }
  .container {
    box-shadow: none;
    border: none;
  }
}
</style>
</head>
<body onload="window.print()">

<div class="header">
  <h1><i class="fa-solid fa-house-user"></i> Household / Housing Survey Record</h1>
  <p>Barangay Pamanlinan Information System</p>
</div>

<div class="container">
  <table>
    <?php foreach ($data as $key => $value): ?>
      <?php if ($key != 'id'): ?>
        <tr>
          <th><?= ucwords(str_replace("_", " ", $key)) ?></th>
          <td><?= htmlspecialchars($value) ?></td>
        </tr>
      <?php endif; ?>
    <?php endforeach; ?>
  </table>

  <div class="buttons">
    <button onclick="window.location.href='pamanlinan.php'"><i class="fa-solid fa-arrow-left"></i> Back to Dashboard</button>
    <button onclick="window.print()"><i class="fa-solid fa-print"></i> Print Again</button>
  </div>
</div>

</body>
</html>
