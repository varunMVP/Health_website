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

// Function to get diet recommendations
function getDietRecommendations($conn, $sickness) {
    $sql = "SELECT * FROM sick_diets WHERE sickness = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $sickness);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// Function to log sickness occurrence
function logSicknessOccurrence($conn, $sickness) {
    $sql = "INSERT INTO sickness_log (sickness, occurrence_date) VALUES (?, CURDATE())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $sickness);
    $stmt->execute();
}

// Function to get sickness history
function getSicknessHistory($conn) {
    $sql = "SELECT sickness, occurrence_date FROM sickness_log ORDER BY occurrence_date DESC";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Function to get all sicknesses for dropdown
function getAllSicknesses($conn) {
    $sql = "SELECT DISTINCT sickness FROM sick_diets";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

$dietRecommendation = null;
$showHistory = false;
$allSicknesses = getAllSicknesses($conn);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['get_recommendations'])) {
        $selectedSickness = $_POST['sickness'];
        $dietRecommendation = getDietRecommendations($conn, $selectedSickness);
        if ($dietRecommendation) {
            logSicknessOccurrence($conn, $selectedSickness);
        }
    } elseif (isset($_POST['show_history'])) {
        $showHistory = true;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sick Diet Recommendations - Health & Hygiene Hub</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333;
        }
        .navbar {
            background-color: #4CAF50;
            color: white;
            padding: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .navbar a {
            color: white;
            text-decoration: none;
            padding: 0.5rem 1rem;
        }
        .navbar a:hover {
            background-color: #45a049;
        }
        .logo {
            height: 50px;
        }
        .main-content {
            padding: 2rem;
            max-width: 800px;
            margin: 0 auto;
        }
        h1, h2, h3 {
            color: #4CAF50;
        }
        .card {
            background-color: white;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        select, button {
            padding: 0.5rem;
            margin: 0.5rem 0;
            width: 100%;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #45a049;
        }
        ul {
            padding-left: 1.5rem;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 0.5rem;
            text-align: left;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        .food-list {
            display: flex;
            justify-content: space-between;
        }
        .food-list > div {
            width: 48%;
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
        <h1>Sick Diet Recommendations</h1>
        
        <div class="card">
            <h2>Select Your Sickness</h2>
            <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <select name="sickness" required>
                    <option value="">Choose a sickness</option>
                    <?php foreach ($allSicknesses as $sickness): ?>
                        <option value="<?php echo htmlspecialchars($sickness['sickness']); ?>">
                            <?php echo htmlspecialchars($sickness['sickness']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" name="get_recommendations">Get Recommendations</button>
            </form>
        </div>

        <?php if ($dietRecommendation): ?>
        <div class="card">
            <h2>Diet Recommendations for <?php echo htmlspecialchars($dietRecommendation['sickness']); ?></h2>
            <p>This sickness has been logged for today's date.</p>
            <div class="food-list">
                <div>
                    <h3>Foods to Eat:</h3>
                    <ul>
                        <?php foreach (explode(',', $dietRecommendation['foods_to_eat']) as $food): ?>
                            <li><?php echo htmlspecialchars(trim($food)); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div>
                    <h3>Foods to Avoid:</h3>
                    <ul>
                        <?php foreach (explode(',', $dietRecommendation['foods_to_avoid']) as $food): ?>
                            <li><?php echo htmlspecialchars(trim($food)); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <div class="card">
            <h2>View Sickness History</h2>
            <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <button type="submit" name="show_history">Show Sickness History</button>
            </form>
        </div>

        <?php if ($showHistory): ?>
        <div class="card">
            <h2>Sickness History</h2>
            <table>
                <thead>
                    <tr>
                        <th>Sickness</th>
                        <th>Occurrence Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $sicknessHistory = getSicknessHistory($conn);
                    foreach ($sicknessHistory as $entry): 
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($entry['sickness']); ?></td>
                        <td><?php echo htmlspecialchars($entry['occurrence_date']); ?></td>
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