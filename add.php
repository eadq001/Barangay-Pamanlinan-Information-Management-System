<?php
session_start();
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Database connection
$conn = new mysqli("localhost", "root", "", "pamanlinan_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Calculate age
function calculate_age($dob) {
    $birthDate = new DateTime($dob);
    $today = new DateTime('today');
    return $birthDate->diff($today)->y;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $fields = [
        'first_name', 'last_name', 'middle_name', 'ext_name', 'sex_name',
        'date_of_birth', 'civil_status', 'place_of_birth', 'street_name', 'purok_name',
        'cellphone_no', 'facebook', 'employed_unemployed', 'occupation', 'solo_parent',
        'ofw', 'school_youth', 'pwd', 'indigenous', 'citizenship', 'toilet',
        'womens_association', 'valid_id', 'type_id', 'household_id', 'family_id'
    ];

    foreach ($fields as $field) {
        $$field = isset($_POST[$field]) ? trim($_POST[$field]) : '';
    }

    $age = calculate_age($date_of_birth);

    $stmt = $conn->prepare("SELECT id FROM people WHERE first_name = ? AND last_name = ? AND middle_name = ?");
    $stmt->bind_param("sss", $first_name, $last_name, $middle_name);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $message = "Duplicate entry: This name already exists.";
    } else {
        $sql = "INSERT INTO people (
            first_name, last_name, middle_name, ext_name, sex_name, date_of_birth, age, civil_status,
            place_of_birth, street_name, purok_name, cellphone_no, facebook, employed_unemployed,
            occupation, solo_parent, ofw, school_youth, pwd, indigenous, citizenship,
            toilet, womens_association, valid_id, type_id, household_id, family_id
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $insert = $conn->prepare($sql);
        $insert->bind_param(
            "ssssssissssssssssssssssssss",
            $first_name, $last_name, $middle_name, $ext_name, $sex_name, $date_of_birth, $age, $civil_status,
            $place_of_birth, $street_name, $purok_name, $cellphone_no, $facebook, $employed_unemployed,
            $occupation, $solo_parent, $ofw, $school_youth, $pwd, $indigenous, $citizenship,
            $toilet, $womens_association, $valid_id, $type_id, $household_id, $family_id
        );

        if ($insert->execute()) {
            $message = "Data saved successfully.";
            $success = true;
        } else {
            $message = "Error saving data: " . $insert->error;
        }

        $insert->close();
    }

    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Barangay Pamanlinan | Registration Form</title>
<link rel="shortcut icon" href="pamanlinan.png" type="image/x-icon">
<style>
body {
  background: linear-gradient(to right, #d7e8e9, #ffffff);
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  margin: 0;
  color: #333;
  scroll-behavior: smooth;
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
  z-index: 1000;
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

/* Form Design */
form {
  background: #fff;
  max-width: 950px;
  margin: 160px auto;
  padding: 2rem 2.5rem;
  border-radius: 12px;
  box-shadow: 0 8px 16px rgba(0,0,0,0.1);
  position: relative;
  z-index: 1;
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
  transition: 0.3s;
}
button:hover {
  background-color: #08484d;
}

/* Checkbox group */
.checkbox-group {
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
}
.checkbox-option {
  display: flex;
  align-items: center;
  gap: 6px;
  background: #f8fafa;
  padding: 6px 10px;
  border-radius: 6px;
  border: 1px solid #ccc;
  cursor: pointer;
  font-size: 0.9rem;
}
input[type="radio"], input[type="checkbox"] {
  accent-color: #0d5c63;
  transform: scale(1.1);
}

/* MODAL DESIGN */
.modal {
  display: none;
  position: fixed;
  top: 0; left: 0;
  width: 100%; height: 100%;
  background: rgba(0,0,0,0.4);
  justify-content: center;
  align-items: center;
  z-index: 2000;
}
.modal-content {
  background: white;
  padding: 2rem;
  border-radius: 10px;
  text-align: center;
  max-width: 400px;
  box-shadow: 0 8px 20px rgba(0,0,0,0.3);
}
.modal-content h3 {
  color: #0d5c63;
  margin-bottom: 1rem;
}
.modal-content button {
  background: #0d5c63;
  border: none;
  color: white;
  padding: 0.6rem 1.5rem;
  border-radius: 6px;
  cursor: pointer;
  transition: 0.3s;
}
.modal-content button:hover {
  background: #08484d;
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

<form method="post" autocomplete="off">
  <h1>Resident Registration Form</h1>
  <h2>Personal Information</h2>
  <div class="grid">
    <input type="text" name="last_name" placeholder="Last Name" required>
    <input type="text" name="first_name" placeholder="First Name" required>
    <input type="text" name="middle_name" placeholder="Middle Name" required>
    <input type="text" name="ext_name" list="ext-options" placeholder="Extension Name">
  </div>

  <datalist id="ext-options">
    <option value="N/A"><option value="Jr."><option value="Sr."><option value="II">
    <option value="III"><option value="IV"><option value="Other">
  </datalist>

  <div class="grid">
    <div>
      <label>Sex</label>
      <div class="checkbox-group">
        <label class="checkbox-option"><input type="radio" name="sex_name" value="Male" required> Male</label>
        <label class="checkbox-option"><input type="radio" name="sex_name" value="Female"> Female</label>
      </div>
    </div>
    <input type="date" name="date_of_birth" required>
    <input type="text" name="place_of_birth" placeholder="Place of Birth" required>
    <input type="text" name="civil_status" placeholder="Civil Status" required>
  </div>

  <h2>Address & Contact</h2>
  <div class="grid">
    <input type="text" name="street_name" placeholder="Street Name">
    <select name="purok_name" required>
      <option value="">Select Purok</option>
      <option value="Purok 1">Purok 1</option>
      <option value="Purok 2A">Purok 2A</option>
      <option value="Purok 2B">Purok 2B</option>
      <option value="Purok 3">Purok 3</option>
      <option value="Purok 4">Purok 4</option>
      <option value="Purok 5">Purok 5</option>
      <option value="Purok 6">Purok 6</option>
    </select>
    <input type="tel" name="cellphone_no" placeholder="Contact Number">
    <input type="text" name="facebook" placeholder="Facebook Account">
  </div>

  <h2>Employment & Other Information</h2>
  <div class="grid">
    <input type="text" name="employed_unemployed" placeholder="Employment Status" required>
    <input type="text" name="occupation" placeholder="Occupation" required>
    <input type="text" name="citizenship" placeholder="Citizenship" required>
    <input type="text" name="household_id" placeholder="Household ID" required>
  </div>

  <h2>Additional Details</h2>
  <div class="grid">
    <input type="text" name="solo_parent" placeholder="Solo Parent (Yes/No)">
    <input type="text" name="ofw" placeholder="OFW (Yes/No)">
    <input type="text" name="school_youth" placeholder="Out-of-School Youth (Yes/No)">
    <input type="text" name="pwd" placeholder="PWD (Yes/No or Specify)">
    <input type="text" name="indigenous" placeholder="Indigenous People">
    <input type="text" name="toilet" placeholder="Toilet (Yes/No)">
    <input type="text" name="womens_association" placeholder="Women's Association Member (Yes/No)">
    <input type="text" name="valid_id" placeholder="Valid ID">
    <input type="text" name="type_id" placeholder="Type of ID">
  </div>

  <button type="submit">Save Record</button>
</form>

<!-- Modal Popup -->
<div class="modal" id="popupModal">
  <div class="modal-content">
    <h3 id="popupMessage"></h3>
    <button id="closeModal">OK</button>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  // Uppercase inputs except Facebook
  document.querySelectorAll('input:not([name="facebook"])').forEach(input => {
    input.addEventListener('input', () => {
      if (input.type === 'text' || input.type === 'tel') {
        input.value = input.value.toUpperCase();
      }
    });
  });

  // Handle modal popup message
  <?php if (isset($message)): ?>
  const modal = document.getElementById('popupModal');
  const msg = document.getElementById('popupMessage');
  msg.textContent = "<?php echo addslashes($message); ?>";
  modal.style.display = 'flex';
  document.getElementById('closeModal').onclick = function() {
    modal.style.display = 'none';
    <?php if (!empty($success)): ?>window.location.href='list.php';<?php endif; ?>
  };
  <?php endif; ?>
});
</script>
</body>
</html>
