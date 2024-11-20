<?php
// Connect to the database
$conn = mysqli_connect("localhost", "cglugcom_srm", ",Adgjmptw1", "cglugcom_mon_nite");

// Start the session
session_start();

// Handle the form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and retrieve the form data
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    
    // Check the database for the username and password
    $sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
    $result = mysqli_query($conn, $sql);
    
    // If user is found, create a session
    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];  // Assuming roles are stored for different staff levels
        header("Location: dashboard.php");
        exit;
    } else {
        $error_message = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MON-NITE</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            overflow: hidden; /* Prevent scrolling */
            position: relative; /* Positioning for the pseudo-element */
        }

        body::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: url("images/moneta2.png"); /* Add your background image here */
            background-size: cover;
            background-position: center center; /* Center image horizontally and vertically */
            background-attachment: fixed; /* Keep background fixed on scroll */
            filter: blur(2px); /* Add blur effect to the background image */
            z-index: -1; /* Send the pseudo-element behind the content */
        }

        .login-container {
            background-color: rgba(255, 255, 255, 0.7); /* Make the container slightly transparent */
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            padding: 30px;
            max-width: 300px;
            width: 80%;
        }

        h1 {
            color: #007bff;
            font-size: 24px;
            margin-bottom: 8px;
            text-align: center;
        }

        .del {
            color: #007bff;
            font-size: 15px;
            margin-top: 2px;
            text-align: center;
        }
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 8px;
            margin: 8px 0;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        input[type="checkbox"] {
            margin-top: 8px;
        }

        button {
            background-color: #007bff;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
        }

        button:hover {
            background-color: #004080;
        }

        .login-container a {
            color: #007bff;
            text-decoration: none;
            display: block;
            margin-top: 10px;
            text-align: center;
        }

        .login-container a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>MON-NITE<br>
        <span class="del">PHONES & COMPUTER ACCESSORIES</span></h1>
        
        <?php if (isset($error_message)): ?>
            <p class="error"><?php echo $error_message; ?></p>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <input type="text" name="username" id="username" placeholder="Username" required>
            </div>
            <div class="form-group">
                <input type="password" name="password" id="password" placeholder="Password" required>
            </div>
            <input type="checkbox" id="show-password" onclick="togglePassword()"> Show Password
            <button type="submit">Login</button>
        </form>
    </div>
    <script>
        function togglePassword() {
            var passwordField = document.getElementById("password");
            if (passwordField.type === "password") {
                passwordField.type = "text";
            } else {
                passwordField.type = "password";
            }
        }
    </script>
</body>
</html>
