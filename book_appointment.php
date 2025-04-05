<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "health_hub";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $doctor = $_POST['doctor'];
    $appointment_date = $_POST['date'];
    $reason = $_POST['reason'];

    // Debug: Print received data
    echo "Received data:<br>";
    echo "Name: $name<br>";
    echo "Email: $email<br>";
    echo "Phone: $phone<br>";
    echo "Doctor: $doctor<br>";
    echo "Date: $appointment_date<br>";
    echo "Reason: $reason<br>";

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO appointments (name, email, phone, doctor, appointment_date, reason) VALUES (?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("ssssss", $name, $email, $phone, $doctor, $appointment_date, $reason);

    // Execute the statement
    if ($stmt->execute()) {
        echo "Appointment booked successfully!";
        // Redirect back to the form page with a success message
        header("Location: doctor_app.php?success=1");
        exit();
    } else {
        echo "Error: " . $stmt->error;
        // Redirect back to the form page with an error message
        header("Location: doctor_app.php?error=1");
        exit();
    }

    // Close statement
    $stmt->close();
}

// Close connection
$conn->close();
?>