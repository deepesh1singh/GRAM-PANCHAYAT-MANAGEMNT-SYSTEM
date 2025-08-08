<?php
session_start();
include('db_connect.php'); // Include database connection

// Check if a table is selected
$selected_table = isset($_POST['table_name']) ? $_POST['table_name'] : '';

// Fetch table data
$data = [];
$columns = [];

if ($selected_table) {
    $query = "SELECT * FROM $selected_table";
    $result = $conn->query($query);

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        $columns = array_keys($data[0]);
    }
}

// Fetch statistics for specific tables
$chartData = [];
if ($selected_table == "education_data") {
    $chartData['villages'] = [];
    $chartData['schools'] = [];
    $chartData['literacy_rate'] = [];
    $chartData['teachers'] = [];

    foreach ($data as $row) {
        $chartData['villages'][] = $row['Village_Name'];
        $chartData['schools'][] = $row['No_Of_Schools'];
        $chartData['literacy_rate'][] = $row['Literacy_Rate'];
        $chartData['teachers'][] = $row['No_Of_Teachers'];
    }
} elseif ($selected_table == "agriculture_data") {
    $chartData['crop_type'] = [];
    $chartData['yield'] = [];

    foreach ($data as $row) {
        $chartData['crop_type'][] = $row['CropType'];
        $chartData['yield'][] = $row['Yeild'];
    }
} elseif ($selected_table == "health_data") {
    $chartData['villages'] = [];
    $chartData['disease_cases'] = [];
    $chartData['hospitals'] = [];

    foreach ($data as $row) {
        $chartData['villages'][] = $row['Village_Name'];
        $chartData['disease_cases'][] = $row['Disease_Cases'];
        $chartData['hospitals'][] = $row['No_Of_Hospitals'];
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>📊 Monitor Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        select, button {
            padding: 10px;
            font-size: 16px;
            margin: 10px;
            border-radius: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
            color: black;
            border-radius: 10px;
        }
        th, td {
            padding: 10px;
            border: 1px solid black;
        }
        .chart-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            margin-top: 20px;
        }
        canvas {
            background: white;
            border-radius: 10px;
            width: 400px; /* Set fixed width */
            height: 300px; /* Set fixed height */
        }
        .logout {
            margin-top: 20px;
        }
        .logout a {
            background: #d9534f;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s, transform 0.3s;
        }
        .logout a:hover {
            background: #c9302c;
            transform: scale(1.05);
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>📊 Government Monitor Dashboard</h2>

        <!-- Dropdown to select table -->
        <form method="post">
            <label for="table_name">Select Table:</label>
            <select name="table_name">
                <option value="agriculture_data">🌾 Agricultural Data</option>
                <option value="education_data">📚 Education Data</option>
                <option value="health_data">🏥 Health Data</option>
                <option value="village_data">🏡 Village Data</option>
                <option value="welfare_schemes">🏛️ Welfare Schemes</option>
                <option value="complaints">📢 Complaints</option>
                <option value="election_records">🗳️ Election Records</option>
            </select>
            <button type="submit">📥 Fetch Data</button>
        </form>

        <!-- Show Table Data -->
        <?php if (!empty($data)) { ?>
            <h3>📑 Data from <?php echo strtoupper($selected_table); ?> </h3>
            <table>
                <tr>
                    <?php foreach ($columns as $col) { echo "<th>$col</th>"; } ?>
                </tr>
                <?php foreach ($data as $row) { ?>
                    <tr>
                        <?php foreach ($row as $value) { echo "<td>$value</td>"; } ?>
                    </tr>
                <?php } ?>
            </table>

            <!-- Display Graphs if applicable -->
            <div class="chart-container">
                <?php if ($selected_table == "education_data") { ?>
                    <canvas id="schoolsChart"></canvas>
                    <canvas id="literacyChart"></canvas>
                    <canvas id="teachersChart"></canvas>
                <?php } elseif ($selected_table == "agriculture_data") { ?>
                    <canvas id="yieldChart"></canvas>
                <?php } elseif ($selected_table == "health_data") { ?>
                    <canvas id="diseaseChart"></canvas>
                    <canvas id="hospitalsChart"></canvas>
                <?php } ?>
            </div>
        <?php } ?>

        <script>
            const chartOptions = {
                responsive: true,
                plugins: { legend: { display: true } }
            };

            <?php if ($selected_table == "education_data") { ?>
                new Chart(document.getElementById('schoolsChart'), {
                    type: 'bar',
                    data: {
                        labels: <?php echo json_encode($chartData['villages']); ?>,
                        datasets: [{ label: 'Number of Schools', data: <?php echo json_encode($chartData['schools']); ?>, backgroundColor: '#4caf50' }]
                    },
                    options: chartOptions
                });

                new Chart(document.getElementById('literacyChart'), {
                    type: 'pie',
                    data: {
                        labels: <?php echo json_encode($chartData['villages']); ?>,
                        datasets: [{ data: <?php echo json_encode($chartData['literacy_rate']); ?>, backgroundColor: ['#ff5733', '#33c4ff', '#ffeb33', '#a933ff'] }]
                    },
                    options: chartOptions
                });

                new Chart(document.getElementById('teachersChart'), {
                    type: 'bar',
                    data: {
                        labels: <?php echo json_encode($chartData['villages']); ?>,
                        datasets: [{ label: 'Number of Teachers', data: <?php echo json_encode($chartData['teachers']); ?>, backgroundColor: '#ff9800' }]
                    },
                    options: chartOptions
                });
            <?php } elseif ($selected_table == "agriculture_data") { ?>
                new Chart(document.getElementById('yieldChart'), {
                    type: 'bar',
                    data: {
                        labels: <?php echo json_encode($chartData['crop_type']); ?>,
                        datasets: [{ label: 'Yield', data: <?php echo json_encode($chartData['yield']); ?>, backgroundColor: '#4caf50' }]
                    },
                    options: chartOptions
                });
            <?php } elseif ($selected_table == "health_data") { ?>
                new Chart(document.getElementById('diseaseChart'), {
                    type: 'bar',
                    data: {
                        labels: <?php echo json_encode($chartData['villages']); ?>,
                        datasets: [{ label: 'Disease Cases', data: <?php echo json_encode($chartData['disease_cases']); ?>, backgroundColor: '#f44336' }]
                    },
                    options: chartOptions
                });

                new Chart(document.getElementById('hospitalsChart'), {
                    type: 'bar',
                    data: {
                        labels: <?php echo json_encode($chartData['villages']); ?>,
                        datasets: [{ label: 'Number of Hospitals', data: <?php echo json_encode($chartData['hospitals']); ?>, backgroundColor: '#2196f3' }]
                    },
                    options: chartOptions
                });
            <?php } ?>
        </script>
    </div>

    <div class="logout">
        <a href="logout.php">Logout</a>
    </div>
</body>
</html>

<?php $conn->close(); ?>