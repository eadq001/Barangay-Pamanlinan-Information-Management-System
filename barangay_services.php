<?php
// --- DATABASE CONNECTION ---
$pdo = new PDO('mysql:host=localhost;dbname=pamanlinan_db', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

// --- HANDLE FORM SUBMISSION ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name     = $_POST['resident_name'];
    $address  = $_POST['address'];
    $category = $_POST['service_category'];
    $sub      = $_POST['sub_service'];
    $date     = $_POST['service_date'];
    $contact  = $_POST['contact'];

    $stmt = $pdo->prepare("INSERT INTO service_records 
        (resident_name, address, category, sub_service, service_date, contact) 
        VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$name, $address, $category, $sub, $date, $contact]);
}

// --- FETCH ALL RECORDS ---
$stmt = $pdo->query("SELECT * FROM service_records ORDER BY id DESC");
$records = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Barangay Services Management</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
</head>

<style>
  .nav-links {
    list-style: none;
    display: flex;
    gap: 1rem;
  }
  nav{
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 2rem;
  }
  .nav-links a {
    color: white;
    text-decoration: none;
    font-weight: 700;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
    padding: 4px 8px;
    border-radius: 3px;
    transition: all 0.3s;
  }
  .nav-links a:hover {
    background-color: rgb(139, 226, 217);
    color: #000000;
    font-weight: bolder;
  }
</style>

<body class="bg-gray-100">

  <!-- HEADER -->
  <header class="bg-green-700 text-white p-4 shadow-lg">
    <h1 class="text-2xl font-bold text-center">PAMANLINAN SERVICES MANAGEMENT RECORDS</h1><br><br>
    <nav>
      <ul class="nav-links" id="navLinks">
        <li><a href="pamanlinan.php">DASHBOARD</a></li>
        <li><a href="ageGroup.php">AGE GROUP</a></li>
        <li><a href="disabilitiesGroup.php">DISABILITIES</a></li>
        <li><a href="deceased.php">DECEASED</a></li>
        <li><a href="barangay_services.php">ADD SERVICE</a></li>
        <li><a href="logout.php">LOGOUT</a></li>
      </ul>
    </nav>
  </header>

  <!-- MAIN CONTENT -->
  <main class="p-6 max-w-5xl mx-auto">

    <!-- FORM SECTION -->
    <section class="bg-white p-6 rounded-2xl shadow-md mb-8">
      <h2 class="text-xl font-semibold mb-4">Record a Service</h2>
      <form method="POST" class="grid md:grid-cols-2 gap-4">

        <!-- Resident Name -->
        <div>
          <label class="block text-gray-700">Resident Name</label>
          <input type="text" name="resident_name" class="w-full p-2 border rounded-lg" placeholder="Enter full name" required>
        </div>

        <!-- Address -->
        <div>
          <label class="block text-gray-700">Address</label>
          <input type="text" name="address" class="w-full p-2 border rounded-lg" placeholder="Enter address" required>
        </div>

        <!-- Service Category -->
        <div>
          <label class="block text-gray-700">Service Category</label>
          <select id="serviceCategory" name="service_category" class="w-full p-2 border rounded-lg" onchange="updateSubservices()" required>
            <option value="">-- Select --</option>
            <option value="health">Health Services</option>
            <option value="social">Social Services</option>
            <option value="disaster">Disaster & Relief Services</option>
          </select>
        </div>

        <!-- Sub-service -->
        <div>
          <label class="block text-gray-700">Sub-Service</label>
          <select id="subService" name="sub_service" class="w-full p-2 border rounded-lg" required>
            <option value="">-- Select Category First --</option>
          </select>
        </div>

        <!-- Date -->
        <div>
          <label class="block text-gray-700">Date of Service</label>
          <input type="date" name="service_date" class="w-full p-2 border rounded-lg" required>
        </div>

        <!-- Contact -->
        <div>
          <label class="block text-gray-700">Contact Number</label>
          <input type="tel" name="contact" class="w-full p-2 border rounded-lg" placeholder="09XXXXXXXXX" required>
        </div>

        <!-- Submit -->
        <div class="md:col-span-2">
          <button type="submit" class="w-full bg-green-600 text-white p-3 rounded-lg hover:bg-green-800">
            Save Service Record
          </button>
        </div>
      </form>
    </section>

    <!-- RECORD LIST -->
    <section class="bg-white p-6 rounded-2xl shadow-md">
      <h2 class="text-xl font-semibold mb-4">Service Records</h2>

      <div class="overflow-x-auto">
        <table class="w-full border-collapse border border-gray-300">
          <thead class="bg-gray-200">
            <tr>
              <th class="border p-2">#</th>
              <th class="border p-2">Resident</th>
              <th class="border p-2">Address</th>
              <th class="border p-2">Category</th>
              <th class="border p-2">Sub-Service</th>
              <th class="border p-2">Date</th>
              <th class="border p-2">Contact</th>
            </tr>
          </thead>
          <tbody>
            <?php if (count($records) > 0): ?>
              <?php foreach ($records as $row): ?>
                <tr>
                  <td class="border p-2 text-center"><?= htmlspecialchars($row['id']) ?></td>
                  <td class="border p-2"><?= htmlspecialchars($row['resident_name']) ?></td>
                  <td class="border p-2"><?= htmlspecialchars($row['address']) ?></td>
                  <td class="border p-2"><?= htmlspecialchars($row['category']) ?></td>
                  <td class="border p-2"><?= htmlspecialchars($row['sub_service']) ?></td>
                  <td class="border p-2 text-center"><?= htmlspecialchars($row['service_date']) ?></td>
                  <td class="border p-2"><?= htmlspecialchars($row['contact']) ?></td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="7" class="border p-2 text-center">No records yet.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </section>

  </main>

  <!-- FOOTER -->
  <footer class="bg-gray-800 text-gray-200 text-center p-4 mt-6">
    <!-- <p>&copy; 2025 Barangay Services Management. All Rights Reserved.</p> -->
  </footer>

  <!-- SCRIPT -->
  <script>
    function updateSubservices() {
      const category = document.getElementById("serviceCategory").value;
      const subService = document.getElementById("subService");

      let options = "<option value=''>-- Select --</option>";

      if (category === "health") {
        options += "<option value='immunization'>Immunization</option>";
        options += "<option value='maternal'>Maternal Care</option>";
        options += "<option value='medical_mission'>Medical Mission</option>";
      } else if (category === "social") {
        options += "<option value='senior'>Assistance for Senior Citizens</option>";
        options += "<option value='pwd'>Assistance for PWDs</option>";
        options += "<option value='indigent'>Assistance for Indigents</option>";
      } else if (category === "disaster") {
        options += "<option value='relief'>Disaster & Relief Services</option>";
      }

      subService.innerHTML = options;
    }
  </script>

</body>
</html>
