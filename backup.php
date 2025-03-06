<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: loginpage.php'); // Redirect to login if not logged in
    exit();
}

// Get user_id from the session
$user_id = $_SESSION['user_id'];
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File List</title>

    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Custom styles for the table */
        .table-blue thead {
            background-color: #007bff; /* Bootstrap primary color */
            color: white; /* Text color */
        }
        .table-blue tbody tr:hover {
            background-color: #e7f1ff; /* Light blue on row hover */
        }
        .back-button {
            position: absolute;
            top: 10px; /* Adjust top position */
            left: 10px; /* Adjust left position */
            z-index: 1000; /* Ensure it's on top */
        }

        .dropdown {
            position: absolute;
            top: 20px;
            right: 20px;
        }

        .dropbtn {
            background-color: transparent; /* Set to transparent */
            color: #333; /* Change text color for visibility */
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #f9f9f9; /* Dropdown item background */
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
            background-color: #f1f1f1;
        }

        .dropdown:hover .dropdown-content {
            display: block;
        }

        .dropdown .dropbtn img {
            width: 50px; /* Adjust width as needed */
            height: auto; /* Maintain aspect ratio */
            background-color: transparent; /* Ensure background is transparent */
        }
    </style>
    <script>
        function confirmDelete(name) {
            return confirm("คุณต้องการที่จะลบ " + name + " ใช่หรือไม่?");
        }
    </script>
</head>
<body>
<div class="dropdown">
        <div class="dropbtn">
            <img src="profile.png" alt="Profile Image"> <!-- Image for dropdown -->
        </div>
        <div class="dropdown-content">
            <a href="editprofile.php?user_id=<?php echo $user_id; ?>">Profile</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <button class="btn btn-secondary back-button" onclick="window.location.href='techunit.php'">Back to Main</button>

<div class="container mt-5">
    <?php
    
    // เปิดแสดงข้อผิดพลาดเพื่อช่วยในการดีบัก
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

    // Check if a delete request was made
    if (isset($_POST['delete'])) {
        $id = $_POST['id'];
        
        // Debugging: แสดงค่า f_id
        // echo "<pre>";
        // var_dump($f_id);
        // echo "</pre>";
        
        // Prepare delete statement
        $delete_sql = "DELETE FROM backup  WHERE id = ?";
        $stmt = $conn->prepare($delete_sql);
        
        if ($stmt === false) {
            die("Failed to prepare statement: " . $conn->error);
        }
        
        $stmt->bind_param("i", $id);
        
        // Debugging: แสดงคำสั่ง SQL
        // echo "<pre>";
        // echo "Executing SQL: DELETE FROM exam WHERE f_id = " . $f_id;
        // echo "</pre>";
        
        if ($stmt->execute()) {
            echo "<div class='alert alert-success'>exam deleted successfully!</div>";
        } else {
            echo "<div class='alert alert-danger'>Error deleting exam: " . $stmt->error . "</div>";
        }
        
        $stmt->close();
    }

    // Fetch data from the user table
    $sql = "SELECT backup.*, status.name
            FROM backup
            JOIN status ON backup.st_id = status.id
            WHERE st_id = 4;";
    $stmt = $conn->prepare($sql);
    //$stmt->bind_param("i", $user_id); // Bind the user_id to the statement
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if there are any rows returned
    if ($result->num_rows > 0) {
        // Display the table with Bootstrap styling
        echo '<table class="table table-striped table-blue">'; // Added custom class here
        echo '<thead><tr>';
        echo '<th>file ID</th>';
        echo '<th>ชื่อไฟล์</th>';
        echo '<th>คำอธิบาย</th>';
        echo '<th>รหัสวิชา</th>';
        echo '<th>คำอธิบายวิชา</th>';
        echo '<th>จำนวนนักศึกษา</th>';
        echo '<th>ตอน</th>';
        echo '<th>คำแนะนำ</th>';
        echo '<th>สถานะ</th>';
        echo '<th>Actions</th>'; // New column for action
        echo '</tr></thead>';
        echo '<tbody>';

        // Loop through the results and output each row
        while($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['fileName']) . "</td>";
            echo "<td>" . htmlspecialchars($row['description']) . "</td>";
            echo "<td>" . htmlspecialchars($row['courseCode']) . "</td>";
            echo "<td>" . htmlspecialchars($row['courseDescription']) . "</td>";
            echo "<td>" . htmlspecialchars($row['studentCount']) . "</td>";
            echo "<td>" . htmlspecialchars($row['classSection']) . "</td>";
            echo "<td>" . htmlspecialchars($row['examInstructions']) . "</td>";
            echo "<td>" . htmlspecialchars($row['name']) . "</td>";
            echo "<td>";
            // Form to delete user
            echo '<form action="" method="post" onsubmit="return confirmDelete(\'' . addslashes($row['fileName']) . '\');" style="display:inline-block;">';
            echo '<input type="hidden" name="id" value="' . htmlspecialchars($row['id']) . '">';
            echo '<input type="submit" name="delete" value="Delete" class="btn btn-danger btn-sm">';
            echo '</form> ';
            // Edit button
            echo '<a href="retrieve.php?id=' . htmlspecialchars($row['id']) . '" class="btn btn-warning btn-sm ml-1">View</a>';
            echo "</td>";
            echo "</tr>";
        }
        
        echo '</tbody></table>';
    } else {
        // If no users are found
        echo "<p>No users found in the database.</p>";
    }

    // Close the connection
    $conn->close();
    ?>

</div>

<!-- Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
