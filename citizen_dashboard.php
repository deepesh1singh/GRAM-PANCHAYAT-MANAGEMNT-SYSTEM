<?php
session_start();
include('db_connect.php'); // Ensure database connection

// Check if user is logged in and is a citizen
if (!isset($_SESSION['userid']) || $_SESSION['role'] !== 'citizen') {
    header("Location: login.php"); // Redirect to login if not a citizen
    exit();
}

$userid = $_SESSION['userid'];

// Fetch citizen details from the database
$query = "SELECT CITIZENID, NAME, DOB, PANCHAYATID, EDUCATIONAL_QUALIFICATION FROM citizen WHERE UserID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $userid);
$stmt->execute();
$result = $stmt->get_result();
$citizen = $result->fetch_assoc();
$panchayat_id = $citizen['PANCHAYATID'];

$stmt->close();

// Fetch related data from other tables
function fetchData($conn, $table, $panchayat_id) {
    $query = "SELECT * FROM $table WHERE PANCHAYATID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $panchayat_id);
    $stmt->execute();
    return $stmt->get_result();
}

$education_data = fetchData($conn, 'education_data', $panchayat_id);
$health_data = fetchData($conn, 'health_data', $panchayat_id);
$agriculture_data = fetchData($conn, 'agriculture_data', $panchayat_id);
$village_data = fetchData($conn, 'village_data', $panchayat_id);
$welfare_scheme = fetchData($conn, 'welfare_schemes', $panchayat_id);

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Citizen Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            text-align: center;
            padding: 20px;
        }
        .dashboard {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            display: inline-block;
            width: 90%;
        }
        h2 {
            color: #333;
            border-bottom: 2px solid #007bff;
            padding-bottom: 5px;
        }
        .details, .data-section {
            margin-top: 20px;
            padding: 15px;
            background: #e3f2fd;
            border-radius: 10px;
        }
        .table-container {
            margin-top: 15px;
            background: white;
            padding: 10px;
            border-radius: 8px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background: #007bff;
            color: white;
        }
        .logout {
            margin-top: 20px;
        }

        .logout, .complaints {
            margin-top: 20px;
        }
        .logout a, .complaints a {
            background: #d9534f;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s, transform 0.3s;
        }
        .logout a:hover, .complaints a:hover {
            background: #c9302c;
            transform: scale(1.05);
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
    <div class="dashboard">
        <h2>Welcome, <?php echo htmlspecialchars($citizen['NAME']); ?>!</h2>
        <div class="details">
            <p><strong>Citizen ID:</strong> <?php echo htmlspecialchars($citizen['CITIZENID']); ?></p>
            <p><strong>Date of Birth:</strong> <?php echo htmlspecialchars($citizen['DOB']); ?></p>
            <p><strong>Panchayat ID:</strong> <?php echo htmlspecialchars($citizen['PANCHAYATID']); ?></p>
        </div>
        
        <?php function displayTable($data, $title) {
            if ($data->num_rows > 0) {
                echo "<div class='data-section'><h2>$title</h2><div class='table-container'><table><tr>";
                while ($fieldinfo = $data->fetch_field()) {
                    echo "<th>" . htmlspecialchars($fieldinfo->name) . "</th>";
                }
                echo "</tr>";
                while ($row = $data->fetch_assoc()) {
                    echo "<tr>";
                    foreach ($row as $value) {
                        echo "<td>" . htmlspecialchars($value) . "</td>";
                    }
                    echo "</tr>";
                }
                echo "</table></div></div>";
            }
        }
        displayTable($education_data, 'Educational Data');
        displayTable($health_data, 'Health Data');
        displayTable($agriculture_data, 'Agriculture Data');
        displayTable($village_data, 'Village Data');
        displayTable($welfare_scheme, 'Welfare Schemes');
        ?>
        
        <div class="complaints">
            <a href="complaints.php">Enter Complaints</a>
        </div>
        
        <div class="logout">
            <a href="logout.php">Logout</a>
        </div>
    </div>
</body>
</html>