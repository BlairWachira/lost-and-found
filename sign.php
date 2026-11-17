
<?php
require_once "db.php";

$message = "";
$messageType = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $full_name = trim($_POST["full_name"] ?? "");
    $username = trim($_POST["username"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirm_password = $_POST["confirm_password"] ?? "";

    if (
        empty($full_name) ||
        empty($username) ||
        empty($email) ||
        empty($password) ||
        empty($confirm_password)
    ) {

        $message = "Please fill in all fields.";
        $messageType = "error";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $message = "Please enter a valid email address.";
        $messageType = "error";

    } elseif ($password !== $confirm_password) {

        $message = "Passwords do not match.";
        $messageType = "error";

    } elseif (strlen($password) < 8) {

        $message = "Password must be at least 8 characters.";
        $messageType = "error";

    } else {

        try {

            $check = $conn->prepare(
                "SELECT id FROM users
                 WHERE username = ? OR email = ?"
            );

            $check->execute([$username, $email]);

            if ($check->rowCount() > 0) {

                $message = "Username or email already exists.";
                $messageType = "error";

            } else {
                $hashed_password = password_hash(
                    $password,
                    PASSWORD_DEFAULT
                );

                $sql = "INSERT INTO users
                        (full_name, username, email, password)
                        VALUES (?, ?, ?, ?)";

                $stmt = $conn->prepare($sql);

                $stmt->execute([
                    $full_name,
                    $username,
                    $email,
                    $hashed_password
                ]);

                $message = "Account created successfully!";
                $messageType = "success";
                //header("location : login.php");

            }

        } catch (PDOException $e) {

            $message = "Something went wrong. Please try again.";
            $messageType = "error";

        }

    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Create Account | Lost & Found</title>

    <link rel="stylesheet" href="style-sign.css">
</head>

<body>

    <div class="signup-container">

        <div class="signup-box">

            <h1>Create Account</h1>

            <p class="subtitle">
                Join Lost & Found and help reunite people with their belongings.
            </p>

            <?php if (!empty($message)): ?>

                <div class="message <?php echo $messageType; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>

            <?php endif; ?>

            <form action="" method="POST">

                <div class="input-group">
                    <label>Full Name</label>
                    <input
                        type="text"
                        name="full_name"
                        placeholder="Enter your full name"
                        value="<?php echo htmlspecialchars($full_name ?? ''); ?>"
                        required
                    >
                </div>

                <div class="input-group">
                    <label>Username</label>
                    <input
                        type="text"
                        name="username"
                        placeholder="Choose a username"
                        value="<?php echo htmlspecialchars($username ?? ''); ?>"
                        required
                    >
                </div>

                <div class="input-group">
                    <label>Email Address</label>
                    <input
                        type="email"
                        name="email"
                        placeholder="Enter your email"
                        value="<?php echo htmlspecialchars($email ?? ''); ?>"
                        required
                    >
                </div>

                <div class="input-group">
                    <label>Password</label>
                    <input
                        type="password"
                        name="password"
                        placeholder="Create a password"
                        required
                    >
                </div>

                <div class="input-group">
                    <label>Confirm Password</label>
                    <input
                        type="password"
                        name="confirm_password"
                        placeholder="Confirm your password"
                        required
                    >
                </div>

                <button type="submit" class="signup-btn">
                    Create Account
                </button>

            </form>

            <p class="login-link">
                Already have an account?
                <a href="login.php">Login</a>
            </p>

        </div>

    </div>

</body>
</html>
