<?php
session_start();

ini_set('display_errors', 1);
error_reporting(E_ALL);

// ข้อมูลการเชื่อมต่อฐานข้อมูล
$host = "localhost"; 
$username = "root";  
$password = "";      
$dbname = "softwareengineer"; 

// สร้างการเชื่อมต่อ
$conn = new mysqli($host, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = $_POST['username'];
    $password = $_POST['password'];

    // เตรียมคำสั่งเพื่อป้องกัน SQL injection
    $stmt = $conn->prepare("SELECT user_id, password, role FROM user WHERE username = ?");
    $stmt->bind_param("s", $user); 
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($userId, $hashedPassword, $role);
        $stmt->fetch();

        // ตรวจสอบรหัสผ่าน
        if (password_verify($password, $hashedPassword)) {
            $_SESSION['username'] = $user;
            $_SESSION['role'] = $role;
            $_SESSION['user_id'] = $userId; // เก็บ user_id ในเซสชัน

            // ตรวจสอบบทบาทและทำการเปลี่ยนเส้นทางตามที่เหมาะสม
            if ($role == 'อาจารย์ประจำวิชา') {
                header('Location: teachermain.php'); 
                exit();
            } elseif ($role == 'หน่วยเทคโนโลยีการศึกษา') {
                header('Location: techunit.php'); 
                exit();
            } elseif ($role == 'เจ้าหน้าที่ดำเนินการสอบ') {
                header('Location: examagent.php'); 
                exit();
            } else {
                header('Location: adminmain.php'); 
                exit();
            }
        } else {
            $error = 'รหัสประจำตัวหรือรหัสผ่านไม่ถูกต้อง';
        }
    } else {
        $error = 'รหัสประจำตัวหรือรหัสผ่านไม่ถูกต้อง';
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .container {
            width: 50%;
            margin: 50px auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <h2 class="text-center">Login</h2>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>
    <form method="post">
        <div class="form-group">
            <label for="username">ชื่อผู้ใช้</label>
            <input type="text" class="form-control" id="username" name="username" required>
        </div>
        <div class="form-group">
            <label for="password">รหัสผ่าน</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary">เข้าสู่ระบบ</button>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
