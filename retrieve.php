<?php

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
if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Ensure it's an integer

    // Prepare and execute the statement
    $stmt = $conn->prepare("SELECT fileName, file FROM exam WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($fileName, $file);

    if ($stmt->fetch()) {
        // Debugging output
        if (empty($file)) {
            die("No data found for this file."); // Check if data is empty
        }

        // Set headers to download the file
        header("Content-Type: application/pdf");
        header("Content-Disposition: inline; filename=\"$fileName\""); // Use inline to display in browser
        header("Content-Length: " . strlen($file)); // Add content length header
        echo $file;
    } else {
        echo "File not found."; // No matching file
    }

    // Close the statement
    $stmt->close();
} else {
    echo "No ID specified."; // No ID provided
}

// Close the connection
$conn->close();
