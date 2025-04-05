<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "health_hub";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to get previous appointments
function getPreviousAppointments($conn, $limit = 5) {
    $sql = "SELECT * FROM appointments ORDER BY appointment_date DESC LIMIT ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

$showPreviousAppointments = false;
if (isset($_POST['show_previous'])) {
    $showPreviousAppointments = true;
    $previousAppointments = getPreviousAppointments($conn);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Appointments - Health & Hygiene Hub</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f8ff;
        }
        .navbar {
            background-color: #4CAF50;
            overflow: hidden;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
        }
        .navbar a {
            color: white;
            text-decoration: none;
            padding: 14px 16px;
        }
        .logo {
            height: 50px;
        }
        .main-content {
            padding: 20px;
            max-width: 800px;
            margin: 0 auto;
        }
        h1 {
            color: #4CAF50;
        }
        .appointment-form {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input[type="text"], input[type="email"], input[type="tel"], select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        .previous-appointments {
            margin-top: 20px;
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <img src="/api/placeholder/100/50" alt="Logo" class="logo">
        <div>
            <a href="home.html">Home</a>
            <a href="about.html">About Us</a>
            <a href="services.html">Services</a>
            <a href="contact.html">Contact Us</a>
        </div>
    </div>

    <div class="main-content">
        <h1>Doctor Appointments</h1>
        
        <?php
        if (isset($_GET['success'])) {
            echo "<div style='background-color: #dff0d8; color: #3c763d; padding: 10px; margin-bottom: 20px; border-radius: 4px;'>Appointment booked successfully!</div>";
        }
        if (isset($_GET['error'])) {
            echo "<div style='background-color: #f2dede; color: #a94442; padding: 10px; margin-bottom: 20px; border-radius: 4px;'>Error booking appointment. Please try again.</div>";
        }
        ?>
        
        <div class="appointment-form">
            <h2>Book Your Appointment</h2>
            <form action="book_appointment.php" method="post">
                <div class="form-group">
                    <label for="name">Full Name:</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="phone">Phone Number:</label>
                    <input type="tel" id="phone" name="phone" required>
                </div>
                <div class="form-group">
                    <label for="doctor">Select Doctor:</label>
                    <select id="doctor" name="doctor" required>
                        <option value="">Choose a doctor</option>
                        <option value="dr-smith">Dr. Smith - General Practitioner</option>
                        <option value="dr-jones">Dr. Jones - Pediatrician</option>
                        <option value="dr-wilson">Dr. Wilson - Cardiologist</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="date">Preferred Date:</label>
                    <input type="date" id="date" name="date" required>
                </div>
                <div class="form-group">
                    <label for="reason">Reason for Visit:</label>
                    <textarea id="reason" name="reason" rows="4" required></textarea>
                </div>
                <button type="submit">Request Appointment</button>
            </form>
        </div>

        <form method="post" style="margin-top: 20px;">
            <button type="submit" name="show_previous">Show Previous Appointments</button>
        </form>

        <?php if ($showPreviousAppointments): ?>
        <div class="previous-appointments">
            <h2>Previous Appointments</h2>
            <?php if (empty($previousAppointments)): ?>
                <p>No previous appointments found.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Doctor</th>
                            <th>Date</th>
                            <th>Reason</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($previousAppointments as $appointment): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($appointment['name']); ?></td>
                            <td><?php echo htmlspecialchars($appointment['doctor']); ?></td>
                            <td><?php echo htmlspecialchars($appointment['appointment_date']); ?></td>
                            <td><?php echo htmlspecialchars($appointment['reason']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>
<?php
$conn->close();
?>