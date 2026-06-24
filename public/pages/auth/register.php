<?php

session_start();

$DB_HOST = "localhost";
$DB_NAME = "gaming_retail_store";
$DB_USER = "root";
$DB_PASS = "";

$pdo = new PDO(
    "mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4",
    $DB_USER,
    $DB_PASS,
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_EMULATE_PREPARES => false]
);

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrfToken = $_SESSION['csrf_token'];

$error = "";
$success = "";
$username = "";
$email = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submittedToken = $_POST['csrf_token'] ?? '';

    if (!hash_equals($_SESSION['csrf_token'], $submittedToken)) {
        $error = "Invalid request. Please refresh and try again.";
    } else {
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['user_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if ($username === '' || $email === '' || $password === '' || $confirmPassword === '') {
            $error = "Please fill in all fields.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Please enter a valid email address.";
        } elseif (strlen($password) < 8) {
            $error = "Password must be at least 8 characters.";
        } elseif ($password !== $confirmPassword) {
            $error = "Passwords do not match.";
        } else {
            $stmt = $pdo->prepare("SELECT userid FROM users WHERE email = ? LIMIT 1");
            $stmt->execute([$email]);

            if ($stmt->fetch()) {
                $error = "Email already exists.";
            } else {
                $passwordHash = password_hash($password, PASSWORD_ARGON2ID);
                $stmt = $pdo->prepare(
                    "INSERT INTO users (username, email, password, usertype) VALUES (?, ?, ?, 'customer')"
                );
                $stmt->execute([$username, $email, $passwordHash]);

                $success = "Registration successful. You can now log in.";
                $username = "";
                $email = "";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Gaming Store</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f9;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .card {
            background: white;
            padding: 30px 26px;
            border-radius: 10px;
            width: 100%;
            max-width: 380px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        h2 { margin-bottom: 22px; color: #2c3e50; }
        .form-group { margin-bottom: 16px; }
        label { display: block; margin-bottom: 5px; font-size: 0.88em; font-weight: bold; color: #444; }
        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 0.95em;
        }
        .show-row { display: flex; align-items: center; gap: 6px; margin-top: 8px; font-size: 0.85em; color: #555; }
        .show-row .login-link { margin-left: auto; color: #2c3e50; font-weight: bold; text-decoration: none; }
        .show-row .login-link:hover { text-decoration: underline; }
        .error { background: #fdecea; color: #a93226; padding: 10px 12px; border-radius: 6px; font-size: 0.88em; margin-bottom: 16px; }
        .success { background: #eafaf1; color: #1e8449; padding: 10px 12px; border-radius: 6px; font-size: 0.88em; margin-bottom: 16px; }
        button {
            width: 100%;
            padding: 11px;
            background: #2c3e50;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 1em;
            cursor: pointer;
            margin-top: 6px;
        }
        button:hover { background: #1a252f; }
    </style>
</head>
<body>
<div class="card">
    <h2>Register</h2>

    <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="POST" action="register.php">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required maxlength="50" value="<?= htmlspecialchars($username) ?>">
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required maxlength="255" value="<?= htmlspecialchars($email) ?>">
        </div>

        <div class="form-group">
            <label for="user_password">
                Password <span style="font-weight:normal; color:#888;">(min 8 characters)</span>
            </label>
            <input type="password" id="user_password" name="user_password" required minlength="8">
        </div>

        <div class="form-group">
            <label for="confirm_password">Confirm password</label>
            <input type="password" id="confirm_password" name="confirm_password" required minlength="8">
            <div class="show-row">
                <input type="checkbox" id="show_pw" onclick="togglePasswords()">
                <label for="show_pw" style="font-weight:normal;">Show password</label>
                <a class="login-link" href="login.php">Login</a>
            </div>
        </div>

        <button type="submit">Register</button>
    </form>
</div>

<script>
    function togglePasswords() {
        const password = document.getElementById('user_password');
        const confirmPassword = document.getElementById('confirm_password');
        const nextType = password.type === 'password' ? 'text' : 'password';

        password.type = nextType;
        confirmPassword.type = nextType;
    }
</script>
</body>
</html>
