<?php 
session_start();
include('db_connect.php'); // Ensure database connection

// Check if user is logged in and is an admin
if (!isset($_SESSION['userid']) || $_SESSION['role'] !== 'system_admin') {
    header("Location: login.php");
    exit();
}

// Fetch users with only userid, name, and password
$query = "SELECT userid, name, password FROM user";
$result = $conn->query($query);

// Handle new user addition
if (isset($_POST['add_user'])) {
    $userid = $_POST['userid'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Secure password storage

    $insert_query = "INSERT INTO user (userid, name, email, role, password) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insert_query);
    $stmt->bind_param("sssss", $userid, $name, $email, $role, $password);
    
    if ($stmt->execute()) {
        echo "<script>alert('User added successfully!'); window.location.href='admin_dashboard.php';</script>";
    } else {
        echo "<script>alert('Error adding user.');</script>";
    }
    $stmt->close();
}

// Handle user deletion
if (isset($_POST['delete_user'])) {
    $name = $_POST['delete_name'];

    $delete_query = "DELETE FROM user WHERE name = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("s", $name);

    if ($stmt->execute()) {
        echo "<script>alert('User deleted successfully!'); window.location.href='admin_dashboard.php';</script>";
    } else {
        echo "<script>alert('Error deleting user.');</script>";
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
    <title>Admin Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #1e1e2f;
            color: white;
            text-align: center;
            padding: 20px;
        }
        .dashboard {
            background: #2c2c3e;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            width: 80%;
            margin: auto;
        }
        h2 {
            color: #f8b400;
        }
        button {
            background: #f8b400;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
            margin: 10px;
        }
        button:hover {
            background: #c97c00;
        }
        .table-container, .form-container {
            background: white;
            padding: 10px;
            border-radius: 10px;
            margin-top: 20px;
            color: black;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }
        th {
            background: #f8b400;
            color: black;
        }
        input, select {
            width: 90%;
            padding: 8px;
            margin: 5px 0;
        }
        .hidden {
            display: none;
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
    <script>
        function toggleTable() {
            var tableContainer = document.getElementById("userTableContainer");
            tableContainer.style.display = tableContainer.style.display === "none" ? "block" : "none";
        }

        function toggleAddUser() {
            var form = document.getElementById("addUserForm");
            form.classList.toggle("hidden");
        }

        function toggleDeleteUser() {
            var form = document.getElementById("deleteUserForm");
            form.classList.toggle("hidden");
        }
    </script>
</head>
<body>
    <div class="dashboard">
        <h2>Admin Dashboard</h2>
        
        <button onclick="toggleTable()">See User Table</button>
        <button onclick="toggleAddUser()">Add User</button>
        <button onclick="toggleDeleteUser()">Delete User</button>
        
        <!-- User Table -->
        <div id="userTableContainer" class="table-container hidden">
            <table>
                <tr>
                    <th>User ID</th>
                    <th>Name</th>
                    <th>Password</th>
                </tr>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['userid']); ?></td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['password']); ?></td>
                </tr>
                <?php endwhile; ?>
            </table>
        </div>

        <!-- Add User Form -->
        <div id="addUserForm" class="form-container hidden">
            <h3>Add New User</h3>
            <form action="admin_dashboard.php" method="POST">
                <input type="text" name="userid" placeholder="User ID" required>
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
                <button type="submit" name="add_user">Update</button>
            </form>
        </div>

        <!-- Delete User Form -->
        <div id="deleteUserForm" class="form-container hidden">
            <h3>Delete User</h3>
            <form action="admin_dashboard.php" method="POST">
                <input type="text" name="delete_name" placeholder="Enter User Name" required>
                <button type="submit" name="delete_user">Delete</button>
            </form>
        </div>

        <div class="logout">
            <a href="logout.php">Logout</a>
        </div>
    </div>
</body>
</html>