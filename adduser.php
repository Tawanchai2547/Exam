<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เพิ่มผู้ใช้งาน</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-center">เพิ่มผู้ใช้งาน</h2>
        <a href="adminmain.php" class="btn btn-secondary">กลับสู่หน้า Admin</a>
    </div>

    <?php
    // ตรวจสอบว่าข้อมูลฟอร์มถูกส่งมาแล้วหรือไม่
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        // ข้อมูลการเชื่อมต่อฐานข้อมูล
        $host = "localhost";
        $username = "root"; // ชื่อผู้ใช้สำหรับ XAMPP
        $password = ""; // รหัสผ่านสำหรับ XAMPP (ปกติมักจะว่าง)
        $dbname = "softwareengineer";

        // สร้างการเชื่อมต่อ
        $conn = new mysqli($host, $username, $password, $dbname);

        // ตรวจสอบการเชื่อมต่อ
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // รับค่าจากฟอร์ม
        $name = $_POST['name'];
        $user_email = $_POST['user_email'];
        $user_tel = $_POST['user_tel'];
        $role = $_POST['role'];
        $user_username = $_POST['username'];
        $user_password = $_POST['user_password'];

        // เข้ารหัสรหัสผ่านก่อนบันทึกลงในฐานข้อมูล
        $hashedPassword = password_hash($user_password, PASSWORD_DEFAULT);

        // เตรียมคำสั่ง SQL สำหรับเพิ่มผู้ใช้ใหม่
        $sql = "INSERT INTO user (name, user_email, user_tel, role, username, password) VALUES (?, ?, ?, ?, ?, ?)";

        // เตรียมคำสั่ง
        $stmt = $conn->prepare($sql);

        // ผูกพารามิเตอร์เข้ากับคำสั่ง
        $stmt->bind_param("ssssss", $name, $user_email, $user_tel, $role, $user_username, $hashedPassword);

        // ดำเนินการคำสั่ง
        if ($stmt->execute()) {
            echo "<div class='alert alert-success'>เพิ่มผู้ใช้ใหม่เรียบร้อยแล้ว!</div>";
        } else {
            echo "<div class='alert alert-danger'>เกิดข้อผิดพลาด: " . $stmt->error . "</div>";
        }

        // ปิดคำสั่งและการเชื่อมต่อ
        $stmt->close();
        $conn->close();
    }
    ?>

    <!-- HTML Form -->
    <form action="" method="post" class="mt-4">
        <div class="form-group row">
            <label for="username" class="col-sm-2 col-form-label">Username:</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
        </div>

        <div class="form-group row">
            <label for="password" class="col-sm-2 col-form-label">Password:</label>
            <div class="col-sm-10">
                <input type="password" class="form-control" id="password" name="user_password" required>
            </div>
        </div>

        <div class="form-group row">
            <label for="name" class="col-sm-2 col-form-label">ชื่อ-นามสกุล:</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
        </div>
        
        <div class="form-group row">
            <label for="email" class="col-sm-2 col-form-label">Email:</label>
            <div class="col-sm-10">
                <input type="email" class="form-control" id="email" name="user_email" required>
            </div>
        </div>

        <div class="form-group row">
            <label for="tel" class="col-sm-2 col-form-label">Telephone:</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="tel" name="user_tel" required>
            </div>
        </div>
        
        <div class="form-group row">
            <label for="role" class="col-sm-2 col-form-label">Role:</label>
            <div class="col-sm-10">
                <select class="form-control" id="role" name="role" required>
                    <option value="" disabled selected>Select Role</option>
                    <option value="อาจารย์ประจำวิชา">อาจารย์ประจำวิชา</option>
                    <option value="หน่วยเทคโนโลยีการศึกษา">หน่วยเทคโนโลยีการศึกษา</option>
                    <option value="เจ้าหน้าที่ดำเนินการสอบ">เจ้าหน้าที่ดำเนินการสอบ</option>
                    <option value="Admin">Admin</option>
                </select>
            </div>
        </div>
        
        <button type="submit" class="btn btn-primary btn-block">เพิ่มผู้ใช้งาน</button>
    </form>
</div>

<!-- Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
