<?php
include('db_connect.php'); // Ensure database connection

// Function to generate the next PanchayatID
function generatePanchayatID($conn) {
    $query = "SELECT MAX(PANCHAYATID) AS max_id FROM citizen";
    $result = $conn->query($query);
    $row = $result->fetch_assoc();
    $max_id = $row['max_id'];

    if ($max_id) {
        $num = (int)substr($max_id, 1) + 1;
        return 'P' . str_pad($num, 3, '0', STR_PAD_LEFT);
    } else {
        return 'P001';
    }
}

// Function to generate a random DOB
function generateRandomDOB() {
    $start = strtotime("1970-01-01");
    $end = strtotime("2000-12-31");
    $timestamp = mt_rand($start, $end);
    return date("Y-m-d", $timestamp);
}

// Function to randomly select an Educational Qualification
function getRandomEducationalQualification() {
    $qualifications = ["Bachelor of Science", "Master of Arts", "Diploma in Engineering", "MBBS passout", "12th pass"];
    return $qualifications[array_rand($qualifications)];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userid = $_POST['userid'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $password = $_POST['password']; // Store password as plain text (Not recommended)
    
    // Check if user already exists
    $check_query = "SELECT * FROM user WHERE userid = ? OR email = ?";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("ss", $userid, $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $error_message = "User ID or Email already exists!";
    } else {
        // Insert new user into the user table
        $query = "INSERT INTO user (userid, name, email, role, password) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssss", $userid, $name, $email, $role, $password);
        
        if ($stmt->execute()) {
            // Insert into role-specific tables
            switch ($role) {
                case 'citizen':
                    $citizen_id = "C" . substr($userid, 1); // Generate Citizen ID
                    $panchayat_id = generatePanchayatID($conn); // Generate Panchayat ID
                    $dob = generateRandomDOB(); // Generate random DOB
                    $educational_qualification = getRandomEducationalQualification(); // Randomly select educational qualification
                    $query = "INSERT INTO citizen (CITIZENID, UserID, NAME, PANCHAYATID, DOB, EDUCATIONAL_QUALIFICATION) VALUES (?, ?, ?, ?, ?, ?)";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("ssssss", $citizen_id, $userid, $name, $panchayat_id, $dob, $educational_qualification);
                    break;
                case 'panchayat_employee':
                    $employee_id = "PE" . substr($userid, 1);
                    $query = "INSERT INTO panchayat_employee (EmployeeID, Name) VALUES (?, ?)";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("ss", $employee_id, $name);
                    break;
                case 'government_monitor':
                    $monitor_id = "GM" . substr($userid, 1);
                    $query = "INSERT INTO government_monitor (monitorID, NAME) VALUES (?, ?)";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("ss", $monitor_id, $name);
                    break;
                case 'system_admin':
                    $admin_id = "A" . substr($userid, 1);
                    $query = "INSERT INTO system_admin (AdminID, NAME) VALUES (?, ?)";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("ss", $admin_id, $name);
                    break;
            }
            if (isset($stmt)) {
                $stmt->execute();
            }
            echo "<script>alert('Registration successful! Please log in.'); window.location.href='login.php';</script>";
        } else {
            $error_message = "Error occurred! Please try again.";
        }
    }
    
    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Gram Panchayat Management System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .register-container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            text-align: center;
            width: 350px;
        }
        h2 {
            color: #333;
        }
        input, select, button {
            width: 90%;
            padding: 10px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }
        button {
            background: #007bff;
            color: white;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
        }
        button:hover {
            background: #0056b3;
        }
        .error-message {
            color: red;
            font-size: 14px;
            margin-top: 10px;
        }
        .login-link {
            margin-top: 10px;
            display: block;
            color: #007bff;
            text-decoration: none;
        }
        .login-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h2>Register</h2>
        <?php if (isset($error_message)) echo "<p class='error-message'>$error_message</p>"; ?>
        <form action="register.php" method="POST">
            <input type="text" name="userid" placeholder="User ID (U001, U002, etc.)" required>
            <input type="text" name="name" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <select name="role" required>
                <option value="">Select Role</option>
                <option value="citizen">Citizen</option>
                <option value="panchayat_employee">Panchayat Employee</option>
                <option value="government_monitor">Government Monitor</option>
                <option value="system_admin">System Administrator</option>
            </select>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Register</button>
        </form>
        <a href="login.php" class="login-link">Have an account? Login</a>
    </div>
</body>
</html>