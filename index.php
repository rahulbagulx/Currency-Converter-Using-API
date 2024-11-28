<?php
session_start(); // Start the session

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ./login_and_signup/login.php"); // Redirect to login page if not logged in
    exit();
}

$con = mysqli_connect("localhost", "root", "", "currency_converter");
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

// Initialize variables for displaying converted amount and exchange rate
$converted_amount = null;
$exchange_rate = null;
$history_saved = false; // Flag to check if history is saved

if (isset($_POST['submit'])) {
    $api_key = "983bf558e23137860fe27e4c";
    $base_currency = $_POST['from'];
    $target_currency = $_POST['to'];
    $amount = $_POST['amt'];

    // API request
    $url = "https://v6.exchangerate-api.com/v6/{$api_key}/latest/{$base_currency}";
    $response = @file_get_contents($url);

    if ($response === FALSE) {
        echo "<p>Failed to retrieve data from the exchange rate API.</p>";
    } else {
        $response_json = json_decode($response);
        if (isset($response_json->conversion_rates->$target_currency)) {
            $exchange_rate = $response_json->conversion_rates->$target_currency;
            $converted_amount = $amount * $exchange_rate;

            // Insert into conversion_history with user_id
            $userId = $_SESSION['user_id']; // Get user ID from session
            $stmt = $con->prepare("INSERT INTO conversion_history (user_id, base_currency, target_currency, exchange_rate, converted_amount, original_amount, datetime) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $datetime = date('Y-m-d H:i:s');
            $stmt->bind_param("issddds", $userId, $base_currency, $target_currency, $exchange_rate, $converted_amount, $amount, $datetime);

            if ($stmt->execute()) {
                $history_saved = true; // Set flag to true if history is saved
            } else {
                echo "<p>Error saving conversion history: " . mysqli_error($con) . "</p>";
            }

            $stmt->close();
        } else {
            echo "<p>Invalid target currency.</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Currency Converter</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
    <div class="navbar">
        <nav>
            <ul class="nav-list">
                <li><a href="#">Home</a></li>
                <li><a href="#">About</a></li>
                <li><a href="#">Services</a></li>
                <li><a href="https://rahulbagul.netlify.app" target="_blank">Contact Us</a></li>
                <li><a href="History/history.php">History</a></li>
            </ul>
        </nav>
        <div class="logout-div">
            <a class="logout-btn" href="logout.php">Logout <i class="fa-solid fa-right-from-bracket"></i></a>
        </div>
    </div>

    <div class="parent">
        <div class="child">
            <header>Currency Converter</header>


            <form action="" method="POST">
                <div class="amount">
                    <p class="en-amt">Enter Amount</p>
                    <input type="text" name="amt" value="1" required>
                </div>
                <div class="drop-list">
                    <div class="from">
                        <p class="fname">From</p>
                        <div class="select-box">
                            <img src="https://flagcdn.com/48x36/us.png" alt="flag">
                            <select name="from" required><!-- Options Tag Are Inserted From JavaScript --></select>
                        </div>
                    </div>
                    <div class="to">
                        <p class="tname">To</p>
                        <div class="select-box">
                            <img src="https://flagcdn.com/48x36/in.png" alt="flag">
                            <select name="to" required><!-- Options Tag Are Inserted From JavaScript --></select>
                        </div>
                    </div>
                </div>

                <!-- Display converted amount and exchange rate -->
                <?php if ($converted_amount !== null && $exchange_rate !== null): ?>
                    <p class="result">Converted Amount: <strong><?php echo number_format($converted_amount, 2); ?></strong></p>
                    <p>Exchange Rate: <strong><?php echo number_format($exchange_rate, 4); ?></strong></p>
                <?php endif; ?>

                <!-- Alert for saving history -->
                <script>
                    <?php if ($history_saved): ?>
                        alert("Conversion history saved successfully!");
                    <?php endif; ?>
                </script>
                <!-- Button to get exchange rate -->
                <button name="submit">Get Exchange Rate</button>
            </form>
        </div>
    </div>

    <script src="js/country-list.js"></script>
    <script src="js/script.js"></script>

</body>

</html>

<?php mysqli_close($con); ?>