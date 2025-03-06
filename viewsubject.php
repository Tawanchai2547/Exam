<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subject List</title>

    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Custom styles for the table */
        .table-blue thead {
            background-color: #007bff;
            /* Bootstrap primary color */
            color: white;
            /* Text color */
        }

        .table-blue tbody tr:hover {
            background-color: #e7f1ff;
            /* Light blue on row hover */
        }
    </style>
    <script>
        function confirmDelete(name) {
            return confirm("คุณต้องการที่จะลบ " + name + " ใช่หรือไม่?");
        }
    </script>
</head>

<body>

    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-center">Subject List</h2>
            <a href="examagent.php" class="btn btn-secondary">กลับสู่หน้าหลัก</a>
        </div>

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
            $sub_id = $_POST['sub_id'];

            // Debugging: แสดงค่า sub_id
            // echo "<pre>";
            // echo "Received sub_id: ";
            // var_dump($sub_id);
            // echo "</pre>";

            // ตรวจสอบว่า sub_id มีค่าและไม่ว่างเปล่า
            if (empty($sub_id)) {
                echo "<div class='alert alert-danger'>Invalid Subject ID.</div>";
            } else {
                // Prepare delete statement
                $delete_sql = "DELETE FROM subject WHERE sub_id = ?";
                $stmt = $conn->prepare($delete_sql);

                if ($stmt === false) {
                    die("Failed to prepare statement: " . $conn->error);
                }

                // เนื่องจาก sub_id เป็น VARCHAR ใช้ "s" ใน bind_param
                $stmt->bind_param("s", $sub_id);

                // Debugging: แสดงคำสั่ง SQL ที่จะดำเนินการ
                // echo "<pre>";
                // echo "Executing SQL: DELETE FROM subject WHERE sub_id = '" . addslashes($sub_id) . "'";
                // echo "</pre>";

                if ($stmt->execute()) {
                    if ($stmt->affected_rows > 0) {
                        echo "<div class='alert alert-success'>Subject deleted successfully!</div>";
                    } else {
                        echo "<div class='alert alert-warning'>No subject found with the provided ID.</div>";
                    }
                } else {
                    echo "<div class='alert alert-danger'>Error deleting subject: " . $stmt->error . "</div>";
                }

                $stmt->close();
            }
        }

        // Fetch data from the subject table
        $sql = "SELECT * FROM subject";
        $result = $conn->query($sql);

        // Check if there are any rows returned
        if ($result->num_rows > 0) {
            // Display the table with Bootstrap styling
            echo '<table class="table table-striped table-blue">'; // Added custom class here
            echo '<thead><tr>';
            echo '<th>Subject ID</th>';
            echo '<th>ชื่อวิชา</th>';
            echo '<th>วันสอบ</th>';
            echo '<th>เวลาเริ่มสอบ</th>';
            echo '<th>เวลาสิ้นสุดการสอบ</th>';
            echo '<th>ห้องสอบ</th>';
            echo '<th>User ID</th>';
            echo '<th>Actions</th>'; // New column for action
            echo '</tr></thead>';
            echo '<tbody>';

            // Loop through the results and output each row
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['sub_id']) . "</td>";
                echo "<td>" . htmlspecialchars($row['sub_name']) . "</td>";
                echo "<td>" . htmlspecialchars($row['date']) . "</td>";
                echo "<td>" . htmlspecialchars($row['st_time']) . "</td>";
                echo "<td>" . htmlspecialchars($row['end_time']) . "</td>";
                echo "<td>" . htmlspecialchars($row['room']) . "</td>";
                echo "<td>" . htmlspecialchars($row['user_id']) . "</td>";
                echo "<td>";
                // Form to delete subject
                echo '<form action="" method="post" onsubmit="return confirmDelete(\'' . addslashes($row['sub_name']) . '\');" style="display:inline-block;">';
                echo '<input type="hidden" name="sub_id" value="' . htmlspecialchars($row['sub_id']) . '">';
                echo '<input type="submit" name="delete" value="Delete" class="btn btn-danger btn-sm">';
                echo '</form> ';
                // Edit button
                echo '<a href="editsubject.php?sub_id=' . htmlspecialchars($row['sub_id']) . '" class="btn btn-warning btn-sm ml-1">Edit</a>';
                echo "</td>";
                echo "</tr>";
            }

            echo '</tbody></table>';
        } else {
            // If no subjects are found
            echo "<p>No subjects found in the database.</p>";
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