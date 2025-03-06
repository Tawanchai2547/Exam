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
            font-size: 1.5rem;
            /* ลดขนาดฟอนต์สำหรับหน้าจอเล็ก */
            padding: 20px 30px;
            /* ลดขนาด padding สำหรับหน้าจอเล็ก */
            border-radius: 1rem;
            /* ลดขนาด border-radius สำหรับหน้าจอเล็ก */
            transition: all 0.3s ease;
            width: 100%;
            /* ให้ปุ่มเต็มความกว้างในคอลัมน์ */
            margin-bottom: 20px;
            /* เพิ่มระยะห่างด้านล่างสำหรับหน้าจอเล็ก */
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
            /* รูปภาพเป็นวงกลม */
            cursor: pointer;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            z-index: 999;
            /* เพิ่ม z-index ให้สูงกว่า */
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
            /* เพิ่มเงาให้รูปภาพเมื่อ hover */
        }

        @media (min-width: 768px) {
            .custom-btn {
                font-size: 2rem;
                /* เพิ่มขนาดฟอนต์สำหรับหน้าจอใหญ่ */
                padding: 50px 70px;
                /* เพิ่มขนาด padding สำหรับหน้าจอใหญ่ */
                border-radius: 2rem;
                /* เพิ่มขนาด border-radius สำหรับหน้าจอใหญ่ */
                margin-bottom: 0;
                /* ลบระยะห่างด้านล่างสำหรับหน้าจอใหญ่ */
            }
        }
    </style>
</head>

<body>

    <div class="user-info">
        <p>Welcome to your mainpage Khun <?php echo htmlspecialchars($name); ?> (Role: <?php echo htmlspecialchars($role); ?>)</p>
    </div>
    <div class="dropdown">
        <div class="dropbtn">
            <img src="profile.png" alt="Profile Image"> <!-- รูปภาพสำหรับ dropdown -->
        </div>
        <div class="dropdown-content">
            <a href="editprofile.php?user_id=<?php echo $user_id; ?>">Profile</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <div class="container full-height d-flex align-items-center justify-content-center">
        <div class="row w-100">
            <div class="col-md-4 mb-3">
                <a href="exammanage.php" class="btn custom-btn">จัดการการสอบ</a>
            </div>
            <div class="col-md-4 mb-3">
                <a href="viewallfile.php" class="btn custom-btn">ดูไฟล์ทั้งหมด</a>
            </div>
            <div class="col-md-4 mb-3">
                <a href="viewsubject.php" class="btn custom-btn">ดูวิชาทั้งหมด</a>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS และ dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>

</html>