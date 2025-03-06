<?php
// เปิดแสดงข้อผิดพลาดเพื่อช่วยในการดีบัก
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect to login if not logged in
    exit();
}

// Get user_id from the session
$user_id = $_SESSION['user_id'];

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database credentials
$host = "localhost";
$username = "root"; // default username for XAMPP
$password = ""; // default password for XAMPP (usually empty)
$dbname = "softwareengineer";

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // ตรวจสอบว่ามีฟิลด์ทั้งหมดถูกส่งมา
    if (isset($_POST['sub_id'], $_POST['sub_name'], $_POST['date'], $_POST['st_time'], $_POST['end_time'], $_POST['room'], $_POST['roll'])) {
        $sub_id = $_POST['sub_id'];
        $sub_name = $_POST['sub_name'];
        $date = $_POST['date'];
        $st_time = $_POST['st_time'];
        $end_time = $_POST['end_time'];
        $room = $_POST['room'];
        $roll = $_POST['roll'];
        $user_id = $_POST['user_id'];

        // ตรวจสอบว่าข้อมูลไม่ว่างเปล่า
        if (empty($sub_id) || empty($sub_name) || empty($date) || empty($st_time) || empty($end_time) || empty($room) || empty($roll)) {
            echo "<div class='alert alert-danger'>All fields are required.</div>";
        } else {
            // Prepare an insert statement
            $insert_sql = "INSERT INTO subject (sub_id, sub_name, date, st_time, end_time, room, roll, user_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($insert_sql);

            if ($stmt === false) {
                die("Failed to prepare statement: " . $conn->error);
            }

            // เนื่องจาก sub_id เป็น VARCHAR และ user_id เป็น INT ใช้ "ssssssi"
            $stmt->bind_param("sssssssi", $sub_id, $sub_name, $date, $st_time, $end_time, $room, $roll, $user_id);

            if ($stmt->execute()) {
                echo "<div class='alert alert-success'>Exam data added successfully!</div>";
            } else {
                echo "<div class='alert alert-danger'>Error adding exam: " . $stmt->error . "</div>";
            }

            $stmt->close();
        }
    } else {
        echo "<div class='alert alert-danger'>Invalid form submission.</div>";
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Exam</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            position: relative;
        }
        .back-button {
            position: absolute;
            top: 20px;
            left: 20px;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .back-button:hover {
            background-color: #45a049;
        }
        .container {
            width: 50%;
            margin: 80px auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        input[type="text"],
        input[type="file"],
        input[type="number"],
        textarea,
        select {
            margin-bottom: 10px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        input[type="submit"] {
            padding: 10px;
            border: none;
            background-color: #4CAF50;
            color: white;
            border-radius: 5px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
        .dropdown {
            position: absolute;
            top: 20px;
            right: 20px;
        }

        .dropbtn {
            background-color: transparent; /* Set to transparent */
            color: #333; /* Adjust text color for visibility */
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .dropdown .dropbtn img {
            width: 30px; /* Adjust size as needed */
            height: auto; /* Maintain aspect ratio */
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #f9f9f9; /* Keep this if you want a background for dropdown items */
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            z-index: 1;
        }

        .dropdown-content a {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }

        .dropdown-content a:hover {
            background-color: #f1f1f1; /* Background color on hover */
        }

        .dropdown:hover .dropdown-content {
            display: block;
        }

        .dropdown:hover .dropbtn {
            background-color: transparent; /* Ensure it stays transparent on hover */
        }
        
        .dropdown .dropbtn img {
            width: 50px; /* Adjust width as needed */
            height: auto; /* Maintain aspect ratio */
            background-color: transparent; /* Ensure background is transparent */
        }
    </style>
<body>

<button class="back-button" onclick="window.location.href='examagent.php'">Back</button>

<div class="dropdown">
        <div class="dropbtn">
            <img src="profile.png" alt="Profile Image"> <!-- Image for dropdown -->
        </div>
        <div class="dropdown-content">
            <a href="editprofile.php?user_id=<?php echo $user_id; ?>">Profile</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>

<div class="container mt-5">
    <h2>Add Exam</h2>

    <!-- Form to add new exam data -->
    <form action="exammanage.php" method="post">
        <div class="form-group">
            <label for="sub_id">Exam ID:</label>
            <input type="text" class="form-control" name="sub_id" required>
        </div>
        <div class="form-group">
            <label for="sub_name">Subject Name:</label>
            <input type="text" class="form-control" name="sub_name" required>
        </div>
        <div class="form-group">
            <label for="room">Room:</label>
            <input type="text" class="form-control" name="room" required>
        </div>
        <div class="form-group">
            <label for="date">Date:</label>
            <input type="date" class="form-control" name="date" required>
        </div>
        <div class="form-group">
            <label for="st_time">Start Time:</label>
            <input type="time" class="form-control" name="st_time" required>
        </div>
        <div class="form-group">
            <label for="end_time">End Time:</label>
            <input type="time" class="form-control" name="end_time" required>
        </div>
        <div class="form-group">
            <label for="roll">Row:</label>
            <input type="text" class="form-control" name="roll" required>
        </div>
            
        <input type="hidden" class="user_id" name="user_id" value="<?php echo $user_id; ?>">

        <button type="submit" class="btn btn-primary">Add Exam</button>
    </form>
</div>

<!-- Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
