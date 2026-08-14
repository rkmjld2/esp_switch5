<?php
/*
============================================================
 ESP-SWITCH4 - Stage 2C
 Activate / Deactivate Controller
============================================================

Requires:
    login.php
    admin.php
    db.php

Uses existing:
    controllers table

Changes ONLY:
    controllers.active

Does NOT change:
    esp_control
    last_seen
    controller_id
    device_token

============================================================
*/

session_start();

/* =========================================================
   ADMIN LOGIN CHECK
========================================================= */

if (
    !isset($_SESSION["admin_logged_in"]) ||
    $_SESSION["admin_logged_in"] !== true
) {
    header("Location: login.php");
    exit;
}


/* =========================================================
   DATABASE CONNECTION
========================================================= */

require_once "db.php";


/* =========================================================
   ONLY POST REQUESTS ARE ACCEPTED
========================================================= */

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    header("Location: admin.php");
    exit;
}


/* =========================================================
   GET REQUEST VALUES
========================================================= */

$controller_id = trim(
    $_POST["controller_id"] ?? ""
);

$action = strtoupper(
    trim($_POST["action"] ?? "")
);


/* =========================================================
   VALIDATE CONTROLLER ID
========================================================= */

if ($controller_id === "") {

    header(
        "Location: admin.php?error=Controller+ID+missing"
    );

    exit;
}


/* =========================================================
   VALIDATE ACTION
========================================================= */

if (
    $action !== "ACTIVATE" &&
    $action !== "DEACTIVATE"
) {

    header(
        "Location: admin.php?error=Invalid+action"
    );

    exit;
}


/* =========================================================
   DETERMINE ACTIVE VALUE
========================================================= */

$active = (
    $action === "ACTIVATE"
) ? 1 : 0;


/* =========================================================
   UPDATE CONTROLLER
========================================================= */

$stmt = $conn->prepare("
    UPDATE controllers
    SET active = ?
    WHERE controller_id = ?
");


if (!$stmt) {

    header(
        "Location: admin.php?error=Database+prepare+error"
    );

    exit;
}


$stmt->bind_param(
    "is",
    $active,
    $controller_id
);


if (!$stmt->execute()) {

    $stmt->close();

    header(
        "Location: admin.php?error=Controller+update+failed"
    );

    exit;
}


$affected = $stmt->affected_rows;

$stmt->close();


/* =========================================================
   RETURN TO ADMIN PAGE
========================================================= */

if ($affected > 0) {

    $message = (
        $active === 1
    )
    ? "Controller+activated+successfully"
    : "Controller+deactivated+successfully";

} else {

    $message = (
        $active === 1
    )
    ? "Controller+is+already+active"
    : "Controller+is+already+inactive";
}


header(
    "Location: admin.php?message=" . $message
);

exit;

?>
