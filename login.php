<?php
session_start(); // Start the session to manage user authentication.

// Check if the user is already logged in. If yes, redirect to the dashboard page.
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Assuming you retrieved the form data and sanitized it.
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Perform database connection.
    $servername = "sql211.infinityfree.com";
    $username_db = "if0_34678793";
    $password_db = "BGwQzhlufMvams";
    $dbname = "if0_34678793_sc";

    try {
        $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username_db, $password_db);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Retrieve user data from the database based on the provided username.
        $stmt = $pdo->prepare("SELECT ID, Username, PasswordHash FROM users WHERE Username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['PasswordHash'])) {
            // Authentication successful. Set the user session.
            $_SESSION['user_id'] = $user['ID'];
            $_SESSION['username'] = $user['Username'];

            // Redirect to the dashboard page after successful login.
            header("Location:http://sc.000.pe/dashboard");
            exit();
        } else {
            // Invalid login credentials. Set the error message.
            $errorMsg = "Username and/or password incorrect.";
        }
    } catch (PDOException $e) {
        // Handle database errors.
        $errorMsg = "Error: " .  $e->getMessage();
        // You may also show a user-friendly error message and/or log the error.
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
</head>
<body>
    <div style="text-align: center;">
        <h2>Login</h2>
        <?php
        // Display the error message if it's not empty.
        if (isset($errorMsg)) {
            echo '<p style="color: red;">' . $errorMsg . '</p>';
        }
        ?>
        <form action="login.php" method="POST">
            <label for="username">Username:</label>
            <input type="text" name="username" required><br>

            <label for="password">Password:</label>
            <input type="password" name="password" required><br>

            <input type="submit" value="Login">
        </form>
    </div>
</body>
</html>
