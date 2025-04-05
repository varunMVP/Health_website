<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In / Sign Up - Health & Hygiene Hub</title>
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
            max-width: 400px;
            margin: 0 auto;
        }
        h1 {
            color: #4CAF50;
            text-align: center;
        }
        .form-container {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            color: #333;
        }
        input[type="email"], input[type="password"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
        }
        button:hover {
            background-color: #45a049;
        }
        .switch-form {
            text-align: center;
            margin-top: 15px;
        }
        .switch-form a {
            color: #4CAF50;
            text-decoration: none;
        }
        .switch-form a:hover {
            text-decoration: underline;
        }
    </style>
    
</head>
<body>
    <div class="navbar">
        <img src="/api/placeholder/100/50" alt="Logo" class="logo">
        <div>
            <a href="index.php">Home</a>
            <a href="#about">About Us</a>
            <a href="#services">Services</a>
            <a href="#user-info">User Info</a>
            <a href="#contact">Contact Us</a>
        </div>
    </div>

    <div class="main-content">
        <h1>Welcome to Health & Hygiene Hub</h1>
        
        <?php if(isset($_SESSION['user_id'])): ?>
            <div class="form-container">
                <h2>Welcome!</h2>
                <p>You are logged in.</p>
                <form action="logout.php" method="post">
                    <button type="submit">Log Out</button>
                </form>
            </div>
        <?php else: ?>
            <div class="form-container" id="signin-form">
                <h2>Sign In</h2>
                <form action="login.php" method="post">
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <button type="submit">Sign In</button>
                </form>
                <div class="switch-form">
                    <p>Don't have an account? <a href="#" onclick="showSignUp()">Create one</a></p>
                </div>
            </div>

            <div class="form-container" id="signup-form" style="display: none;">
                <h2>Create Account</h2>
                <form action="register.php" method="post">
                    <div class="form-group">
                        <label for="new-email">Email:</label>
                        <input type="email" id="new-email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="username">Username:</label>
                        <input type="text" id="username" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="new-password">Password:</label>
                        <input type="password" id="new-password" name="password" required>
                    </div>
                    <div class="form-group">
                        <label for="confirm-password">Confirm Password:</label>
                        <input type="password" id="confirm-password" name="confirm_password" required>
                    </div>
                    <button type="submit">Create Account</button>
                </form>
                <div class="switch-form">
                    <p>Already have an account? <a href="#" onclick="showSignIn()">Sign in</a></p>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script>
        function showSignUp() {
            document.getElementById('signin-form').style.display = 'none';
            document.getElementById('signup-form').style.display = 'block';
        }

        function showSignIn() {
            document.getElementById('signup-form').style.display = 'none';
            document.getElementById('signin-form').style.display = 'block';
        }
    </script>
</body>
</html>