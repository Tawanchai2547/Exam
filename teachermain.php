<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: loginpage.php'); // Redirect to login if not logged in
    exit();
}

// Get user_id from the session
$user_id = $_SESSION['user_id'];

$host = "localhost";
$username = "root"; // default username for XAMPP
$password = ""; // default password for XAMPP (usually empty)
$dbname = "softwareengineer";

// สร้างการเชื่อมต่อ
$conn = new mysqli($host, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// Query to get name and role based on user_id
$sql = "SELECT name, role FROM user WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($name, $role);
$stmt->fetch();
$stmt->close();
$conn->close();

?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Main</title>

    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .custom-btn {
            background-color: #007bff;
            color: rgb(0, 0, 0);
            font-size: 2rem;
            padding: 50px 70px;
            border-radius: 2rem;
            transition: all 0.3s ease;
        }

        .custom-btn:hover {
            background-color: #0056b3;
            color: #000000;
            text-decoration: none;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            transform: scale(1.05);
        }

        .full-height {
            height: 80vh;
        }

        .dropdown {
            position: absolute;
            top: 20px;
            right: 50px;
        }

        .dropbtn img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            /* Circular image */
            cursor: pointer;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            z-index: 999;
            /* Add a higher z-index */
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

        .dropdown:hover .dropbtn img {
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
            /* Add shadow effect to image on hover */
        }
    </style>
</head>

<body>

    <div class="user-info">
        <p>Welcome to your mainpage Khun <?php echo htmlspecialchars($name); ?> (Role: <?php echo htmlspecialchars($role); ?>)</p>
    </div>
    <div class="dropdown">
        <div class="dropbtn">
            <img src="profile.png" alt="Profile Image"> <!-- Image for dropdown -->
        </div>
        <div class="dropdown-content">
            <a href="editprofile.php?user_id=<?php echo $user_id; ?>">Profile</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <div class="container full-height d-flex align-items-center justify-content-center">
        <div class="text-center mt-4">
            <a href="upload.php" class="custom-btn mx-5">อัปโหลด</a>
            <a href="viewmyfile.php" class="custom-btn mx-5">ไฟล์ของฉัน</a>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>

</html>