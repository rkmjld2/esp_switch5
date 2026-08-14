<?php
/*
============================================================
 ESP-SWITCH4 - Stage 2C
 Administrator Activate / Deactivate
============================================================
*/

session_start();

/* ---------------------------------------------------------
   Check administrator login
--------------------------------------------------------- */

if (
    !isset($_SESSION["admin_logged_in"]) ||
    $_SESSION["admin_logged_in"] !== true
) {
    header("Location: login.php");
    exit;
}


/* ---------------------------------------------------------
   Database connection
--------------------------------------------------------- */

require_once "db.php";


/* ---------------------------------------------------------
   Accept POST request only
--------------------------------------------------------- */

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: admin.php?error=Invalid+request");
    exit;
}


/* ---------------------------------------------------------
   Read submitted values
--------------------------------------------------------- */

$controller_id = trim($_POST["controller_id"] ?? "");
$action        = strtoupper(trim($_POST["action"] ?? ""));


/* ---------------------------------------------------------
   Validate controller ID
--------------------------------------------------------- */

if ($controller_id === "") {
    header("Location: admin.php?error=Controller+ID+missing");
    exit;
}


/* ---------------------------------------------------------
   Determine new active value
--------------------------------------------------------- */

if ($action === "DEACTIVATE") {

    $new_active = 0;

} elseif ($action === "ACTIVATE") {

    $new_active = 1;

} else {

    header("Location: admin.php?error=Invalid+action");
    exit;
}


/* ---------------------------------------------------------
   Update controllers.active
   IMPORTANT: use controller_id, NOT id
--------------------------------------------------------- */

$stmt = $conn->prepare("
    UPDATE controllers
    SET active = ?
    WHERE controller_id = ?
");

if (!$stmt) {
    header(
        "Location: admin.php?error=" .
        urlencode("Prepare failed: " . $conn->error)
    );
    exit;
}


$stmt->bind_param(
    "is",
    $new_active,
    $controller_id
);


if (!$stmt->execute()) {

    $error = $stmt->error;

    $stmt->close();

    header(
        "Location: admin.php?error=" .
        urlencode("Update failed: " . $error)
    );

    exit;
}


/* ---------------------------------------------------------
   Check that a controller row was actually changed
--------------------------------------------------------- */

$affected = $stmt->affected_rows;

$stmt->close();


/*
   affected_rows can be 0 if the requested status is already
   the same. Therefore verify the actual database value.
*/

$check = $conn->prepare("
    SELECT active
    FROM controllers
    WHERE controller_id = ?
");

if (!$check) {
    header(
        "Location: admin.php?error=" .
        urlencode("Verification failed")
    );
    exit;
}

$check->bind_param("s", $controller_id);
$check->execute();

$check->bind_result($actual_active);

if (!$check->fetch()) {

    $check->close();

    header(
        "Location: admin.php?error=" .
        urlencode("Controller not found")
    );

    exit;
}

$check->close();


/* ---------------------------------------------------------
   Confirm actual database value
--------------------------------------------------------- */

if ((int)$actual_active !== $new_active) {

    header(
        "Location: admin.php?error=" .
        urlencode("Database status was not changed")
    );

    exit;
}


/* ---------------------------------------------------------
   Success message
--------------------------------------------------------- */

if ($new_active === 0) {

    $message = $controller_id . " deactivated successfully";

} else {

    $message = $controller_id . " activated successfully";
}


/* ---------------------------------------------------------
   Return to administrator page
--------------------------------------------------------- */

header(
    "Location: admin.php?message=" .
    urlencode($message)
);

exit;

?>
