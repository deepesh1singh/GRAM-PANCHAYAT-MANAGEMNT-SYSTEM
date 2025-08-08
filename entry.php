<?php
include('db_connect.php'); // Ensure connection to the database
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gram Panchayat Management System</title>
    <style>
        /* General Styles */
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
            text-align: center;
            color: #333;
        }

        /* Header */
        header {
            background: #1e5631;
            color: white;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
        }
        header h1 {
            margin: 0;
            font-size: 26px;
            text-transform: uppercase;
            font-weight: bold;
        }
        .buttons {
            margin-right: 20px;
        }
        .buttons button {
            background: #ff9800;
            color: white;
            border: none;
            padding: 12px 18px;
            cursor: pointer;
            font-size: 16px;
            border-radius: 8px;
            margin-left: 10px;
            transition: 0.3s;
        }
        .buttons button:hover {
            background: #e68900;
        }

        /* Image Gallery */
        .image-gallery {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            padding: 20px;
            gap: 15px;
        }
        .image-gallery img {
            width: 30%;
            height: auto;
            border-radius: 12px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.3);
            transition: transform 0.3s;
        }
        .image-gallery img:hover {
            transform: scale(1.05);
        }

        /* User Services Section */
        .user-services {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            padding: 40px;
            background: white;
        }
        .service {
            background: #4caf50;
            color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.3);
            transition: transform 0.3s, background 0.3s;
            position: relative;
        }
        .service:hover {
            transform: translateY(-5px);
            background: #388e3c;
        }
        .service h2 {
            margin: 0 0 10px 0;
            font-size: 22px;
        }
        .service p {
            font-size: 16px;
        }
        .service::before {
            content: '\1F4C4'; /* Default emoji */
            font-size: 30px;
            position: absolute;
            top: 15px;
            right: 15px;
        }
        #citizen::before { content: '\1F3E1'; }  /* House Emoji */
        #employee::before { content: '\1F4DD'; }  /* Document Emoji */
        #monitor::before { content: '\1F5C3'; }  /* Chart Emoji */
        #admin::before { content: '\2699'; }  /* Gear Emoji */

        /* Footer */
        footer {
            background: #1e5631;
            color: white;
            padding: 15px;
            margin-top: 20px;
            font-size: 14px;
        }
    </style>
</head>
<body>

    <!-- Header -->
    <header>
        <h1>🌿 GRAM PANCHAYAT MANAGEMENT SYSTEM</h1>
        <div class="buttons">
            <button onclick="location.href='login.php'">🔑 Login</button>
            <button onclick="location.href='register.php'">📝 Register</button>
        </div>
    </header>

    <!-- Image Section -->
    <section class="image-gallery">
        <img src="/GRAM_PANCHAYAT/assets/Screen-Shot-2017-10-09-at-18.08.04.png" alt="Village Development">
        <img src="/GRAM_PANCHAYAT/assets/OIP.png" alt="Gram Panchayat Office">
        <img src="/GRAM_PANCHAYAT/assets/Agri.png" alt="Agriculture and Welfare">
    </section>

    <!-- User Role Blocks -->
    <section class="user-services">
        <div class="service" id="citizen">
            <h2>👨‍👩‍👧‍👦 Citizen Services</h2>
            <p>View village information, welfare schemes, and development updates.</p>
        </div>
        <div class="service" id="employee">
            <h2>🏢 Panchayat Employee</h2>
            <p>Enter, modify, and query village data for better governance.</p>
        </div>
        <div class="service" id="monitor">
            <h2>📊 Government Monitor</h2>
            <p>Access reports on agriculture, health, education, and welfare.</p>
        </div>
        <div class="service" id="admin">
            <h2>⚙️ System Administrator</h2>
            <p>Manage user roles, modify, and query user data.</p>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <p>🌾 &copy; 2025 Gram Panchayat Management System | Empowering Rural India 🚜</p>
    </footer>

</body>
</html>