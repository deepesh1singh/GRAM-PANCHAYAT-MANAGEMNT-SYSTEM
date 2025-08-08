<?php
session_start();
include('db_connect.php'); // Database connection

// Function to generate the next ComplaintID
function generateComplaintID($conn) {
    $query = "SELECT MAX(ComplaintID) AS max_id FROM complaints";
    $result = $conn->query($query);
    $row = $result->fetch_assoc();
    $max_id = $row['max_id'];

    if ($max_id) {
        $num = (int)substr($max_id, 3) + 1; // Change the starting index to 3 to skip 'CMP'
        return 'CMP' . str_pad($num, 3, '0', STR_PAD_LEFT);
    } else {
        return 'CMP001';
    }
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $complaint_id = generateComplaintID($conn);
    $citizen_id = $_POST['citizen_id'];
    $panchayat_id = $_POST['panchayat_id'];
    $name_of_citizen = $_POST['name_of_citizen'];
    $description = $_POST['description'];

    // Insert complaint into the database
    $query = "INSERT INTO complaints (ComplaintID, CitizenID, PanchayatID, Description, Name_of_citizen) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssss", $complaint_id, $citizen_id, $panchayat_id, $description, $name_of_citizen);

    if ($stmt->execute()) {
        echo "<script>alert('✅ Complaint submitted successfully!'); window.location='complaints.php';</script>";
    } else {
        echo "<script>alert('❌ Error submitting complaint!');</script>";
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
    <title>📜 Complaint Submission</title>
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
            width: 50%;
            margin: auto;
        }
        h2 {
            color: #f8b400;
            margin-bottom: 20px;
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        label {
            font-size: 18px;
            margin-bottom: 5px;
        }
        input, textarea {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            border: none;
            font-size: 16px;
        }
        textarea {
            height: 100px;
        }
        button {
            background: #f8b400;
            color: white;
            padding: 15px;
            border-radius: 10px;
            font-size: 18px;
            border: none;
            cursor: pointer;
            transition: 0.3s;
        }
        button:hover {
            background: #c97c00;
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
        <h2>📢 Register Your Complaint</h2>
        <form method="post" action="">
            <label>🆔 Citizen ID:</label>
            <input type="text" name="citizen_id" required>

            <label>🏡 Panchayat ID:</label>
            <input type="text" name="panchayat_id" required>

            <label>👤 Your Name:</label>
            <input type="text" name="name_of_citizen" required>

            <label>✍ Complaint Description:</label>
            <textarea name="description" required></textarea>

            <button type="submit">🚀 Submit Complaint</button>
        </form>
        <button class="back-button" onclick="history.back()">🔙 Go Back</button>
    </div>

</body>
</html>