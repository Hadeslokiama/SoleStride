<?php
declare(strict_types=1);
require_once __DIR__ . '/functions.php';
start_secure_session();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SoleStride</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<header class="site-header">
    <nav class="navbar">
        <a href="/index.php" class="logo">SoleStride</a>
        <ul class="nav-links">
            <li><a href="/index.php">Shop</a></li>
            <li><a href="/about.php">About</a></li>
            <li><a href="/cart.php">Cart</a></li>
            <?php if (is_logged_in()): ?>
                <li><a href="/auth/logout.php">Logout</a></li>
            <?php else: ?>
                <li><a href="/auth/login.php">Login</a></li>
                <li><a href="/auth/register.php">Register</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>
<main class="site-main">