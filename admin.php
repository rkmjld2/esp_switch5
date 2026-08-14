<?php
/*
============================================================
 ESP-SWITCH4 - Stage 2B
 Administrator Controller List
============================================================

Uses the existing database and existing controllers table.

Stage 2B:
- Requires administrator login from login.php
- Displays all controllers
- Shows controller ID
- Shows customer name
- Shows ACTIVE/INACTIVE
- Shows last_seen
- Shows device token only for administrator
- Does NOT yet activate/deactivate controllers
============================================================
*/

session_start();

/* =========================================================
   SECURITY CHECK
========================================================= */

if (
    !isset($_SESSION["admin_logged_in"]) ||
    $_SESSION["admin_logged_in"] !== true
) {
    header("Location: login.php");
    exit;
}


/* =========================================================
   DATABASE
========================================================= */

require_once "db.php";

date_default_timezone_set("Asia/Kolkata");


/* =========================================================
   GET CONTROLLERS
========================================================= */

$sql = "
    SELECT
        id,
        controller_id,
        device_token,
        customer_name,
        active,
        last_seen
    FROM controllers
    ORDER BY id
";

$result = $conn->query($sql);

if (!$result) {
    die("Database error: " . htmlspecialchars($conn->error));
}


/* =========================================================
   HTML ESCAPE FUNCTION
========================================================= */

function h($value)
{
    return htmlspecialchars(
        (string)$value,
        ENT_QUOTES,
        "UTF-8"
    );
}

?>
<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>ESP-SWITCH4 Admin Controller List</title>

<style>

body {
    margin: 0;
    font-family: Arial, sans-serif;
    background: #f1f3f6;
}

.container {
    width: 95%;
    max-width: 1100px;
    margin: 30px auto;
    background: white;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 3px 15px rgba(0,0,0,0.12);
}

h1 {
    text-align: center;
    margin-top: 0;
}

.admin-info {
    text-align: center;
    margin-bottom: 20px;
    color: #555;
}

.top-buttons {
    text-align: center;
    margin-bottom: 20px;
}

.top-buttons a {
    display: inline-block;
    padding: 10px 18px;
    margin: 5px;
    text-decoration: none;
    color: white;
    border-radius: 5px;
    background: #007bff;
}

.top-buttons a.logout {
    background: #dc3545;
}

.table-wrap {
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
    min-width: 850px;
}

th, td {
    border: 1px solid #ddd;
    padding: 10px;
    text-align: center;
}

th {
    background: #343a40;
    color: white;
}

tr:nth-child(even) {
    background: #f8f9fa;
}

.active {
    color: green;
    font-weight: bold;
}

.inactive {
    color: red;
    font-weight: bold;
}

.note {
    margin-top: 20px;
    padding: 12px;
    background: #fff3cd;
    color: #664d03;
    border-radius: 6px;
    text-align: center;
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


<div class="top-buttons">

    <a href="index.php">
        Controller Control
    </a>

    <a href="logout.php" class="logout">
        Logout
    </a>

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
    <th>Device Token</th>
    <th>Action</th>
</tr>

</thead>

<tbody>

<?php while ($row = $result->fetch_assoc()): ?>

<tr>

    <td>
        <?= h($row["id"]) ?>
    </td>

    <td>
        <strong>
            <?= h($row["controller_id"]) ?>
        </strong>
    </td>

    <td>
        <?= h($row["customer_name"]) ?>
    </td>

    <td>

        <?php if ((int)$row["active"] === 1): ?>

            <span class="active">
                ACTIVE
            </span>

        <?php else: ?>

            <span class="inactive">
                INACTIVE
            </span>

        <?php endif; ?>

    </td>

    <td>
        <?= h($row["last_seen"] ?? "-") ?>
    </td>

    <td>
        <?= h($row["device_token"]) ?>
    </td>

    <td>
        Stage 2C
    </td>

</tr>

<?php endwhile; ?>

</tbody>

</table>

</div>


<div class="note">

Stage 2B only displays the controller list.
<br>
Activate / Deactivate will be added in <strong>Stage 2C</strong>.

</div>

</div>

</body>
</html>
