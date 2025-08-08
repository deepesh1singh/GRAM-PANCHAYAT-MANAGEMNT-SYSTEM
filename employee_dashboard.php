<?php
session_start();
include('db_connect.php'); // Database connection

// Check if the user is logged in and is a panchayat employee
if (!isset($_SESSION['userid']) || $_SESSION['role'] !== 'panchayat_employee') {
    header("Location: login.php"); // Redirect to login if not authorized
    exit();
}

$employee_id = $_SESSION['userid'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            color: white;
            text-align: center;
            padding: 20px;
        }
        .dashboard {
            background: rgba(255, 255, 255, 0.1);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            width: 60%;
            margin: auto;
        }
        h2 {
            color: #f8b400;
            font-size: 2em;
            margin-bottom: 20px;
        }
        .button-container {
            margin-top: 20px;
        }
        button {
            background: #3498db;
            color: white;
            border: none;
            padding: 15px 25px;
            font-size: 18px;
            cursor: pointer;
            border-radius: 8px;
            margin: 10px;
            display: inline-block;
            transition: background 0.3s, transform 0.3s;
        }
        button:hover {
            background: #2980b9;
            transform: scale(1.05);
        }
        button:active {
            transform: scale(1);
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
    <div class="dashboard">
        <h2>Welcome, System Admin! 👨‍💼</h2>
        <div class="button-container">
            <button onclick="window.location.href='enter_data.php'">📝 Enter Data</button>
            <button onclick="window.location.href='modify_data.php'">✏️ Modify Data</button>
            <button onclick="window.location.href='query_data.php'">🔍 Query Data</button>
            <button onclick="window.location.href='fetch_complaints.php'">📢 Fetch Complaints</button>
        </div>
    </div>
    <div class="logout">
        <a href="logout.php">Logout</a>
    </div>
</body>
</html>