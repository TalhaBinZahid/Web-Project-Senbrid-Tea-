<?php
session_start();
// Database configuration
$servername = "localhost"; 
$db_username = "root";
$db_password = "";
$dbname = "senbrid-tea";

// Create connection
$conn = new mysqli($servername, $db_username, $db_password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize login error message variable
$loginError = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve input from the form
    $inputUsername = $_POST['username'] ?? '';
    $inputPassword = $_POST['password'] ?? '';
    
    // SQL query to fetch user information
    $sql = "SELECT * FROM admin_login WHERE userName = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $inputUsername);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the user exists and verify password
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($inputPassword, $user['Password'])) {
            
            $_SESSION['username'] = $user['userName'];

            // Redirect to the dashboard page
            header("Location: ../home_page/home.php");
            exit();
        } else {
            $loginError = "Incorrect username or password.";
        }
    } else {
        $loginError = "Incorrect username or password.";
    }
    // Close connection
    $stmt->close();
    $conn->close();
} 
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Page</title>
  <link rel="stylesheet" href="../Login/login.css">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>
  <div class="login-container">
    <div class="login-image">
      <img src="../../Assests/login.jfif" alt="Login Image">
    </div>
    <div class="login-form">
        <img src="../../Assests/logo.png" class="img_logo" alt="">
        <h2>Login</h2>
         <!-- Session Timeout Message -->
        <?php if (isset($_GET['timeout']) && $_GET['timeout'] === 'true') { ?>
          <p style="color: red;">Your session has expired due to inactivity. Please log in again.</p>
        <?php } ?>

        <!-- Display login error if any -->
        <?php if (!empty($loginError)) { ?>
          <p style="color: red;"><?php echo htmlspecialchars($loginError); ?></p>
        <?php } ?>
        <form action="login.php" method="POST" class="form">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" placeholder="Enter your Username">

        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="Enter your Password">
        
        <div class="forgot-password">
          <a href="forgot-password.php">Forgot Password?</a>
        </div>

        <button type="submit">Login</button>
      </form>
    </div>
  </div>
</body>
</html>
