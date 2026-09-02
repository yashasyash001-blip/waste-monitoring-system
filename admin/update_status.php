<?php

session_start();

require_once __DIR__ . '/../config/db.php';


/* Check authority login */

if (
    !isset($_SESSION["admin_logged_in"]) ||
    $_SESSION["admin_logged_in"] !== true
) {
    header("Location: login.php");
    exit;
}


/* Get complaint ID and status */

$complaint_id = trim($_POST["complaint_id"] ?? "");
$status = trim($_POST["status"] ?? "");


/* Allowed statuses */

$allowed_statuses = [
    "Pending",
    "In Progress",
    "Resolved",
    "Rejected"
];


/* Validate */

if (
    $complaint_id === "" ||
    !in_array($status, $allowed_statuses, true)
) {
    header("Location: complaints.php");
    exit;
}


try {

    /* Update complaint */

    $result = $database->complaints->updateOne(
        [
            "complaint_id" => $complaint_id
        ],
        [
            '$set' => [
                "status" => $status,
                "updated_at" =>
                    new MongoDB\BSON\UTCDateTime()
            ]
        ]
    );

} catch (Exception $e) {

    /* If database update fails, return to complaints page */

    header("Location: complaints.php");
    exit;

}


/* Return to complaints page */

header("Location: complaints.php");
exit;

?>