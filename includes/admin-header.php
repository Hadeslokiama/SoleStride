<?php
declare(strict_types=1);
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/auth-check.php';
require_admin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SoleStride Admin</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="admin-body">
<div class="admin-layout">
    <aside class="admin-sidebar">
        <h2 class="admin-logo">SoleStride Admin</h2>
        <ul class="admin-nav">
            <li><a href="/admin/dashboard.php">Dashboard</a></li>
            <li><a href="/admin/inventory.php">Inventory</a></li>
            <li><a href="/admin/manage-users.php">Manage Users</a></li>
            <li><a href="/admin/audit-log.php">Audit Log</a></li>
            <li><a href="/auth/logout.php">Logout</a></li>
        </ul>
    </aside>
    <main class="admin-main">