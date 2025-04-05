<?php
// Enable error reporting for debugging
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

// Function to get user's bookings
function getUserBookings($conn, $email) {
    $sql = "SELECT * FROM medicine_bookings WHERE email = ? ORDER BY booking_date DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

$message = '';
$userBookings = [];

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['submit_booking'])) {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $medicine_name = $_POST['medicine_name'];
        $quantity = $_POST['quantity'];

        $sql = "INSERT INTO medicine_bookings (name, email, phone, medicine_name, quantity) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $name, $email, $phone, $medicine_name, $quantity);

        if ($stmt->execute()) {
            $message = "Booking submitted successfully!";
        } else {
            $message = "Error: " . $stmt->error;
        }
        $stmt->close();
    } elseif (isset($_POST['show_bookings'])) {
        $email = $_POST['email'];
        $userBookings = getUserBookings($conn, $email);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medicine Booking - Health & Hygiene Hub</title>
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
        .booking-form, .bookings-table {
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
        input[type="text"], input[type="email"], input[type="tel"], input[type="number"] {
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
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
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
        <h1>Medicine Booking</h1>
        
        <?php
        if (!empty($message)) {
            echo "<p style='color: " . ($message == "Booking submitted successfully!" ? "green" : "red") . ";'>$message</p>";
        }
        ?>
        
        <div class="booking-form">
            <h2>Book Your Medicine</h2>
            <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
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
                    <label for="medicine_name">Medicine Name:</label>
                    <input type="text" id="medicine_name" name="medicine_name" required>
                </div>
                <div class="form-group">
                    <label for="quantity">Quantity:</label>
                    <input type="number" id="quantity" name="quantity" min="1" required>
                </div>
                <button type="submit" name="submit_booking">Book Medicine</button>
            </form>
        </div>

        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <div class="form-group">
                <label for="booking_email">Enter your email to see your bookings:</label>
                <input type="email" id="booking_email" name="email" required>
            </div>
            <button type="submit" name="show_bookings">My Bookings</button>
        </form>

        <?php if (!empty($userBookings)): ?>
        <div class="bookings-table">
            <h2>Your Bookings</h2>
            <table>
                <thead>
                    <tr>
                        <th>Medicine Name</th>
                        <th>Quantity</th>
                        <th>Booking Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($userBookings as $booking): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($booking['medicine_name']); ?></td>
                        <td><?php echo htmlspecialchars($booking['quantity']); ?></td>
                        <td><?php echo htmlspecialchars($booking['booking_date']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>

<?php
$conn->close();
?>