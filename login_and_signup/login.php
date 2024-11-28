<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="login_signup.css">
</head>

<body>

    <?php
    session_start(); // Start the session

    if (isset($_POST['submit'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Database connection
        $con = mysqli_connect("localhost", "root", "", "currency_converter");
        if (!$con) {
            die("Connection failed: " . mysqli_connect_error());
        }

        // Prepared statement to prevent SQL injection
        $stmt = $con->prepare("SELECT id, password FROM users WHERE username=?");
        $stmt->bind_param("s", $username);

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                // Verify the password
                if (password_verify($password, $row['password'])) {
                    $_SESSION['user_id'] = $row['id']; // Store user ID in session
                    echo "<script>alert('Login successful!'); window.location.href='../index.php';</script>"; // Redirect to main page after successful login
                    exit();
                } else {
                    echo "<script>alert('Invalid username or password. Please try again.');</script>";
                }
            } else {
                echo "<script>alert('User not found.');</script>";
            }
        }

        $stmt->close();
        mysqli_close($con);
    }
    ?>

    <div class="parent">
        <div class="child">
            <header>Login</header>
            <form method="post" action="">
                <div class="input-group">
                    <label>Username:</label>
                    <input type="text" name="username" required>
                </div>
                <div class="input-group">
                    <label>Password:</label>
                    <input type="password" name="password" required>

                </div>

                <button name="submit">Login</button>

                <!-- Link to registration -->
                <a href='./register.php'>Not registered? Sign up here!</a>
            </form>
        </div>
    </div>

</body>

</html>