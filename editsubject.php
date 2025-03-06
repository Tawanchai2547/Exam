<?php
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

// Fetch the subject data for editing
$subject = null;
if (isset($_GET['sub_id'])) {
    $sub_id = intval($_GET['sub_id']); // Ensure sub_id is an integer

    $sql = "SELECT * FROM subject WHERE sub_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $sub_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $subject = $result->fetch_assoc();
    } else {
        echo "<p>Subject not found.</p>";
        exit();
    }

    $stmt->close();
} else {
    echo "<p>No subject selected for editing.</p>";
    exit();
}

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collecting form data
    $sub_id = $_GET['sub_id']; // ไม่ต้องใช้ intval

    $sub_name = $_POST['sub_name'];
    $date = $_POST['date'];
    $st_time = $_POST['st_time'];
    $end_time = $_POST['end_time'];
    $room = $_POST['room'];
    $roll = $_POST['roll'];
    $user_id = $_POST['user_id'];

    // Prepare an update statement
    $update_sql = "UPDATE subject SET sub_name = ?, date = ?, st_time = ?, end_time = ?, room = ?, roll = ?, user_id = ? WHERE sub_id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("ssssssis", $sub_name, $date, $st_time, $end_time, $room, $roll, $user_id, $sub_id);

    if ($stmt->execute()) {
        // Redirect to viewsubject.php after successful update
        header("Location: viewsubject.php?success=1");
        exit();
    } else {
        echo "<div class='alert alert-danger'>Error updating subject: " . $stmt->error . "</div>";
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
    <title>Edit Subject</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
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
    </style>
</head>
<body>

<button class="back-button" onclick="window.location.href='viewsubject.php'">Back</button>

<div class="container mt-5">
    <h2>Edit Subject</h2>

    <form action="editsubject.php?sub_id=<?php echo htmlspecialchars($subject['sub_id']); ?>" method="post">
        <div class="form-group">
            <label for="sub_id">Exam ID:</label>
            <input type="text" class="form-control" name="sub_id" value="<?php echo htmlspecialchars($subject['sub_id']); ?>" readonly>
        </div>
        <div class="form-group">
            <label for="sub_name">Subject Name:</label>
            <input type="text" class="form-control" name="sub_name" value="<?php echo htmlspecialchars($subject['sub_name']); ?>" required>
        </div>
        <div class="form-group">
            <label for="room">Room:</label>
            <input type="text" class="form-control" name="room" value="<?php echo htmlspecialchars($subject['room']); ?>" required>
        </div>
        <div class="form-group">
            <label for="date">Date:</label>
            <input type="date" class="form-control" name="date" value="<?php echo htmlspecialchars($subject['date']); ?>" required>
        </div>
        <div class="form-group">
            <label for="st_time">Start Time:</label>
            <input type="time" class="form-control" name="st_time" value="<?php echo htmlspecialchars($subject['st_time']); ?>" required>
        </div>
        <div class="form-group">
            <label for="end_time">End Time:</label>
            <input type="time" class="form-control" name="end_time" value="<?php echo htmlspecialchars($subject['end_time']); ?>" required>
        </div>
        <div class="form-group">
            <label for="roll">Row:</label>
            <input type="text" class="form-control" name="roll" value="<?php echo htmlspecialchars($subject['roll']); ?>" required>
        </div>
        
        <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($subject['user_id']); ?>">

        <button type="submit" class="btn btn-primary">Update Exam</button>
    </form>
</div>

<!-- Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
