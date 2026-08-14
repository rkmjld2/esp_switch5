<?php
session_start();

/*
 ESP-SWITCH4 - Stage 2A
 Administrator Login

 This file does not modify the existing database tables.
 Change the username and password before deployment.
*/

$ADMIN_USERNAME = "admin";
$ADMIN_PASSWORD = "ESP-SWITCH4-ADMIN-2026";

if (isset($_SESSION["admin_logged_in"]) &&
    $_SESSION["admin_logged_in"] === true) {
    header("Location: admin.php");
    exit;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    if (hash_equals($ADMIN_USERNAME, $username) &&
        hash_equals($ADMIN_PASSWORD, $password)) {

        session_regenerate_id(true);

        $_SESSION["admin_logged_in"] = true;
        $_SESSION["admin_username"] = $username;

        header("Location: admin.php");
        exit;

    } else {
        $error = "Invalid administrator username or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ESP-SWITCH4 Administrator Login</title>
<style>
body {
    margin:0;
    padding:0;
    font-family:Arial,sans-serif;
    background:#f2f2f2;
}
.login-container {
    width:360px;
    max-width:90%;
    margin:100px auto;
    background:white;
    padding:30px;
    border-radius:10px;
    box-shadow:0 3px 12px rgba(0,0,0,.20);
    text-align:center;
}
h1 { margin-top:0; color:#222; }
h2 { color:#555; font-size:20px; }
input[type=text], input[type=password] {
    width:100%;
    box-sizing:border-box;
    padding:12px;
    margin:8px 0 15px;
    border:1px solid #aaa;
    border-radius:5px;
    font-size:16px;
}
button {
    width:100%;
    padding:12px;
    background:#007bff;
    color:white;
    border:0;
    border-radius:5px;
    font-size:17px;
    cursor:pointer;
}
button:hover { background:#0056b3; }
.error {
    background:#f8d7da;
    color:#721c24;
    padding:10px;
    border-radius:5px;
    margin-bottom:15px;
}
.note {
    margin-top:20px;
    color:#666;
    font-size:13px;
}
</style>
</head>
<body>
<div class="login-container">
    <h1>ESP-SWITCH4</h1>
    <h2>Administrator Login</h2>

    <?php if ($error !== ""): ?>
        <div class="error">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <label for="username">Administrator Username</label>
        <input type="text" id="username" name="username"
               required autocomplete="username">

        <label for="password">Administrator Password</label>
        <input type="password" id="password" name="password"
               required autocomplete="current-password">

        <button type="submit">LOGIN</button>
    </form>

    <div class="note">Stage 2A - Administrator Login</div>
</div>
</body>
</html>
