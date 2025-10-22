<?php
// === Database Connection ===
$conn = new mysqli("localhost", "root", "", "pamanlinan_db"); 
if ($conn->connect_error) { 
    die("Connection failed: " . $conn->connect_error); 
}

// === Handle Form Submission ===
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    function getValues($name) {
        return isset($_POST[$name]) ? implode(", ", $_POST[$name]) : '';
    }

    $fields = [
        'type_building','roof_material','wall_material','state_repair','floor_area','year_built',
        'monthly_rental','source_income','tenure_status','acquisition','electricity_source',
        'fuel_cooking','fuel_lighting','garbage_disposal','water_drinking','water_cooking',
        'water_laundry','toilet_facility','internet_access','household_devices'
    ];

    $values = [];
    foreach ($fields as $f) $values[$f] = getValues($f);

    $stmt = $conn->prepare("INSERT INTO household_housing 
    (type_building, roof_material, wall_material, state_repair, floor_area, year_built, monthly_rental, 
     source_income, tenure_status, acquisition, electricity_source, fuel_cooking, fuel_lighting, 
     garbage_disposal, water_drinking, water_cooking, water_laundry, toilet_facility, internet_access, 
     household_devices)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
     
    $stmt->bind_param(
        "ssssssssssssssssssss",
        $values['type_building'],$values['roof_material'],$values['wall_material'],$values['state_repair'],
        $values['floor_area'],$values['year_built'],$values['monthly_rental'],$values['source_income'],
        $values['tenure_status'],$values['acquisition'],$values['electricity_source'],$values['fuel_cooking'],
        $values['fuel_lighting'],$values['garbage_disposal'],$values['water_drinking'],$values['water_cooking'],
        $values['water_laundry'],$values['toilet_facility'],$values['internet_access'],$values['household_devices']
    );

    if ($stmt->execute()) {
        $last_id = $conn->insert_id;
        echo "<script>
            window.onload = function() {
                document.getElementById('popup').classList.add('show');
                setTimeout(() => {
                    window.location.href = 'view_survey.php?id=$last_id';
                }, 2000);
            }
        </script>";
    } else {
        echo "<script>alert('Error saving data!');</script>";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Household / Housing Survey Form</title>
<style>
:root {
    --primary: #0b6b2d;
    --primary-light: #2e8b57;
    --bg: #f3f7f4;
    --white: #ffffff;
    --text: #222;
    --shadow: 0 4px 10px rgba(0,0,0,0.1);
}
body {
    font-family: "Segoe UI", Arial, sans-serif;
    background: var(--bg);
    margin: 0;
    color: var(--text);
}
header {
    background: var(--primary);
    color: var(--white);
    padding: 1.2rem 2rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    box-shadow: var(--shadow);
}
header h1 {
    font-size: 1.4rem;
}
header a {
    color: var(--white);
    text-decoration: none;
    font-weight: 600;
    background: rgba(255,255,255,0.15);
    padding: 0.4rem 1rem;
    border-radius: 6px;
    transition: 0.3s;
}
header a:hover {
    background: rgba(255,255,255,0.3);
}

/* === FORM CONTAINER === */
.container {
    max-width: 1300px;
    background: var(--white);
    margin: 2rem auto;
    padding: 2rem;
    border-radius: 12px;
    box-shadow: var(--shadow);
}
.container h1 {
    text-align: center;
    color: var(--primary-dark);
}
.container p {
    text-align: center;
    margin-bottom: 2rem;
    color: #555;
}

/* === GRID FORM LAYOUT === */
.form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(380px, 1fr));
    gap: 1.5rem;
}
.section {
    border: 1px solid #d9d9d9;
    border-radius: 10px;
    padding: 1rem 1.5rem;
    background: #fdfdfd;
    box-shadow: inset 0 1px 3px rgba(0,0,0,0.05);
    transition: 0.3s;
}
.section:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow);
}
.section h2 {
    font-size: 1.1rem;
    color: var(--primary);
    margin-bottom: 0.8rem;
    text-align: center;
    border-bottom: 2px solid var(--primary);
    padding-bottom: 0.3rem;
}
label {
    display: block;
    font-size: 0.95rem;
    margin-bottom: 6px;
}
input[type="checkbox"] {
    margin-right: 6px;
    transform: scale(1.1);
    accent-color: var(--primary);
}
input[type="text"] {
    width: 70%;
    padding: 6px 8px;
    border: 1px solid #ccc;
    border-radius: 5px;
}

/* === BUTTON === */
button {
    display: block;
    width: 220px;
    margin: 2rem auto;
    background: var(--primary);
    color: var(--white);
    font-size: 1rem;
    padding: 0.8rem;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    box-shadow: var(--shadow);
    transition: background 0.3s, transform 0.2s;
}
button:hover {
    background: var(--primary-light);
    transform: translateY(-2px);
}

