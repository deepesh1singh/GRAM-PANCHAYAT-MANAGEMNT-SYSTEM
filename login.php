<?php
session_start();
include('db_connect.php'); // Ensure database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $password = $_POST['password'];

    // Prepare and execute SQL query
    $query = "SELECT userid, name, role, password FROM user WHERE name = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Debugging check
        if (!isset($user['password'])) {
            die("Error: Password field is missing from database query.");
        }

        if ($password == $user['password']) {  // Use password_verify() if passwords are hashed
            $_SESSION['userid'] = $user['userid'];
            $_SESSION['role'] = strtolower(trim($user['role'])); // Normalize role
            
            // Redirect based on role
            switch ($_SESSION['role']) {
                case 'citizen':
                    header("Location: citizen_dashboard.php");
                    break;
                case 'panchayat_employee':
                    header("Location: employee_dashboard.php");
                    break;
                case 'government_monitor':
                    header("Location: monitor_dashboard.php");
                    break;
                case 'system_admin': // Ensure this matches database values
                    header("Location: admin_dashboard.php");
                    break;
                default:
                    die("Invalid role: " . htmlspecialchars($_SESSION['role'])); // Debugging
            }
            exit();
        } else {
            $error_message = "Wrong password!";
        }
    } else {
        $error_message = "No such user found!";
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
    <title>Login | Gram Panchayat Management System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .login-container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            text-align: center;
            width: 350px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        h2 {
            color: #333;
            margin-bottom: 20px;
        }
        form {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
        }
        input, button {
            width: 90%;
            padding: 10px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }
        button {
            background: #28a745;
            color: white;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
        }
        button:hover {
            background: #218838;
        }
        .error-message {
            color: red;
            font-size: 14px;
            margin-top: 10px;
        }
        .register-link {
            margin-top: 10px;
        }
        .register-link a {
            color: #007bff;
            text-decoration: none;
        }
        .register-link a:hover {
            text-decoration: underline;
        }
        .back-button {
            background: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            margin-top: 10px;
            border-radius: 5px;
        }
        .back-button:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        <?php if (isset($error_message)) echo "<p class='error-message'>$error_message</p>"; ?>
        <form action="login.php" method="POST">
            <input type="text" name="name" placeholder="Full Name" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        <div class="register-link">
            <p>New user? <a href="register.php">Create an account</a></p>
        </div>
        <button class="back-button" onclick="history.back()">Go Back</button>
    </div>
</body>
</html>