<?php
session_start();
include('db_connect.php'); // Database connection

// Fetch complaints from the database
$query = "SELECT * FROM complaints ORDER BY ComplaintID DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>📢 Complaints Dashboard</title>
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
            overflow-x: auto;
        }
        h2 {
            color: #f8b400;
            margin-bottom: 20px;
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
            padding: 15px;
            text-align: left;
        }
        th {
            background: #f8b400;
            color: white;
        }
        tr:nth-child(even) {
            background: #f2f2f2;
        }
        tr:hover {
            background: #c97c00;
            color: white;
        }
        .complaint {
            font-weight: bold;
            color: #d9534f;
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
        <h2>📜 All Registered Complaints</h2>
        <table border="1">
            <tr>
                <th>🆔 Complaint ID</th>
                <th>👤 Citizen ID</th>
                <th>🏡 Panchayat ID</th>
                <th>📝 Description</th>
                <th>👤 Citizen Name</th>
            </tr>

            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$row['ComplaintID']}</td>
                            <td>{$row['CitizenID']}</td>
                            <td>{$row['PanchayatID']}</td>
                            <td class='complaint'>{$row['Description']}</td>
                            <td>{$row['Name_of_citizen']}</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='5'>No complaints found.</td></tr>";
            }
            $conn->close();
            ?>

        </table>
        <button class="back-button" onclick="history.back()">🔙 Go Back</button>
    </div>

</body>
</html>