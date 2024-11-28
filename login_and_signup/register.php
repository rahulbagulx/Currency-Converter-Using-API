<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="login_signup.css">
</head>

<body>
    <?php
    session_start(); // Start the session

    if (isset($_POST['submit'])) {
        $username = $_POST['username'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password
        $email = $_POST['email'];

        // Database connection
        $con = mysqli_connect("localhost", "root", "", "currency_converter");
        if (!$con) {
            die("Connection failed: " . mysqli_connect_error());
        }

        // Check if username already exists
        $stmt = $con->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo "<script>alert('Username already taken. Please choose another one.');</script>";
        } else {
            // Check if email already exists
            $stmt = $con->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                echo "<script>alert('Email already taken. Please choose another one.');</script>";
            } else {
                // Email and username do not exist, proceed with registration
                $stmt = $con->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $username, $password, $email);

                if ($stmt->execute()) {
                    echo "<script>alert('User registered successfully!');</script>";
                    header("Location: login.php"); // Redirect to login page after successful registration
                    exit();
                } else {
                    echo "<script>alert('Registration failed. Please try again.');</script>";
                }
            }
        }

        // Close statement and connection
        $stmt->close();
        mysqli_close($con);
    }
    ?>

    <div class="parent">
        <div class="child">
            <header>Register User</header>
            <form method="post" action="">
                <div class="input-group">
                    <label>Username:</label>
                    <input type="text" name="username" required>
                </div>
                <div class="input-group">
                    <label>Password:</label>
                    <input type="password" name="password" required>
                </div>
                <div class="input-group">
                    <label>Email:</label>
                    <input type="email" name="email" required>
                </div>
                <button name="submit">Register</button>
                <a href="login.php">You Are Already Registered</a>
            </form>
        </div>
    </div>

</body>

</html>