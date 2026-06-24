<?php

session_start();

if (empty($_SESSION['logged_in'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Gaming Store</title>
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
            padding: 40px 30px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            max-width: 380px;
        }
        h2 { color: #2c3e50; margin-bottom: 10px; }
        p { color: #666; margin-bottom: 24px; }
        a.logout {
            display: inline-block;
            padding: 10px 24px;
            background: #2c3e50;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-size: 0.95em;
        }
        a.logout:hover { background: #1a252f; }
    </style>
</head>
<body>
    <div class="card">
        <h2>Welcome! 🎮</h2>
        <p>You are logged in as
            <strong><?= htmlspecialchars($_SESSION['username'] ?? 'User') ?></strong>
        </p>
        <a class="logout" href="logout.php">Log out</a>
    </div>
</body>
</html>