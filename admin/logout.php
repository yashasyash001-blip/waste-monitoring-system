<?php

session_start();

/* Remove authority session */
unset($_SESSION["admin_logged_in"]);
unset($_SESSION["admin_email"]);
unset($_SESSION["admin_name"]);

/* Destroy remaining session */
session_destroy();

/* Return to authority login */
header("Location: login.php");
exit;

?>