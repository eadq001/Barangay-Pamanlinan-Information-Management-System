<?php
// Migration to create cases, mediations and status counts tables
require_once __DIR__ . '/../connection.php';

$queries = [
    "CREATE TABLE IF NOT EXISTS `cases` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `case_number` VARCHAR(50) NOT NULL UNIQUE,
        `complainant` VARCHAR(255) NOT NULL,
        `respondent` VARCHAR(255) DEFAULT NULL,
        `incident_date` DATE DEFAULT NULL,
        `category` VARCHAR(100) DEFAULT NULL,
        `description` TEXT,
        `status` VARCHAR(50) DEFAULT 'Open',
        `created_by` INT DEFAULT NULL,
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        `updated_at` DATETIME DEFAULT NULL
    ) ENGINE=InnoDB;",

    "CREATE TABLE IF NOT EXISTS `mediations` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `case_id` INT NOT NULL,
        `mediator` VARCHAR(255) DEFAULT NULL,
        `meeting_date` DATETIME DEFAULT NULL,
        `outcome` TEXT,
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (`case_id`) REFERENCES `cases`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB;",

    "CREATE TABLE IF NOT EXISTS `case_status_counts` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `status` VARCHAR(50) NOT NULL UNIQUE,
        `count` INT DEFAULT 0
    ) ENGINE=InnoDB;"
];

foreach($queries as $q){
    if(!mysqli_query($con, $q)){
        echo "Error creating tables: " . mysqli_error($con) . "\n";
    }
}

// Insert sample status rows if not exist
$statuses = ['Open','Resolved','Endorsed'];
foreach($statuses as $s){
    $check = mysqli_query($con, "SELECT id FROM case_status_counts WHERE status='".mysqli_real_escape_string($con,$s)."'");
    if(mysqli_num_rows($check) == 0){
        mysqli_query($con, "INSERT INTO case_status_counts (status,count) VALUES ('".mysqli_real_escape_string($con,$s)."',0)");
    }
}

// Insert sample cases
$sample_check = mysqli_query($con, "SELECT id FROM cases LIMIT 1");
if(mysqli_num_rows($sample_check) == 0){
    mysqli_query($con, "INSERT INTO cases (case_number, complainant, respondent, incident_date, category, description, status, created_by) VALUES
        ('C-2025-001', 'Juan Dela Cruz', 'Pedro Santos', '2025-09-01', 'Dispute', 'Neighbor dispute over boundary fence.', 'Open', NULL),
        ('C-2025-002', 'Maria Reyes', 'N/A', '2025-08-15', 'Complaint', 'Noise complaint during night hours.', 'Resolved', NULL)");
    // update counts
    mysqli_query($con, "UPDATE case_status_counts SET count = count + 1 WHERE status='Open'");
    mysqli_query($con, "UPDATE case_status_counts SET count = count + 1 WHERE status='Resolved'");
}

echo "Cases migration completed.\n";
