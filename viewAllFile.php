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
    </style>
    <script>
        function confirmDelete(name) {
            return confirm("คุณต้องการที่จะลบ " + name + " ใช่หรือไม่?");
        }

        function openStatusModal(id) {
            // Set the ID of the exam
            document.getElementById("exam_id").value = id;
            // Show the modal
            $('#statusModal').modal('show');
        }
    </script>
</head>
<body>
<div class="container mt-5">
    <button class="btn btn-secondary back-button" onclick="window.location.href='examagent.php'">Back to Main</button>

    <?php

    // เปิดแสดงข้อผิดพลาดเพื่อช่วยในการดีบัก
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    // Database credentials
    $host = "localhost";
    $username_db = "root"; // changed variable name to avoid conflict with PHP variable
    $password_db = ""; // default password for XAMPP (usually empty)
    $dbname = "softwareengineer";

    // Create connection
    $conn = new mysqli($host, $username_db, $password_db, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Handle status update
    if (isset($_POST['update_status'])) {
        $exam_id = $_POST['exam_id'] ?? null;
        $st_id = $_POST['st_id'] ?? null;

        if ($exam_id && $st_id) {
            // Check if status is "พิมพ์แล้ว" (id=4)
            if ($st_id == 4) {
                // Insert into backup table
                $backup_sql = "INSERT INTO backup (fileName, description, courseCode, courseDescription, studentCount, classSection, file, examInstructions, st_id, user_id, sub_id) SELECT fileName, description, courseCode, courseDescription, studentCount, classSection, file, examInstructions, 4, user_id, sub_id FROM exam WHERE id = ?";
                $stmt_backup = $conn->prepare($backup_sql);
                if ($stmt_backup === false) {
                    die("Failed to prepare backup statement: " . $conn->error);
                }
                $stmt_backup->bind_param("i", $exam_id);
                if (!$stmt_backup->execute()) {
                    echo "<div class='alert alert-danger'>Error copying data to backup: " . $stmt_backup->error . "</div>";
                }
                $stmt_backup->close();
            }

            // Update exam status
            $update_sql = "UPDATE exam SET st_id = ? WHERE id = ?";
            $stmt_update = $conn->prepare($update_sql);
            if ($stmt_update === false) {
                die("Failed to prepare update statement: " . $conn->error);
            }
            $stmt_update->bind_param("ii", $st_id, $exam_id);
            if ($stmt_update->execute()) {
                if ($stmt_update->affected_rows > 0) {
                    echo "<div class='alert alert-success'>Status updated successfully!</div>";
                } else {
                    echo "<div class='alert alert-warning'>No exam found with the provided ID.</div>";
                }
            } else {
                echo "<div class='alert alert-danger'>Error updating status: " . $stmt_update->error . "</div>";
            }
            $stmt_update->close();
        } else {
            echo "<div class='alert alert-danger'>Invalid status update request.</div>";
        }
    }

    // Handle delete request
    if (isset($_POST['delete'])) {
        $id = $_POST['id'] ?? null;

        if ($id) {
            // Prepare delete statement
            $delete_sql = "DELETE FROM exam WHERE id = ?";
            $stmt_delete = $conn->prepare($delete_sql);

            if ($stmt_delete === false) {
                die("Failed to prepare delete statement: " . $conn->error);
            }

            $stmt_delete->bind_param("i", $id);

            if ($stmt_delete->execute()) {
                if ($stmt_delete->affected_rows > 0) {
                    echo "<div class='alert alert-success'>Exam deleted successfully!</div>";
                } else {
                    echo "<div class='alert alert-warning'>No exam found with the provided ID.</div>";
                }
            } else {
                echo "<div class='alert alert-danger'>Error deleting exam: " . $stmt_delete->error . "</div>";
            }

            $stmt_delete->close();
        } else {
            echo "<div class='alert alert-danger'>Invalid delete request.</div>";
        }
    }

    // Fetch data from the exam table
    $sql = "SELECT exam.*, status.name FROM exam JOIN status ON exam.st_id = status.id";
    $result = $conn->query($sql);

    // Check if there are any rows returned
    if ($result->num_rows > 0) {
        // Display the table with Bootstrap styling
        echo '<table class="table table-striped table-blue">'; 
        echo '<thead><tr>';
        echo '<th>Exam ID</th>';
        echo '<th>ชื่อไฟล์</th>';
        echo '<th>คำอธิบาย</th>';
        echo '<th>รหัสวิชา</th>';
        echo '<th>คำอธิบายวิชา</th>';
        echo '<th>จำนวนนักศึกษา</th>';
        echo '<th>ตอน</th>';
        echo '<th>คำแนะนำ</th>';
        echo '<th>สถานะ</th>';
        echo '<th>Actions</th>'; 
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
            // Update status button
            echo '<button class="btn btn-info btn-sm" onclick="openStatusModal(' . htmlspecialchars($row['id']) . ')">Update Status</button> ';
            // Form to delete exam
            echo '<form action="" method="post" onsubmit="return confirmDelete(\'' . addslashes($row['fileName']) . '\');" style="display:inline-block;">';
            echo '<input type="hidden" name="id" value="' . htmlspecialchars($row['id']) . '">';
            echo '<input type="submit" name="delete" value="Delete" class="btn btn-danger btn-sm">';
            echo '</form> ';
            echo '<a href="retrieve.php?id=' . htmlspecialchars($row['id']) . '" class="btn btn-warning btn-sm ml-1">View</a>';
            echo "</td>";
            echo "</tr>";
        }
        
        echo '</tbody></table>';
    } else {
        echo "<p>No exams found in the database.</p>";
    }

    // Close the connection
    $conn->close();
    ?>
</div>

<!-- Modal for updating status -->
<div class="modal fade" id="statusModal" tabindex="-1" role="dialog" aria-labelledby="statusModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="statusForm" method="POST" action="">
                <div class="modal-header">
                    <h5 class="modal-title" id="statusModalLabel">เลือกสถานะ</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Hidden input สำหรับ exam_id -->
                    <input type="hidden" id="exam_id" name="exam_id" value="">
                    <div class="form-group">
                        <label for="st_id">สถานะ:</label>
                        <select class="form-control" id="st_id" name="st_id" required>
                            <option value="">-- เลือกสถานะ --</option>
                            <option value="1">รอตรวจสอบ</option>
                            <option value="2">รอแก้ไข</option>
                            <option value="3">สมบูรณ์</option>
                            <option value="4">พิมพ์แล้ว</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">ยกเลิก</button>
                    <button type="submit" name="update_status" class="btn btn-primary">ยืนยัน</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
