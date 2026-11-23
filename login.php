
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

    <style>
        .password-wrapper {
            position: relative;
            width: 100%;
        }

        .password-wrapper input {
            width: 100%;
            box-sizing: border-box;
            padding-right: 45px;
        }

        .toggle-password {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            padding: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #666;
        }

        .toggle-password svg {
            width: 21px;
            height: 21px;
            fill: none;
            stroke: currentColor;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .toggle-password:hover {
            color: #222;
        }
    </style>
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

                        <div class="password-wrapper">

                            <input
                                type="password"
                                name="password"
                                id="password"
                                placeholder="Enter your password"
                                required
                            >

                            <button
                                type="button"
                                class="toggle-password"
                                onclick="togglePassword()"
                                aria-label="Show password"
                                aria-pressed="false"
                            >
                                <svg viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                            </button>

                        </div>

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

    <script>
        function togglePassword() {

            const passwordInput = document.getElementById("password");
            const button = document.querySelector(".toggle-password");

            const isHidden = passwordInput.type === "password";

            passwordInput.type = isHidden ? "text" : "password";

            button.setAttribute(
                "aria-label",
                isHidden ? "Hide password" : "Show password"
            );

            button.setAttribute("aria-pressed", isHidden);

            button.innerHTML = isHidden
                ? `<svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M3 3l18 18"/>
                    <path d="M10.6 10.6a2 2 0 002.8 2.8"/>
                    <path d="M9.9 5.2A10.8 10.8 0 0112 5c6.5 0 10 7 10 7a15 15 0 01-4 4.5"/>
                    <path d="M6.2 6.2C3.5 8 2 12 2 12s3.5 7 10 7c1 0 2-.2 2.9-.5"/>
                </svg>`
                : `<svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z"/>
                    <circle cx="12" cy="12" r="3"/>
                </svg>`;
        }
    </script>

</body>
</html>
