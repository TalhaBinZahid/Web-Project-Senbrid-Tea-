<?php
$conn = mysqli_connect("localhost", "root", "", "senbrid-tea");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$message = "";

// Add or update user
if (isset($_POST['submit'])) {
    $userId = $_POST['userId'] ?? null;
    $userName = $_POST['userName'];
    $email = $_POST['email'];
    $role = $_POST['role']; 
    $password = $_POST['password'] ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;

    if ($userId) { // If editing, update the record
        $stmt = $conn->prepare("UPDATE add_user SET userName = ?, email = ?, role = ?" . ($password ? ", password = ?" : "") . " WHERE id = ?");
        if ($password) {
            $stmt->bind_param("ssssi", $userName, $email, $role, $password, $userId);
        } else {
            $stmt->bind_param("sssi", $userName, $email, $role, $userId);
        }
        $message = $stmt->execute() ? "User updated successfully!" : "Error updating user: " . $stmt->error;
        $stmt->close();
    } else { // Otherwise, insert a new record
        $stmt = $conn->prepare("INSERT INTO add_user (userName, email, role, password) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $userName, $email, $role, $password);
        $message = $stmt->execute() ? "Data successfully entered!" : "Error: " . $stmt->error;
        $stmt->close();
    }
}

// Fetch all users
$sql = "SELECT * FROM add_user";
$result = mysqli_query($conn, $sql);
$users = [];

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $users[] = $row;
    }
} else {
    echo "Error fetching users: " . mysqli_error($conn);
}

// Delete the user
if (isset($_GET['id']) && !empty($_GET['delete'])) {
    $id = (int)$_GET['id'];
    $stmt = $conn->prepare("DELETE FROM add_user WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo "Record deleted successfully";
    } else {
        echo "Error deleting record: " . $stmt->error;
    }
    $stmt->close();
    header("Location: home.php"); // Redirect after deletion
    exit;
}

// Fetch user data for editing
$userData = [];
if (isset($_GET['id']) && isset($_GET['edit']) && $_GET['edit'] === 'true') {
    $id = (int)$_GET['id']; // Cast to int for security
    $stmt = $conn->prepare("SELECT * FROM add_user WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && mysqli_num_rows($result) > 0) {
        $userData = $result->fetch_assoc();
    } else {
        echo "Error fetching user data: " . mysqli_error($conn);
    }
}

$conn->close(); // Close the connection

?>

<script>
// Show the message in an alert
window.onload = function() {
    var message = "<?php echo addslashes($message); ?>"; // Prevent issues with quotes
    if (message) {
        alert(message);
        // Redirect after 2 seconds (2000 milliseconds)
        setTimeout(function() {
            window.location.href = 'home.php';
        }, 2000); // 2 seconds
    }
}
</script>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin Panel</title>
    <link rel="stylesheet" href="../home_page/index.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>

<div class="admin-panel">
    <!-- Sidebar -->
    <aside class="sidebar">
        <h2>Admin Panel</h2>
        <ul>
            <li><a href="#dashboard">Dashboard</a></li>
            <li><a href="#user-management">User Management</a></li>
            <li><a href="#profile">Profile</a></li>
            <li><a href="">Sign-out</a></li>
        </ul>
    </aside>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Bar -->
        <header class="top-bar">
            <h1>Welcome Abdullah</h1>
            <div class="search">
                <input type="text" placeholder="Search users...">
                <button>Search</button>
            </div>
        </header>

        <!-- User Management Section -->
        <section id="user-management">
            <h2>User Management</h2>
            <!-- Add/Edit User Form -->
            <div class="add-user-form">
                <h3><?php echo !empty($userData) ? "Edit User" : "Add New User"; ?></h3>
                <form action="home.php" method="POST" id="registrationForm">
                    <input type="hidden" name="userId" value="<?php echo !empty($userData) ? ($userData['id']) : ''; ?>">
                    <input type="text" name="userName" id="username" placeholder="Username" value="<?php echo !empty($userData) ? ($userData['userName']) : ''; ?>" required>
                    <small class="error" id="usernameError"></small>
                    <input type="email" name="email" id="email" placeholder="Email" value="<?php echo !empty($userData) ? ($userData['email']) : ''; ?>" required>
                    <small class="error" id="emailError"></small>
                    <input type="text" name="role" id="role" placeholder="Role" value="<?php echo !empty($userData) ? ($userData['role']) : ''; ?>" required>
                    <small class="error" id="roleError"></small>
                    <input type="password" name="password" id="password" placeholder="New Password (leave blank to keep current)">
                    <small class="error" id="passwordError"></small>
                    <button type="submit" name="submit"><?php echo !empty($userData) ? "Update User" : "Add User"; ?></button>
                </form>
            </div>

            <!-- User List Table -->
            <div class="user-list">
                <h3>User List</h3>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if(!empty($users)) {
                            // Use foreach to iterate over the users array
                            foreach ($users as $user) {
                                echo "<tr>
                                        <td>" . htmlspecialchars($user['id']) . "</td>
                                        <td>" . htmlspecialchars($user['userName']) . "</td>
                                        <td>" . htmlspecialchars($user['email']) . "</td>
                                        <td>" . htmlspecialchars($user['role']) . "</td>
                                        <td>
                                            <a href='home.php?id=" . htmlspecialchars($user['id']) . "&edit=true'><button>Edit</button></a>
                                            <a href='home.php?id=" . htmlspecialchars($user['id']) . "&delete=true' onclick='return confirm(\"Are you sure you want to delete this item?\")'><button>Delete</button></a>
                                        </td>
                                    </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5'>No users found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</div>

</body>
</html>
