<?php
session_start();
if (!isset($_SESSION['auth_user_id']) || !isset($_SESSION['current_role']) || $_SESSION['current_role'] !== 'admin') {
    header('Location: ../../index.html');
    exit;
}
?>