/* === POPUP STYLE === */
.popup {
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: rgba(0,0,0,0.4);
    display: none;
    justify-content: center;
    align-items: center;
    z-index: 999;
}
.popup.show { display: flex; }
.popup-content {
    background: white;
    padding: 2rem 3rem;
    border-radius: 12px;
    box-shadow: var(--shadow);
    text-align: center;
    animation: scaleIn 0.4s ease;
}
.popup-content h3 {
    color: var(--primary);
    font-size: 1.5rem;
}
.popup-content p {
    margin-top: 10px;
    color: #555;
}
@keyframes scaleIn {
    from { transform: scale(0.8); opacity: 0; }
    to { transform: scale(1); opacity: 1; }
}
</style>
</head>
<body>

<header>
    <h1>Barangay Pamanlinan Information System</h1>
    <a href="pamanlinan.php">⬅ Back to Dashboard</a>
</header>

<form method="POST" class="container">
    <h1>Household / Housing Survey Form</h1>
    <p>Republic of the Philippines • Province of Surigao del Sur<br>City of Bislig • Barangay Pamanlinan</p>

    <div class="form-grid">
        <?php
        function checkboxGroup($title,$name,$items){
            echo "<div class='section'><h2>$title</h2>";
            foreach($items as $item){
                if(strpos($item,'Others')!==false){
                    echo "<label><input type='checkbox' name='{$name}[]' value='Others'> Others, Specify <input type='text' name='{$name}[]'></label>";
                } else {
                    echo "<label><input type='checkbox' name='{$name}[]' value='$item'> $item</label>";
                }
            }
            echo "</div>";
        }

        checkboxGroup("Type of Building/House","type_building",["Single House","Duplex","Apartment/Accessoria/Rowhouse/Townhouse","Commercial/Industrial/Agricultural Building used as dwelling unit","Institutional living quarter (school dormitory, hospital, etc.)","Others"]);
        checkboxGroup("Construction Materials of the Roof","roof_material",["Strong materials (Galvanized iron/Aluminum/Tile/Concrete/Clay tile/Asbestos)","Light materials (Cogon/Nipa/Anahaw)","Salvaged/Makeshift/Improvised materials","Others"]);
        checkboxGroup("Outer Wall Materials","wall_material",["Strong materials (Concrete/Brick/Stone/Wood)","Light materials (Bamboo/Salvaged materials)","Makeshift/Improvised materials","Others"]);
        checkboxGroup("State of Repair","state_repair",["Needs no repair","Needs minor repair","Needs major repair","Dilapidated/Condemned","Under construction","Unfinished construction","Others"]);
        checkboxGroup("Floor Area","floor_area",["Below 10 sq. m","10-19 sq. m","20-29 sq. m","30-39 sq. m","40-49 sq. m","50-69 sq. m","70-99 sq. m","100 sq. m and over"]);
        checkboxGroup("Year Built","year_built",["2000 & later","1991-2000","1981-1990","1971-1980","1961-1970","1960 & earlier"]);
        checkboxGroup("Monthly Rental","monthly_rental",["None (Owned)","Below PHP 1000","PHP 1001 - 3000","PHP 3001 - 5000","PHP 5001 - 10000","PHP 10001 and over"]);
        checkboxGroup("Source of Income","source_income",["Wages/Salary","Entrepreneurial activities","Domestic household activities","Others"]);
        checkboxGroup("Tenure Status","tenure_status",["Owned/being amortized by owner","Rented","Rent-free with consent of owner","Rent-free without consent of owner","Others"]);
        checkboxGroup("Acquisition of Unit","acquisition",["Purchased","Constructed by owner","Government housing program","Inherited","Others"]);
        checkboxGroup("Electricity Source","electricity_source",["Solar","Generator","Others"]);
        checkboxGroup("Fuel for Cooking","fuel_cooking",["LPG","Kerosene","Wood","Others"]);
        checkboxGroup("Fuel for Lighting","fuel_lighting",["Electricity","Kerosene","LPG","Oil","Others","None"]);
        checkboxGroup("Usual Manner of Garbage Disposal","garbage_disposal",["Picked up by garbage truck","Burning","Composting","Burying","Feeding to animals","Others"]);
        checkboxGroup("Source of Water Supply (Drinking)","water_drinking",["Water District","Deep Well","Shared Well","Spring","Rain","Bottled","Others"]);
        checkboxGroup("Source of Water Supply (Cooking)","water_cooking",["Water District","Deep Well","Shared Well","Spring","Rain","Bottled","Others"]);
        checkboxGroup("Source of Water Supply (Laundry/Bathing)","water_laundry",["Water District","Deep Well","Shared Well","Spring","Rain","Bottled","Others"]);
        checkboxGroup("Kind of Toilet Facility","toilet_facility",["Water-sealed (exclusive)","Water-sealed (shared)","Closed pit","Open pit","Others","None"]);
        checkboxGroup("Internet Access","internet_access",["Yes","No"]);
        checkboxGroup("Household Devices","household_devices",["Television","Cellphone","Computer","Refrigerator","Washing Machine","Vehicle","Others"]);
        ?>
    </div>

    <button type="submit">Submit Survey</button>
</form>

<!-- Popup Dialog -->
<div class="popup" id="popup">
  <div class="popup-content">
    <h3>✅ Survey Submitted Successfully!</h3>
    <p>Redirecting to the view page...</p>
  </div>
</div>

</body>
</html>
