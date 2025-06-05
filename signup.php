<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM admin_users WHERE username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 1) {
        $row = $res->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['admin'] = $username;
            header("Location: dashboard.php");
            exit();
        }
    }
    $error = "Invalid login credentials.";
} 

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['signup'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    
    // Check if username already exists
    $stmt = $conn->prepare("SELECT * FROM admin_users WHERE username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {
        $error = "Username already taken.";
    } else {
        $stmt = $conn->prepare("INSERT INTO admin_users (username, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $username, $password);
        if ($stmt->execute()) {
            $success = "Account created successfully. You can now log in.";
        } else {
            $error = "An error occurred. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login & Sign Up - Buklod Unlad HR</title>
    <style>
        * {
            box-sizing: border-box;
        }
        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            font-family: Arial, sans-serif;
        }

        .wrapper {
            display: flex;
            height: 100vh;
        }

        /* LEFT: LOGIN / SIGNUP */
        .login-signup-section {
            flex: 1;
            background-color: #ffffff;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .container {
            width: 100%;
            max-width: 400px;
            border: 1px solid #ccc;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            background-color: #fff;
        }

        .logo {
            width: 180px;
            display: block;
            margin: 0 auto 20px;
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        .form-input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }

        .show-password {
            display: flex;
            align-items: center;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .show-password input {
            margin-right: 5px;
        }

        .error, .success {
            color: red;
            text-align: center;
            margin-top: 10px;
        }

        .success {
            color: green;
        }

        /* RIGHT: INFO PANEL */
        .info-section {
            flex: 2;
            background: url('./pic/pic2.jpg') no-repeat center center;
            background-size: cover;
            color: white;
            padding: 50px;
            display: flex;
            flex-direction: column;
            position: relative;
        }

        .info-overlay {
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            width: 100%;
            background-color: rgba(0,0,0,0.5); /* Dark overlay */
            z-index: 1;
        }

        .info-content-wrapper {
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .info-header {
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 40px;
        }

        .info-content {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        .info-title {
            font-size: 36px;
            font-weight: bold;
            background-color: rgba(255,255,255,0.2);
            padding: 20px;
            border-radius: 12px;
        }

        @media screen and (max-width: 768px) {
            .wrapper {
                flex-direction: column;
            }

            .info-section {
                display: none;
            }

            .login-signup-section {
                width: 100%;
                height: 100%;
                padding: 20px;
            }

            .container {
                box-shadow: none;
            }
        }
    </style>
</head>
<body>

<div class="wrapper">

    <!-- LEFT SIDE: LOGIN / SIGNUP FORM -->
    <div class="login-signup-section">
        <div class="container">
            <!-- Logo -->
            <img src="./pic/logo.png" alt="Buklod Unlad HR Logo" class="logo">

            <!-- Login / Sign Up Form -->
            <?php if (!isset($_GET['signup'])): ?>
                <h2>Login</h2>
                <form method="post">
                    <input name="username" type="text" class="form-input" placeholder="Username" required><br>
                    <input id="password" name="password" type="password" class="form-input" placeholder="Password" required><br>
                    <div class="show-password">
                        <input type="checkbox" id="showPassword" onclick="togglePassword()">
                        <label for="showPassword">Show Password</label>
                    </div>
                    <button type="submit" name="login">Login</button>
                </form>
                <p style="text-align: center;">Don't have an account? <a href="?signup=true">Sign up</a></p>
            <?php else: ?>
                <h2>Sign Up</h2>
                <form method="post">
                    <input name="username" type="text" class="form-input" placeholder="Username" required><br>
                    <input id="password" name="password" type="password" class="form-input" placeholder="Password" required><br>
                    <div class="show-password">
                        <input type="checkbox" id="showPassword" onclick="togglePassword()">
                        <label for="showPassword">Show Password</label>
                    </div>
                    <button type="submit" name="signup">Sign Up</button>
                </form>
                <p style="text-align: center;">Already have an account? <a href="?">Login</a></p>
            <?php endif; ?>

            <!-- Messages -->
            <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
            <?php if (isset($success)) echo "<p class='success'>$success</p>"; ?>
        </div>
    </div>

    <!-- RIGHT SIDE: INFO SECTION -->
    <div class="info-section">
        <div class="info-overlay"></div>
        <div class="info-content-wrapper">
            <div class="info-header">Buklod-HR System</div>
        </div>
    </div>

</div>

<!-- Script -->
<script>
    function togglePassword() {
        const pw = document.getElementById("password");
        pw.type = pw.type === "password" ? "text" : "password";
    }
</script>

</body>
</html>
