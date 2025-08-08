<?php
session_start();
include('db_connect.php'); // Database connection

if (!isset($_SESSION['userid']) || $_SESSION['role'] !== 'panchayat_employee') {
    die("Unauthorized access.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['table'])) {
    $table = $_POST['table'];
    
    // Fetch column names
    $query = $conn->query("SHOW COLUMNS FROM $table");
    $columns = [];
    while ($row = $query->fetch_assoc()) {
        $columns[] = $row['Field'];
    }

    $rowCount = count($_POST[$columns[0]]); // Assuming first column has same count as rows

    for ($i = 0; $i < $rowCount; $i++) {
        $updateValues = [];
        foreach ($columns as $column) {
            $updateValues[] = "$column = ?";
        }

        $updateQuery = "UPDATE $table SET " . implode(', ', array_slice($updateValues, 1)) . " WHERE $columns[0] = ?";
        $stmt = $conn->prepare($updateQuery);

        $types = str_repeat("s", count($columns));
        $values = array_map(function ($col) use ($i) {
            return $_POST[$col][$i];
        }, $columns);

        // Move ID (primary key) to last for WHERE clause
        $idValue = array_shift($values);
        $values[] = $idValue;

        $stmt->bind_param($types, ...$values);
        $stmt->execute();
        $stmt->close();
    }

    echo "Data updated successfully!";
}

$conn->close();
?>
