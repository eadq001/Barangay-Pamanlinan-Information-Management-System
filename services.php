<?php
// =================== DATABASE CONNECTION ===================
$conn = new mysqli("localhost", "root", "", "pamanlinan_db");
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// =================== FORM SUBMISSION ===================
$showPopup = false;
$new_id = 0;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $fields = [
        'last_name', 'first_name', 'middle_name', 'ext_name', 'sex_name',
        'date_of_birth', 'civil_status', 'place_of_birth', 'street_name',
        'purok_name', 'cellphone_no', 'valid_id', 'type_id',
        'service_category', 'sub_service', 'service_date'
    ];
    
    $data = [];
    foreach ($fields as $field) {
        $data[$field] = trim($_POST[$field] ?? '');
    }

    $stmt = $conn->prepare("
        INSERT INTO services (
            last_name, first_name, middle_name, ext_name, sex_name, date_of_birth, civil_status,
            place_of_birth, street_name, purok_name, cellphone_no, valid_id, type_id,
            service_category, sub_service, service_date
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param(
        "ssssssssssssssss",
        $data['last_name'], $data['first_name'], $data['middle_name'], $data['ext_name'],
        $data['sex_name'], $data['date_of_birth'], $data['civil_status'], $data['place_of_birth'],
        $data['street_name'], $data['purok_name'], $data['cellphone_no'], $data['valid_id'],
        $data['type_id'], $data['service_category'], $data['sub_service'], $data['service_date']
    );

    if ($stmt->execute()) {
        $showPopup = true;
        $new_id = $conn->insert_id;
    } else {
        echo "<script>alert('Error saving record. Please try again.');</script>";
    }

    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Services Management Form</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="shortcut icon" href="pamanlinan.png" type="image/x-icon">
<style>
/* ====== GENERAL STYLES ====== */
body {
  background: linear-gradient(to right, #d7e8e9, #ffffff);
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  margin: 0;
  color: #333;
}
header {
  position: fixed;
  top: 0; left: 0; right: 0;
  background-color: #0d5c63;
  padding: 1.2rem 2rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  color: white;
  box-shadow: 0 4px 10px rgba(0,0,0,0.2);
}
.logo {
  font-size: 1.5rem;
  font-weight: bold;
  text-transform: uppercase;
  letter-spacing: 1px;
}
.nav-links {
  list-style: none;
  display: flex;
  gap: 1rem;
}
.nav-links a {
  color: white;
  text-decoration: none;
  font-weight: 600;
  padding: 0.4rem 1rem;
  border-radius: 6px;
  transition: background 0.3s;
}
.nav-links a:hover {
  background: rgba(255,255,255,0.2);
  color: gold;
}

/* ====== FORM DESIGN ====== */
form {
  background: #fff;
  max-width: 950px;
  margin: 140px auto;
  padding: 2rem 2.5rem;
  border-radius: 12px;
  box-shadow: 0 8px 16px rgba(0,0,0,0.1);
}
h1 {
  color: #0d5c63;
  text-align: center;
  font-size: 1.9rem;
  margin-bottom: 1rem;
}
h2 {
  color: #085458;
  border-bottom: 2px solid #cce1e2;
  padding-bottom: 0.5rem;
  margin-top: 2rem;
  font-size: 1.2rem;
}

/* ====== INPUTS & GRID ====== */
.grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  gap: 1rem;
  margin-top: 1rem;
}
input, select {
  width: 90%;
  padding: 0.6rem;
  border: 1px solid #ccc;
  border-radius: 6px;
  font-size: 0.95rem;
  transition: border-color 0.3s;
}
input:focus, select:focus {
  outline: none;
  border-color: #0d5c63;
  box-shadow: 0 0 5px rgba(13,92,99,0.3);
}

/* ====== BUTTON ====== */
button {
  display: block;
  margin: 2rem auto 0;
  background-color: #0d5c63;
  color: #fff;
  padding: 0.8rem 2.2rem;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  font-size: 1rem;
  transition: background 0.3s;
}
button:hover {
  background-color: #08484d;
}

/* ====== POPUP SUCCESS MODAL ====== */
.popup-overlay {
  position: fixed;
  top: 0; left: 0;
  width: 100%; height: 100%;
  background: rgba(0,0,0,0.5);
  display: none;
  justify-content: center;
  align-items: center;
  z-index: 999;
}
.popup {
  background: white;
  border-radius: 15px;
  padding: 2rem;
  text-align: center;
  box-shadow: 0 5px 20px rgba(0,0,0,0.2);
  animation: fadeIn 0.3s ease-in-out;
  max-width: 400px;
}
.popup i {
  font-size: 3rem;
  color: #0d5c63;
  margin-bottom: 10px;
}
.popup h2 {
  margin: 10px 0;
  color: #0d5c63;
}
.popup button {
  background: #0d5c63;
  color: white;
  border: none;
  padding: 10px 20px;
  border-radius: 8px;
  cursor: pointer;
  margin-top: 15px;
}
.popup button:hover {
  background: #08484d;
}
@keyframes fadeIn {
  from { opacity: 0; transform: scale(0.9); }
  to { opacity: 1; transform: scale(1); }
}
</style>
</head>
<body>
<header>
  <div class="logo">Barangay Pamanlinan Registration</div>
  <ul class="nav-links">
    <li><a href="pamanlinan.php">DASHBOARD</a></li>
    <li><a href="list.php">MAIN RECORDS</a></li>
    <li><a href="logout.php">LOGOUT</a></li>
  </ul>
</header>

<form method="POST" autocomplete="off">
  <h1>Services Form</h1>

  <h2>Personal Information</h2>
  <div class="grid">
    <div><label>Last Name</label><input type="text" name="last_name" required></div>
    <div><label>First Name</label><input type="text" name="first_name" required></div>
    <div><label>Middle Name</label><input type="text" name="middle_name"></div>
    <div><label>Extension Name</label><input type="text" name="ext_name" list="ext-options" placeholder="e.g., Jr."></div>
    <datalist id="ext-options">
      <option value="N/A"><option value="Jr."><option value="Sr."><option value="II"><option value="III">
    </datalist>
    <div><label>Sex</label>
      <select name="sex_name" required>
        <option value="">-- Select --</option>
        <option>Male</option><option>Female</option>
      </select>
    </div>
    <div><label>Birthdate</label><input type="text" name="date_of_birth" placeholder="MM/DD/YYYY" required></div>
    <div><label>Civil Status</label>
      <select name="civil_status" required>
        <option value="">-- Select --</option>
        <option>Single</option><option>Married</option><option>Widowed</option><option>Separated</option>
      </select>
    </div>
    <div><label>Place of Birth</label><input type="text" name="place_of_birth" required></div>
  </div>

  <h2>Address & Contact</h2>
  <div class="grid">
    <div><label>Street Name</label><input type="text" name="street_name"></div>
    <div><label>Purok Name</label>
      <select name="purok_name" required>
        <option value="">-- Select --</option>
        <option>Purok 1</option><option>Purok 2A</option><option>Purok 2B</option>
        <option>Purok 3</option><option>Purok 4</option><option>Purok 5</option><option>Purok 6</option>
      </select>
    </div>
    <div><label>Contact No.</label><input type="tel" name="cellphone_no"></div>
  </div>

  <h2>Service Identification</h2>
  <div class="grid">
    <div><label>Valid ID</label><input type="text" name="valid_id"></div>
    <div><label>Type of ID</label><input type="text" name="type_id"></div>
    <div>
      <label>Service Category</label>
      <select id="serviceCategory" name="service_category" onchange="updateSubservices()" required>
        <option value="">-- Select --</option>
        <option value="health">Health Services</option>
        <option value="social">Social Services</option>
        <option value="disaster">Disaster & Relief Services</option>
      </select>
    </div>
    <div>
      <label>Sub-Service</label>
      <select id="subService" name="sub_service" required>
        <option value="">-- Select Category First --</option>
      </select>
    </div>
    <div><label>Date of Service</label><input type="date" name="service_date" required></div>
  </div>

  <button type="submit">Save</button>
</form>

<!-- Popup Success Dialog -->
<div class="popup-overlay" id="popupOverlay">
  <div class="popup">
    <i class="fa-solid fa-circle-check"></i>
    <h2>Service Record Saved!</h2>
    <p>Your service record has been successfully submitted.</p>
    <button onclick="redirectToView()">View Record</button>
  </div>
</div>

<script>
function updateSubservices() {
  const category = document.getElementById("serviceCategory").value;
  const subService = document.getElementById("subService");
  let options = "<option value=''>-- Select --</option>";

  const subOptions = {
    health: [
      ["immunization", "Immunization"],
      ["maternal", "Maternal Care"],
      ["medical_mission", "Medical Mission"]
    ],
    social: [
      ["senior", "Assistance for Senior Citizens"],
      ["pwd", "Assistance for PWDs"],
      ["indigent", "Assistance for Indigents"]
    ],
    disaster: [
      ["relief", "Relief Distribution"]
    ]
  };

  if (subOptions[category]) {
    subOptions[category].forEach(([value, label]) => {
      options += `<option value="${value}">${label}</option>`;
    });
  }

  subService.innerHTML = options;
}

// Show popup if PHP set $showPopup
<?php if ($showPopup): ?>
document.addEventListener("DOMContentLoaded", () => {
  document.getElementById("popupOverlay").style.display = "flex";
});
function redirectToView() {
  window.location.href = "view_service.php?id=<?= $new_id ?>";
}
<?php endif; ?>
</script>
</body>
</html>
