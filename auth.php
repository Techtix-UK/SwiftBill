<?php
// auth.php
session_start();

// If the session variable isn't set, kick them to the login page
if (empty($_SESSION['swiftbill_logged_in'])) {
    header("Location: login.php");
    exit;
}