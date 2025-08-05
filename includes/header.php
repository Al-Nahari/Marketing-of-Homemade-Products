<?php
if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

$page_title = $page_title ?? 'نظام العائلات المنتجة';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($page_title) ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="/ene/public/index.php"><i class="fas fa-home"></i> العائلات المنتجة</a>
        <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="/ene/public/products.php">المنتجات</a></li>
                <?php if (isLoggedIn()): ?>
                    <?php if (isAdmin()): ?>
                        <li class="nav-item"><a class="nav-link" href="/ene/admin/dashboard.php">لوحة المسؤول</a></li>
                    <?php elseif (isFamily()): ?>
                        <li class="nav-item"><a class="nav-link" href="/ene/family/dashboard.php">لوحتي</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="/ene/user/dashboard.php">لوحتي</a></li>
                    <?php endif; ?>
                <?php endif; ?>
            </ul>
            <ul class="navbar-nav">
                <?php if (isLoggedIn()): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle"></i> <?= htmlspecialchars($_SESSION['full_name'] ?? '') ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="/user/profile.php">الملف الشخصي</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="/ene/public/logout.php">تسجيل الخروج</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="/ene/public/login.php">تسجيل الدخول</a></li>
                    <li class="nav-item"><a class="nav-link" href="/ene/public/register.php">تسجيل جديد</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<main class="container my-5">
    <?php displayFlashMessages(); ?>
