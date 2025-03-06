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

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_POST['user_id'];
    $name = $_POST['name'];
    $user_email = $_POST['user_email'];
    $user_tel = $_POST['user_tel'];
    $role = $_POST['role']; // This will not change
    $username = $_POST['username']; // This will not change

    // Hash the password if it's changed
    if (!empty($_POST['password'])) {
        if ($_POST['password'] === $_POST['confirm_password']) {
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        } else {
            echo "<div class='alert alert-danger'>Passwords do not match!</div>";
            exit();
        }
    } else {
        // Fetch the current password if not updated
        $sql = "SELECT password FROM user WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $password = $row['password']; // Keep the old password if not changing
        $stmt->close();
    }

    // Prepare an update statement
    $update_sql = "UPDATE user SET name = ?, user_email = ?, user_tel = ?, password = ? WHERE user_id = ?";
$stmt = $conn->prepare($update_sql);
$stmt->bind_param("ssssi", $name, $user_email, $user_tel, $password, $user_id);

if ($stmt->execute()) {
    // Redirect based on user role
    if ($role == 'อาจารย์ประจำวิชา') {
        header('Location: teachermain.php'); 
        exit();
    } elseif ($role == 'หน่วยเทคโนโลยีการศึกษา') {
        header('Location: techunit.html'); 
        exit();
    } elseif ($role == 'เจ้าหน้าที่ดำเนินการสอบ') {
        header('Location: anotherPage.html'); 
        exit();
    } else {
        header('Location: adminmain.html'); 
        exit();
    }
} else {
    echo "<div class='alert alert-danger'>Error updating user: " . $stmt->error . "</div>";
}

$stmt->close();
}

// Fetch the user data to edit
if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];

    $sql = "SELECT * FROM user WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        echo "<p>User not found.</p>";
        exit();
    }

    $stmt->close();
} else {
    echo "<p>No user selected for editing.</p>";
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h2>Edit User</h2>

    <form action="editprofile.php" method="post">
        <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($row['user_id']); ?>">
        <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($row['name']); ?>" required>
        </div>
        <div class="form-group">
            <label for="user_email">Email:</label>
            <input type="email" class="form-control" name="user_email" value="<?php echo htmlspecialchars($row['user_email']); ?>" required>
        </div>
        <div class="form-group">
            <label for="user_tel">Phone Number:</label>
            <input type="text" class="form-control" name="user_tel" value="<?php echo htmlspecialchars($row['user_tel']); ?>" required>
        </div>
        <div class="form-group">
            <label for="role">Role:</label>
            <input type="text" class="form-control" value="<?php echo htmlspecialchars($row['role']); ?>" disabled>
            <input type="hidden" name="role" value="<?php echo htmlspecialchars($row['role']); ?>">
        </div>
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" class="form-control" value="<?php echo htmlspecialchars($row['username']); ?>" disabled>
            <input type="hidden" name="username" value="<?php echo htmlspecialchars($row['username']); ?>">
        </div>
        <div class="form-group">
            <label for="password">New Password:</label>
            <input type="password" class="form-control" name="password" placeholder="Leave blank to keep the current password">
        </div>
        <div class="form-group">
            <label for="confirm_password">Confirm Password:</label>
            <input type="password" class="form-control" name="confirm_password" placeholder="Leave blank to keep the current password">
        </div>
        <button type="submit" class="btn btn-primary">Update User</button>
    </form>
</div>

<!-- Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
