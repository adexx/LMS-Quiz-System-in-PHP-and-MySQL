<?php
session_start();

function checkAdminAuth() {
    if (!isset($_SESSION['admin_id'])) {
        header('Location: admin_login.php');
        exit;
    }
}

function isAdminLoggedIn() {
    return isset($_SESSION['admin_id']);
}
?>