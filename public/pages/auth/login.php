<?php

session_start();

$DB_HOST = "localhost";
$DB_NAME = "gaming_store";  
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

   
    $submittedToken = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'], $submittedToken)) {
        $error = "Invalid request. Please refresh and try again.";

    } else {

        $username = trim($_POST['username'] ?? '');
        $password = $_POST['user_password'] ?? '';

        if (empty($username) || empty($password)) {
            $error = "Please fill in all fields.";

        } elseif (strlen($password) < 8) {
            $error = "Invalid username or password.";

        } else {

            $stmt = $pdo->prepare("SELECT id, password_hash FROM users WHERE username = ? LIMIT 1");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
           
            if ($user && password_verify($password, $user['password_hash'])) {

                session_regenerate_id(true); 
                $_SESSION['user_id']   = $user['id'];
                $_SESSION['username']  = $username;
                $_SESSION['logged_in'] = true;
                header("Location: dashboard.php");
                exit;

            } else {
              
                $error = "Invalid username or password.";
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
    <title>Login | Gaming Store</title>
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
        input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 0.95em;
        }
        .show-row { display: flex; align-items: center; gap: 6px; margin-top: 8px; font-size: 0.85em; color: #555; }
        .error { background: #fdecea; color: #a93226; padding: 10px 12px; border-radius: 6px; font-size: 0.88em; margin-bottom: 16px; }
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
    <h2>Login</h2>

    <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="login.php">

        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required maxlength="50">
        </div>

        <div class="form-group">
            <label for="user_password">
                Password <span style="font-weight:normal; color:#888;">(min 14 characters)</span>
            </label>
            <input type="password" id="user_password" name="user_password" required minlength="14">
            <div class="show-row">
                <input type="checkbox" id="show_pw" onclick="togglePassword()">
                <label for="show_pw" style="font-weight:normal;">Show password</label>
            </div>
        </div>

        <button type="submit">Log In</button>
    </form>
</div>

<script>
    function togglePassword() {
        const field = document.getElementById('user_password');
        field.type = (field.type === 'password') ? 'text' : 'password';
    }
</script>
</body>
</html>