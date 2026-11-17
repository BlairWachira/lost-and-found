
<?php

session_start();

require_once "db.php";

$message = "";
$identity = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $identity = trim($_POST["identity"] ?? "");
    $password = $_POST["password"] ?? "";

    if (empty($identity) || empty($password)) {

        $message = "Please fill in all fields.";

    } else {

        $sql = "SELECT * FROM users
                WHERE username = ? OR email = ?
                LIMIT 1";

        $stmt = $conn->prepare($sql);
        $stmt->execute([$identity, $identity]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user["password"])) {

            session_regenerate_id(true);

            $_SESSION["user_id"] = $user["id"];
            $_SESSION["username"] = $user["username"];
            $_SESSION["full_name"] = $user["full_name"];

            header("Location: dashboard.php");
            exit;

        } else {

            $message = "Invalid username/email or password.";

        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login | Lost & Found</title>

    <link rel="stylesheet" href="style-login.css">
</head>

<body class="login-page">

    <div class="login-container">

        <!-- Left side image -->
        <div class="login-image">
            <img src="assets/sheep.jpg" alt="Come, take a sit">

            <div class="image-text">
                <h1>Lost & Found</h1>
                <p>Every lost item deserves to be found.</p>
            </div>
        </div>

        <!-- Right side login form -->
        <div class="login-form-section">

            <div class="login-content">

                <h1>Welcome Back</h1>

                <p class="login-subtitle">
                    Login to continue to your account.
                </p>

                <?php if (!empty($message)): ?>

                    <div class="message error">
                        <?php echo htmlspecialchars($message); ?>
                    </div>

                <?php endif; ?>

                <form method="POST" action="">

                    <div class="input-group">

                        <label>Username or Email</label>

                        <input
                            type="text"
                            name="identity"
                            placeholder="Enter username or email"
                            value="<?php echo htmlspecialchars($identity); ?>"
                            required
                        >

                    </div>

                    <div class="input-group">

                        <label>Password</label>

                        <input
                            type="password"
                            name="password"
                            placeholder="Enter your password"
                            required
                        >

                    </div>

                    <button type="submit" class="login-submit">
                        Login
                    </button>

                </form>

                <p class="register-text">
                    Don't have an account?
                    <a href="sign.php">Create an account</a>
                </p>

            </div>

        </div>

    </div>

</body>
</html>
