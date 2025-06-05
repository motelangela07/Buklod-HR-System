<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Buklod Unlad HR</title>
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

        /* LEFT: LOGIN */
        .login-section {
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

        .error {
            color: red;
            text-align: center;
            margin-top: 10px;
        }

        .signup-link {
            text-align: center;
            margin-top: 15px;
            font-size: 14px;
        }

        .signup-link a {
            color: #4CAF50;
            text-decoration: none;
        }

        .signup-link a:hover {
            text-decoration: underline;
        }

        /* RIGHT: INFO PANEL */
        .info-section {
            flex: 2;
            background: url('./pic./pic2.jpg') no-repeat center center;
            background-size: 85%;
            color: blue;
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
            font-size: 25px;
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

            .login-section {
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

    <!-- LEFT SIDE: LOGIN FORM -->
    <div class="login-section">
        <div class="container">
            <!-- Logo -->
            <img src="./pic./logo.png" alt="Buklod Unlad HR Logo" class="logo">

            <!-- Login Form -->
            <h2>Login</h2>
            <form method="post">
                <input name="username" type="text" class="form-input" placeholder="Username" required><br>
                <input id="password" name="password" type="password" class="form-input" placeholder="Password" required><br>

                <!-- Show Password -->
                <div class="show-password">
                    <input type="checkbox" id="showPassword" onclick="togglePassword()">
                    <label for="showPassword">Show Password</label>
                </div>

                <button type="submit">Login</button>
            </form>

            <!-- Error -->
            <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>

            <!-- Sign Up Link -->
            <div class="signup-link">
                Don’t have an account? <a href="signup.php">Sign up</a>
            </div>
        </div>
    </div>

    <!-- RIGHT SIDE: INFO SECTION -->
    <div class="info-section">
        <div class="info-overlay"></div>
        <div class="info-content-wrapper">
            <div class="info-header">Buklod-Unlad Multi-Purpose Cooperative - HR System</div>
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
