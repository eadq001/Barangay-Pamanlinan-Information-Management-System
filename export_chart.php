<?php
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

// Database Connection
try {
    $pdo = new PDO('mysql:host=localhost;dbname=pamanlinan_db', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

$chart = $_GET['chart'] ?? 'all';

function fetchData($pdo, $query) {
    $stmt = $pdo->query($query);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function createSheet($spreadsheet, $sheetName, $title, $data) {
    $sheet = $spreadsheet->createSheet();
    $sheet->setTitle(substr($sheetName, 0, 30));
    $sheet->setCellValue('A1', $title);
    $sheet->mergeCells('A1:D1');
    $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
    $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    if (!empty($data)) {
        $col = 'A';
        foreach (array_keys($data[0]) as $header) {
            $sheet->setCellValue($col . '3', $header);
            $sheet->getStyle($col . '3')->getFont()->setBold(true);
            $sheet->getStyle($col . '3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $col++;
        }

        $row = 4;
        foreach ($data as $record) {
            $col = 'A';
            foreach ($record as $value) {
                $sheet->setCellValue($col . $row, $value);
                $col++;
            }
            $row++;
        }

        $lastCol = chr(ord('A') + count($data[0]) - 1);
        $sheet->getStyle("A3:{$lastCol}" . ($row - 1))->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => '000000'],
                ],
            ],
        ]);

        foreach (range('A', $lastCol) as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }
    } else {
        $sheet->setCellValue('A3', 'No data available');
        $sheet->mergeCells('A3:D3');
        $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A3')->getFont()->setItalic(true);
    }
}

// Initialize spreadsheet
$spreadsheet = new Spreadsheet();
$spreadsheet->removeSheetByIndex(0);

if ($chart === 'all') {
    // All charts → create multiple sheets
    $charts = [
        'Population per Purok' => "SELECT purok_name AS 'Purok', COUNT(*) AS 'Population' FROM people GROUP BY purok_name",
        'Households per Purok' => "SELECT purok_name AS 'Purok', COUNT(DISTINCT household_id) AS 'Households' FROM people GROUP BY purok_name",
        'Families per Purok' => "SELECT purok_name AS 'Purok', COUNT(DISTINCT family_id) AS 'Families' FROM people GROUP BY purok_name",
        'Households with Toilet' => "SELECT purok_name AS 'Purok', COUNT(*) AS 'Households with Toilet' FROM people WHERE toilet='Yes' GROUP BY purok_name",
        'Gender Distribution per Purok' => "SELECT purok_name AS 'Purok',
            SUM(CASE WHEN sex_name='Male' THEN 1 ELSE 0 END) AS 'Male',
            SUM(CASE WHEN sex_name='Female' THEN 1 ELSE 0 END) AS 'Female'
            FROM people GROUP BY purok_name",
        'Out-of-School Youth per Purok' => "SELECT purok_name AS 'Purok', COUNT(*) AS 'Out-of-School Youth' FROM people WHERE school_youth='Yes' GROUP BY purok_name"
    ];

    foreach ($charts as $title => $query) {
        $data = fetchData($pdo, $query);
        createSheet($spreadsheet, $title, $title, $data);
    }

    $fileName = "All_Charts_Data.xlsx";
} else {
    // Single chart export
    $sheet = $spreadsheet->createSheet();
    switch ($chart) {
        case 'purok':
            $data = fetchData($pdo, "SELECT purok_name AS 'Purok', COUNT(*) AS 'Population' FROM people GROUP BY purok_name");
            $title = "Population per Purok";
            break;
        case 'household':
            $data = fetchData($pdo, "SELECT purok_name AS 'Purok', COUNT(DISTINCT household_id) AS 'Households' FROM people GROUP BY purok_name");
            $title = "Households per Purok";
            break;
        case 'families':
            $data = fetchData($pdo, "SELECT purok_name AS 'Purok', COUNT(DISTINCT family_id) AS 'Families' FROM people GROUP BY purok_name");
            $title = "Families per Purok";
            break;
        case 'toilet':
            $data = fetchData($pdo, "SELECT purok_name AS 'Purok', COUNT(*) AS 'Households with Toilet' FROM people WHERE toilet='Yes' GROUP BY purok_name");
            $title = "Households with Toilet";
            break;
        case 'gender':
            $data = fetchData($pdo, "SELECT purok_name AS 'Purok',
                SUM(CASE WHEN sex_name='Male' THEN 1 ELSE 0 END) AS 'Male',
                SUM(CASE WHEN sex_name='Female' THEN 1 ELSE 0 END) AS 'Female'
                FROM people GROUP BY purok_name");
            $title = "Gender Distribution per Purok";
            break;
        case 'osy':
            $data = fetchData($pdo, "SELECT purok_name AS 'Purok', COUNT(*) AS 'Out-of-School Youth' FROM people WHERE school_youth='Yes' GROUP BY purok_name");
            $title = "Out-of-School Youth per Purok";
            break;
        default:
            $data = [];
            $title = "No Data";
            break;
    }
    createSheet($spreadsheet, $title, $title, $data);
    $fileName = $title . ".xlsx";
}

// Output file
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $fileName . '"');
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>
