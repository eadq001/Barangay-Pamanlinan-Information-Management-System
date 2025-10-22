<?php
// ==== (Optional) Database Connection ====
// include("db_connect.php"); // Uncomment if you already have a db connection file

// Example save handler
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect posted form values here (e.g., $_POST['particulars'])
    // Then insert into DB if needed
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Barangay Budget Preparation Form No. 1</title>
<style>
    body {
        font-family: "Segoe UI", Arial, sans-serif;
        background: #f4f6f9;
        margin: 0;
        padding: 20px;
    }
    .container {
        max-width: 1400px;
        margin: auto;
        background: #fff;
        padding: 25px;
        border-radius: 8px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.15);
    }
    h2, h3, p {
        text-align: center;
        margin: 5px 0;
    }
    .top-buttons {
        margin-bottom: 15px;
        display: flex;
        gap: 10px;
    }
    .btn {
        padding: 10px 20px;
        background: #67de97ff;
        color: #1c1a1aff;
        border-radius: 6px;
        text-decoration: none;
        font-size: 15px;
        transition: background 0.3s;
        cursor: pointer;
        
    }
    .btn:hover { background: #09d094ff; }

    table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
        margin-top: 15px;
    }
    th, td {
        border: 1px solid #030202ff;
        padding: 6px;
        text-align: center;
        vertical-align: middle;
    }
    th {
        background: #67de97ff;
        color: #0c0303ff;
        font-size: 18px;
        
    }
    td input {
        width: 100%;
        border: none;
        text-align: center;
        font-size: 14px;
        padding: 3px;
    }
    td input:focus {
        outline: none;
    }
    .section-title {
        font-weight: bold;
        background: #dce3ec;
        text-align: left;
    }

    .certify {
        margin-top: 20px;
        font-size: 14px;
        text-align: justify;
    }
    .signatories {
        margin-top: 40px;
        display: flex;
        justify-content: space-around;
        flex-wrap: wrap;
    }
    .sign-box {
        text-align: center;
        margin: 15px;
        width: 250px;
    }
    .sign-boxs{
        text-align: center;
        margin: 15px;
        width: 250px;
        margin-top: 90px;
    }

    /* Print Styling */
    @page {
        size: 8.5in 13in landscape;
        margin: 10mm;
    }
    @media print {
        body { background: #fff; }
        .btn, .top-buttons { display: none; }
        .container { box-shadow: none; padding: 10px; }
        table { font-size: 11px; }
        td input { border: none; background: none; }
    }
</style>
</head>
<body>

<div class="top-buttons">
    <a href="pamanlinan.php" class="btn">⬅ Back to Dashboard</a>

</div>

<div class="container">
    <h2>Barangay Budget Preparation Form No. 1</h2>
    <h3>BUDGET OF EXPENDITURES AND SOURCES OF FINANCING, FY 2024</h3>
    <p>Barangay Pamanlinan<br>Bislig City<br>GENERAL FUND</p>

    <!-- Editable Table -->
    <form method="post">
    <table>
        <tr>
            <th>Particulars (1)</th>
            <th>Account Code (2)</th>
            <th>Income Classification (3)</th>
            <th>Past Year (4)</th>
            <th>1st Sem (5)</th>
            <th>2nd Sem (6)</th>
            <th>Total (7)</th>
            <th>Proposed (8)</th>
        </tr>

        <tr><td class="section-title">I. Beginning Balance</td><td><input name="bal_code"></td><td><input name="bal_class"></td><td><input name="bal_past"></td><td><input name="bal_sem1"></td><td><input name="bal_sem2"></td><td><input name="bal_total"></td><td><input name="bal_proposed"></td></tr>
        <tr><td class="section-title">II. Receipts</td><td><input></td><td><input></td><td><input></td><td><input></td><td><input></td><td><input></td><td><input></td></tr>
        <tr><td>Source: External</td><td><input></td><td><input></td><td><input></td><td><input></td><td><input></td><td><input></td><td><input></td></tr>
        <tr><td>10% Youth Development Fund (SK FUND)</td><td><input></td><td><input></td><td><input></td><td><input></td><td><input></td><td><input></td><td><input></td></tr>

        <tr><td class="section-title">III. Expenditures</td><td><input></td><td><input></td><td><input></td><td><input></td><td><input></td><td><input></td><td><input></td></tr>
        <tr><td>A. Maintenance and Other Operating Expenses (MOOE)</td><td><input></td><td><input></td><td><input></td><td><input></td><td><input></td><td><input></td><td><input></td></tr>
        <tr><td>Honorarium SK</td><td><input></td><td><input></td><td><input></td><td><input></td><td><input></td><td><input></td><td><input></td></tr>
        <tr><td>Members, SK Secretary & SK Treasurer</td><td><input></td><td><input></td><td><input></td><td><input></td><td><input></td><td><input></td><td><input></td></tr>
        <tr><td>Traveling Allowance</td><td><input></td><td><input></td><td><input></td><td><input></td><td><input></td><td><input></td><td><input></td></tr>
        <tr><td>Training/Seminars</td><td><input></td><td><input></td><td><input></td><td><input></td><td><input></td><td><input></td><td><input></td></tr>
        <tr><td>Office Supplies</td><td><input></td><td><input></td><td><input></td><td><input></td><td><input></td><td><input></td><td><input></td></tr>
        <tr><td>Fuel, Oil and Lubricant</td><td><input></td><td><input></td><td><input></td><td><input></td><td><input></td><td><input></td><td><input></td></tr>
        <tr><td>Fidelity Bond</td><td><input></td><td><input></td><td><input></td><td><input></td><td><input></td><td><input></td><td><input></td></tr>
        <tr><td>Annual Dues</td><td><input></td><td><input></td><td><input></td><td><input></td><td><input></td><td><input></td><td><input></td></tr>
        <tr><td>Maintenance of Barangay Gym</td><td><input></td><td><input></td><td><input></td><td><input></td><td><input></td><td><input></td><td><input></td></tr>
        <tr><td>Other Services</td><td><input></td><td><input></td><td><input></td><td><input></td><td><input></td><td><input></td><td><input></td></tr>
        <tr><td><b>TOTAL MOOE</b></td><td><input></td><td><input></td><td><input></td><td><input></td><td><input></td><td><input></td><td><input></td></tr>

        <tr><td class="section-title">B. SK YOUTH DEVELOPMENT AND EMPOWERMENT PROGRAMS</td><td><input></td><td><input></td><td><input></td><td><input></td><td><input></td><td><input></td><td><input></td></tr>
        <tr><td>GLOBAL MOBILITY - Equitable Access to Quality Education</td><td><input></td><td><input></td><td><input></td><td><input></td><td><input></td><td><input></td><td><input></td></tr>
        <tr><td>ENVIRONMENT - Green Brigade</td><td><input></td><td><input></td><td><input></td><td><input></td><td><input></td><td><input></td><td><input></td></tr>
        <tr><td>ENVIRONMENT - Disaster Preparedness Seminar</td><td><input></td><td><input></td><td><input></td><td><input></td><td><input></td><td><input></td><td><input></td></tr>
        <tr><td>HEALTH AND ANTI-DRUG ABUSE - *KKDAT</td><td><input></td><td><input></td><td><input></td><td><input></td><td><input></td><td><input></td><td><input></td></tr>
        <tr><td>GENDER SENSITIVITY - Linggo ng Kabataan / PYAP / KK Assembly</td><td><input></td><td><input></td><td><input></td><td><input></td><td><input></td><td><input></td><td><input></td></tr>
        <tr><td>SPORTS DEVELOPMENT - Support to Sports & Yaw-yan Siga Bislig</td><td><input></td><td><input></td><td><input></td><td><input></td><td><input></td><td><input></td><td><input></td></tr>
        <tr><td><b>TOTAL SK DEVELOPMENT AND EMPOWERMENT PROGRAMS</b></td><td><input></td><td><input></td><td><input></td><td><input></td><td><input></td><td><input></td><td><input></td></tr>
        <tr><td><b>TOTAL 10% SK FUND</b></td><td><input></td><td><input></td><td><input></td><td><input></td><td><input></td><td><input></td><td><input></td></tr>
    </table>
    </form>

    <!-- Certification -->
    <div class="certify">
        <p>We hereby certify that the information presented above are true and correct. We further certify that the foregoing estimated receipts are reasonably projected as collectable for the Budget Year.</p>
    </div>

    <!-- Signatories -->
    <div class="signatories">
        <div class="sign-box">
            <p><b>Prepared by:</b></p><br><br>
            <p><u>JHON LIEVERT G. MANGUYAB</u><br>SK Secretary</p>
        </div>
        <div class="sign-boxs">
            <p><u>BERNADETH H. ALVAR</u><br>SK Treasurer</p>
        </div>
        <div class="sign-box">
            <p><b>Approved by:</b></p><br><br>
            <p><u>DARWIN C. REBUITA</u><br>SK Chairperson</p>
        </div>
    </div>
</div>

</body>
</html>
