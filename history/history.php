<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>history</title>
   <link rel="stylesheet" href="../history/history.css" >
   <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
<div class="navbar">
        <nav>
            <ul class="nav-list">
                <li><a href="../index.php">Home</a></li>
                <li><a href="#">About</a></li>
                <li><a href="#">Services</a></li>
                <li><a href="https://rahulbagul.netlify.app" target="_blank">Contact Us</a></li>
                <li><a href="../history/history.php">History</a></li>
            </ul>
        </nav>
        <div class="logout-div">
            <a class="logout-btn" href="../logout.php">Logout <i class="fa-solid fa-right-from-bracket"></i></a>
        </div>
    </div>

    <?php
session_start(); // Start session to access session variables

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
   header("Location: login.php"); // Redirect to login page if not logged in
   exit();
}

$userId = $_SESSION['user_id']; // Get user ID from session

$con = mysqli_connect("localhost", "root", "", "currency_converter");
if (!$con) {
   die("Connection failed: " . mysqli_connect_error());
}

$sql = "SELECT * FROM conversion_history WHERE user_id = ? ORDER BY datetime DESC";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
   echo "<table>";
   echo "<tr><th>Original Amount</th><th>Original Currency</th><th>Target Currency</th><th>Exchange Rate</th><th>Converted Amount</th><th>Date and Time</th></tr>";

   while ($row = $result->fetch_assoc()) {
       echo "<tr>";
       echo "<td>" . htmlspecialchars($row['original_amount']) . "</td>"; // Display original amount
       echo "<td>" . htmlspecialchars($row['base_currency']) . "</td>";
       echo "<td>" . htmlspecialchars($row['target_currency']) . "</td>";
       echo "<td>" . htmlspecialchars($row['exchange_rate']) . "</td>";
       echo "<td class=\"converted-amount\">" . htmlspecialchars($row['converted_amount']) . "</td>";
       echo "<td>" . htmlspecialchars($row['datetime']) . "</td>";
       echo "</tr>";
   }

   echo "</table>";
} else {
   echo "<p>No conversion history found.</p>";
}

$stmt->close();
mysqli_close($con);
?>
</body>
</html>