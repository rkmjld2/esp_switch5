<?php
/*
============================================================
 ESP-SWITCH4 - Stage 2B + 2C
 Administrator Controller List
============================================================
Requires:
    login.php
    admin_action.php
    db.php

Uses existing controllers table.
The buttons below perform the Stage 2C activate/deactivate
operation through admin_action.php.
============================================================
*/

session_start();

if (
    !isset($_SESSION["admin_logged_in"]) ||
    $_SESSION["admin_logged_in"] !== true
) {
    header("Location: login.php");
    exit;
}

require_once "db.php";

function h($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}

$message = trim($_GET["message"] ?? "");
$error   = trim($_GET["error"] ?? "");

$sql = "
    SELECT
        id,
        controller_id,
        customer_name,
        active,
        last_seen
    FROM controllers
    ORDER BY id
";

$result = $conn->query($sql);

if (!$result) {
    die("Database error: " . h($conn->error));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ESP-SWITCH4 Administrator</title>

<style>
body {
    margin:0;
    font-family:Arial,sans-serif;
    background:#f1f3f6;
}
.container {
    width:95%;
    max-width:1100px;
    margin:30px auto;
    background:white;
    padding:25px;
    border-radius:12px;
    box-shadow:0 3px 15px rgba(0,0,0,.12);
}
h1 { text-align:center; margin-top:0; }
.admin-info {
    text-align:center;
    margin-bottom:20px;
    color:#555;
}
.top-buttons {
    text-align:center;
    margin-bottom:20px;
}
.top-buttons a {
    display:inline-block;
    padding:10px 18px;
    margin:5px;
    text-decoration:none;
    color:white;
    border-radius:5px;
    background:#007bff;
}
.top-buttons a.logout { background:#dc3545; }

.message,.error {
    padding:12px;
    border-radius:6px;
    margin-bottom:15px;
    text-align:center;
    font-weight:bold;
}
.message { background:#d4edda; color:#155724; }
.error { background:#f8d7da; color:#721c24; }

.table-wrap { overflow-x:auto; }

table {
    width:100%;
    border-collapse:collapse;
    min-width:750px;
}
th,td {
    border:1px solid #ddd;
    padding:10px;
    text-align:center;
}
th {
    background:#343a40;
    color:white;
}
tr:nth-child(even) { background:#f8f9fa; }

.active {
    color:green;
    font-weight:bold;
}
.inactive {
    color:red;
    font-weight:bold;
}

.action-form { margin:0; }

.action-button {
    border:0;
    border-radius:5px;
    padding:9px 14px;
    color:white;
    font-size:14px;
    cursor:pointer;
}
.activate { background:#28a745; }
.deactivate { background:#dc3545; }

.action-button:hover { opacity:.85; }

.note {
    margin-top:20px;
    padding:12px;
    background:#fff3cd;
    color:#664d03;
    border-radius:6px;
    text-align:center;
}
</style>
</head>

<body>

<div class="container">

<h1>ESP-SWITCH4</h1>

<div class="admin-info">
    <strong>Administrator Controller List</strong><br>
    Logged in as:
    <?= h($_SESSION["admin_username"] ?? "Administrator") ?>
</div>

<?php if ($message !== ""): ?>
<div class="message"><?= h($message) ?></div>
<?php endif; ?>

<?php if ($error !== ""): ?>
<div class="error"><?= h($error) ?></div>
<?php endif; ?>

<div class="top-buttons">
    <a href="index.php">Controller Control</a>
    <a href="logout.php" class="logout">Logout</a>
</div>

<div class="table-wrap">

<table>
<thead>
<tr>
    <th>ID</th>
    <th>Controller ID</th>
    <th>Customer Name</th>
    <th>Status</th>
    <th>Last Seen (IST)</th>
    <th>Action</th>
</tr>
</thead>

<tbody>

<?php while ($row = $result->fetch_assoc()): ?>

<tr>

<td><?= h($row["id"]) ?></td>

<td>
    <strong><?= h($row["controller_id"]) ?></strong>
</td>

<td><?= h($row["customer_name"]) ?></td>

<td>
<?php if ((int)$row["active"] === 1): ?>
    <span class="active">ACTIVE</span>
<?php else: ?>
    <span class="inactive">INACTIVE</span>
<?php endif; ?>
</td>

<td><?= h($row["last_seen"] ?? "-") ?></td>

<td>

<?php if ((int)$row["active"] === 1): ?>

<form method="POST"
      action="admin_action.php"
      class="action-form"
      onsubmit="return confirm('Deactivate <?= h($row["controller_id"]) ?>?');">

    <input type="hidden"
           name="controller_id"
           value="<?= h($row["controller_id"]) ?>">

    <input type="hidden"
           name="action"
           value="DEACTIVATE">

    <button type="submit"
            class="action-button deactivate">
        DEACTIVATE
    </button>

</form>

<?php else: ?>

<form method="POST"
      action="admin_action.php"
      class="action-form"
      onsubmit="return confirm('Activate <?= h($row["controller_id"]) ?>?');">

    <input type="hidden"
           name="controller_id"
           value="<?= h($row["controller_id"]) ?>">

    <input type="hidden"
           name="action"
           value="ACTIVATE">

    <button type="submit"
            class="action-button activate">
        ACTIVATE
    </button>

</form>

<?php endif; ?>

</td>

</tr>

<?php endwhile; ?>

</tbody>
</table>

</div>

<div class="note">
<strong>Stage 2C:</strong>
The buttons change only the <code>controllers.active</code>
field. They do not change <code>last_seen</code> or
<code>esp_control</code>.
</div>

</div>

</body>
</html>
