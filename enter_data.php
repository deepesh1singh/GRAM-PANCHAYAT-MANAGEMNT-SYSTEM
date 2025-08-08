<?php
session_start();
include('db_connect.php');

// Check if user is logged in
if (!isset($_SESSION['userid']) || $_SESSION['role'] !== 'panchayat_employee') {
    header("Location: login.php");
    exit();
}

$success_msg = "";
$error_msg = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['table_name'])) {
        $table = $_POST['table_name'];

        // Define allowed tables and their attributes
        $allowed_tables = [
            "agriculture_data" => ["recordID", "PanchayatID", "Croptype", "Yeild", "Irrigation"],
            "education_data" => ["recordID", "PanchayatID", "No_of_Schools", "literacy_Rate", "No_Of_Teachers", "Village_Name"],
            "health_data" => ["RecordID", "PanchayatID", "No_Of_Hospitals", "Disease_Cases", "HealthCare_facilities", "Village_Name"],
            "village_data" => ["VillageID", "Name", "PanchayatID"],
            "welfare_schemes" => ["SchemeID", "Name", "Description", "PanchayatID", "Status"],
            "election_records" => ["ElectionID", "PanchayatID","Candidate_Name", "Party","Village_Name"]
        ];

        if (array_key_exists($table, $allowed_tables)) {
            $columns = $allowed_tables[$table];
            $values = [];
            
            // Prepare dynamic SQL query
            $placeholders = implode(", ", array_fill(0, count($columns), "?"));
            $query = "INSERT INTO $table (" . implode(", ", $columns) . ") VALUES ($placeholders)";
            $stmt = $conn->prepare($query);

            if ($stmt) {
                $types = str_repeat("s", count($columns)); // Assuming all values are strings
                foreach ($columns as $col) {
                    $values[] = $_POST[$col] ?? null;
                }

                $stmt->bind_param($types, ...$values);
                if ($stmt->execute()) {
                    $success_msg = "✅ Data successfully inserted into $table.";
                } else {
                    $error_msg = "❌ Error inserting data: " . $stmt->error;
                }
                $stmt->close();
            } else {
                $error_msg = "❌ Error preparing statement: " . $conn->error;
            }
        } else {
            $error_msg = "❌ Invalid table selected.";
        }
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>📊 Enter Data - Gram Panchayat</title>
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
            margin-bottom: 20px;
        }
        form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        select, input, button {
            margin: 10px;
            padding: 10px;
            font-size: 16px;
            width: 60%;
            border-radius: 8px;
            border: none;
        }
        button {
            background: #28a745;
            color: white;
            cursor: pointer;
            transition: background 0.3s, transform 0.3s;
        }
        button:hover {
            background: #218838;
            transform: scale(1.05);
        }
        .message {
            margin-top: 10px;
            font-size: 16px;
            font-weight: bold;
        }
        .success { color: green; }
        .error { color: red; }
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
        <h2>📊 Enter Data for Gram Panchayat</h2>
        <form method="post">
            <label for="table_name"><strong>Select Table:</strong></label>
            <select name="table_name" id="table_name" required onchange="updateForm()">
                <option value="">-- Select Table --</option>
                <option value="agriculture_data">🌾 Agriculture Data</option>
                <option value="education_data">📚 Educational Data</option>
                <option value="health_data">🏥 Health Data</option>
                <option value="village_data">🏡 Village Data</option>
                <option value="welfare_schemes">🏛️ Welfare Schemes</option>
                <option value="election_records">🗳️ Election Records</option>
            </select>

            <div id="dynamic_form">
                <!-- Fields will be generated here dynamically -->
            </div>

            <button type="submit">✅ Update Data</button>
        </form>

        <?php if ($success_msg) echo "<p class='message success'>$success_msg</p>"; ?>
        <?php if ($error_msg) echo "<p class='message error'>$error_msg</p>"; ?>

        <button class="back-button" onclick="history.back()">🔙 Go Back</button>
    </div>

    <script>
        function updateForm() {
            const table = document.getElementById("table_name").value;
            const formDiv = document.getElementById("dynamic_form");
            formDiv.innerHTML = ""; // Clear existing fields

            const fields = {
                agriculture_data: ["recordID", "PanchayatID", "Croptype", "Yeild", "Irrigation"],
                education_data: ["recordID", "PanchayatID", "No_of_Schools", "literacy_Rate", "No_Of_Teachers", "Village_Name"],
                health_data: ["RecordID", "PanchayatID", "No_Of_Hospitals", "Disease_Cases", "HealthCare_facilities", "Village_Name"],
                village_data: ["VillageID", "Name", "PanchayatID"],
                welfare_schemes: ["SchemeID", "Name", "Description", "PanchayatID", "Status"],
                election_records: ["ElectionID", "PanchayatID","Candidate_Name", "Party","Village_Name"]
            };

            if (fields[table]) {
                fields[table].forEach(field => {
                    let input = document.createElement("input");
                    input.type = "text";
                    input.name = field;
                    input.placeholder = field;
                    input.required = true;
                    formDiv.appendChild(input);
                });
            }
        }
    </script>
</body>
</html>