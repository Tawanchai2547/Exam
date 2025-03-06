<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect to login if not logged in
    exit();
}

// Get user_id from the session
$user_id = $_SESSION['user_id'];

// Database credentials
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

// Fetch subjects from the subject table
$sql = "SELECT sub_id, sub_name FROM subject";
$sub_result = $conn->query($sql);

// Check if the query was executed successfully
if ($sub_result === false) {
    die("Error fetching subjects: " . $conn->error);
}

// Handle file upload after form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['fileUpload'])) {
    // ดึงข้อมูลจากฟอร์ม
    $sub_id = $_POST['sub_id']; // Make sure sub_id is retrieved from the form
    $fileName = $_FILES['fileUpload']['name'];
    $description = $_POST['description'];
    $courseCode = $_POST['courseCode'];
    $courseDescription = $_POST['courseDescription'];
    $studentCount = $_POST['studentCount'];
    $classSection = $_POST['classSection'];

    // ตรวจสอบการอัปโหลดไฟล์
    if ($_FILES['fileUpload']['error'] !== UPLOAD_ERR_OK) {
        die("เกิดข้อผิดพลาดในการอัปโหลดไฟล์: " . $_FILES['fileUpload']['error']);
    }

    // ตรวจสอบขนาดของไฟล์
    if ($_FILES['fileUpload']['size'] == 0) {
        die("ไฟล์ว่าง ไม่สามารถอัปโหลดได้");
    }

    // อ่านข้อมูลไฟล์
    $file = file_get_contents($_FILES['fileUpload']['tmp_name']);
    if ($file === false) {
        die("ไม่สามารถอ่านข้อมูลไฟล์ได้");
    }

    // เตรียมคำสั่ง SQL
    $sql = "INSERT INTO exam (fileName, description, courseCode, courseDescription, studentCount, classSection, file, examInstructions, st_id , user_id, sub_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    // เตรียมและผูกข้อมูล
    $stmt = $conn->prepare($sql);

    // ตรวจสอบการเตรียมคำสั่ง SQL
    if ($stmt === false) {
        die("เกิดข้อผิดพลาดในการเตรียมคำสั่ง SQL: " . $conn->error);
    }

    // กำหนดค่า st_id
    $st_id = 1; // ตัวอย่าง student ID

    // ดึงคำแนะนำผู้คุมสอบที่เลือก
    $examInstructions = isset($_POST['examInstructions']) ? implode(', ', $_POST['examInstructions']) : '';
    $otherInstruction = isset($_POST['otherInstruction']) ? $_POST['otherInstruction'] : '';
    if ($otherInstruction) {
        $examInstructions .= ', ' . $otherInstruction;
    }

    // ผูกพารามิเตอร์
    $stmt->bind_param("ssssisssiis", $fileName, $description, $courseCode, $courseDescription, $studentCount, $classSection, $file, $examInstructions, $st_id, $user_id, $sub_id);

    // ทำการสั่งงาน
    if ($stmt->execute()) {
        echo "อัปโหลดไฟล์เรียบร้อยแล้ว.";
    } else {
        echo "เกิดข้อผิดพลาดในการอัปโหลดไฟล์: " . $stmt->error;
    }

    // ปิดคำสั่งและการเชื่อมต่อ
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <title>Upload File</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            position: relative;
        }

        .container {
            width: 50%;
            margin: 50px auto;
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

        .back-button {
            position: absolute;
            top: 10px;
            /* Adjust top position */
            left: 10px;
            /* Adjust left position */
            z-index: 1000;
            /* Ensure it's on top */
        }

        .dropdown {
            position: absolute;
            top: 20px;
            right: 20px;
        }

        .dropbtn {
            background-color: transparent;
            /* Set to transparent */
            color: #333;
            /* Change text color for visibility */
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            /* Dropdown item background */
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
            width: 50px;
            /* Adjust width as needed */
            height: auto;
            /* Maintain aspect ratio */
            background-color: transparent;
            /* Ensure background is transparent */
        }
    </style>
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

    <button class="btn btn-secondary back-button" onclick="window.location.href='teachermain.php'">Back to Main</button>

    <div class="container mt-5">

        <form action="upload.php" method="post" enctype="multipart/form-data">
            <h2>Upload</h2>

            <!-- ส่ง user_id ผ่านฟอร์ม -->
            <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">

            <label for="sub_id">วิชา</label>
            <select name="sub_id" id="sub_id" required>
                <option value="">Select Subject</option>
                <?php
                if ($sub_result->num_rows > 0) {
                    while ($row = $sub_result->fetch_assoc()) {
                        echo "<option value='" . $row['sub_id'] . "'>" . $row['sub_name'] . "</option>";
                    }
                }
                ?>
            </select>

            <label for="description">คำแนะนำ</label>
            <input type="text" name="description" id="description" required>

            <label for="courseCode">รหัสวิชา</label>
            <input type="text" name="courseCode" id="courseCode" required>

            <label for="courseDescription">คำแนะนำรายวิชา</label>
            <input type="text" name="courseDescription" id="courseDescription" required>

            <label for="studentCount">จำนวนนักศึกษา</label>
            <input type="number" name="studentCount" id="studentCount" required>

            <label for="classSection">ตอน</label>
            <input type="text" name="classSection" id="classSection" required>

            <label for="fileUpload">ไฟล์ (PDF เท่านั้น)</label>
            <input type="file" name="fileUpload" id="fileUpload" accept="application/pdf" required>

            <label for="examInstructions">อุปกรณ์ที่ใช้หรือคำแนะนำผู้คุมสอบ</label>
            <div class="checkbox-group">
                <input type="checkbox" name="examInstructions[]" value="นำตำราเข้าห้องสอบได้"> นำตำราเข้าห้องสอบได้<br>
                <input type="checkbox" name="examInstructions[]" value="นำเครื่องคิดเลขเข้าห้องสอบได้"> นำเครื่องคิดเลขเข้าห้องสอบได้<br>
                <input type="checkbox" name="examInstructions[]" value="ห้ามนำไม้บรรทัดมีสูตรคณิตศาสตร์เข้าห้องสอบ"> ห้ามนำไม้บรรทัดมีสูตรคณิตศาสตร์เข้าห้องสอบ<br>
                <input type="checkbox" id="otherOption" name="examInstructions[]" value="อื่นๆ"> อื่นๆ<br><br>
                <input type="text" id="otherText" name="otherInstruction" placeholder="กรอกรายละเอียดเพิ่มเติม"><br>
            </div>

            <input type="submit" value="Submit">
        </form>


    </div>
</body>

</html>