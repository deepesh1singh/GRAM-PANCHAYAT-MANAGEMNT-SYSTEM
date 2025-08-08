<?php
session_start();
include('db_connect.php'); // Database connection

// Check if user is logged in and is a panchayat employee
if (!isset($_SESSION['userid']) || $_SESSION['role'] !== 'panchayat_employee') {
    header("Location: login.php"); // Redirect to login if not an employee
    exit();
}

$selectedTable = isset($_POST['table']) ? $_POST['table'] : '';
$records = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($selectedTable)) {
    $stmt = $conn->prepare("SELECT * FROM $selectedTable");
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $records[] = $row;
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>✏️ Modify Data</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            color: white;
            text-align: center;
            padding: 20px;
        }
        .container {
            background: rgba(255, 255, 255, 0.1);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            width: 80%;
            margin: auto;
        }
        h2 {
            color: #f8b400;
            font-size: 2em;
            margin-bottom: 20px;
        }
        h3 {
            color: #ffffff;
            margin-bottom: 20px;
        }
        select, button {
            padding: 10px;
            font-size: 16px;
            margin: 10px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
        }
        select {
            background: #3498db;
            color: white;
        }
        button {
            background: #f8b400;
            color: white;
        }
        button:hover {
            background: #c97c00;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
            color: black;
            border-radius: 10px;
            overflow: hidden;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #3498db;
            color: white;
        }
        td input {
            width: 100%;
            border: none;
            padding: 5px;
            box-sizing: border-box;
        }
        .update-btn {
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #28a745;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            transition: background 0.3s, transform 0.3s;
        }
        .update-btn:hover {
            background-color: #218838;
            transform: scale(1.05);
        }
        .back-button {
            background: #007bff;
            color: white;
            padding: 10px 20px;
            border-radius: 10px;
            font-size: 18px;
            border: none;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 20px;
        }
        .back-button:hover {
            background: #0056b3;
            transform: scale(1.05);
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>✏️ Modify Data</h2>

        <!-- Table selection form -->
        <form method="POST">
            <label for="table">Select Table:</label>
            <select name="table" id="table" onchange="this.form.submit()">
                <option value="">-- Select --</option>
                <option value="agriculture_data" <?php if($selectedTable == 'agriculture_data') echo 'selected'; ?>>🌾 Agricultural Data</option>
                <option value="education_data" <?php if($selectedTable == 'education_data') echo 'selected'; ?>>📚 Education Data</option>
                <option value="health_data" <?php if($selectedTable == 'health_data') echo 'selected'; ?>>🏥 Health Data</option>
                <option value="village_data" <?php if($selectedTable == 'village_data') echo 'selected'; ?>>🏡 Village Data</option>
                <option value="welfare_schemes" <?php if($selectedTable == 'welfare_schemes') echo 'selected'; ?>>🏛️ Welfare Schemes</option>
                <option value="election_records" <?php if($selectedTable == 'election_records') echo 'selected'; ?>>🗳️ Election Records</option>
            </select>
        </form>

        <?php if ($selectedTable && count($records) > 0): ?>
            <h3>Modify Records from <?php echo htmlspecialchars($selectedTable); ?></h3>
            <form id="updateForm">
                <table>
                    <tr>
                        <?php foreach (array_keys($records[0]) as $column): ?>
                            <th><?php echo htmlspecialchars($column); ?></th>
                        <?php endforeach; ?>
                    </tr>
                    <?php foreach ($records as $row): ?>
                        <tr>
                            <?php foreach ($row as $column => $value): ?>
                                <td>
                                    <input type="text" name="<?php echo $column; ?>[]" value="<?php echo htmlspecialchars($value); ?>">
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </table>
                <button type="button" class="update-btn" onclick="updateData()">Update</button>
            </form>
        <?php elseif ($selectedTable): ?>
            <p>No records found in this table.</p>
        <?php endif; ?>

        <button class="back-button" onclick="history.back()">🔙 Go Back</button>
    </div>

    <script>
        function updateData() {
            var formData = $("#updateForm").serialize();
            $.ajax({
                url: 'update_data.php',
                type: 'POST',
                data: formData + "&table=<?php echo $selectedTable; ?>",
                success: function(response) {
                    alert(response);
                }
            });
        }
    </script>
</body>
</html>