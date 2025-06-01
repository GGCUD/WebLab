<?php
session_start();
include 'db_connect.php';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Юридическое агентство</title>
  <link rel="stylesheet" href="style.css" />
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=Roboto:wght@400;500&display=swap" rel="stylesheet" />

  <style>
    html, body {
      margin: 0;
      padding: 0;
      height: 100%;
    }

    body {
      display: flex;
      flex-direction: column;
      min-height: 100vh;
      font-family: 'Roboto', sans-serif;
      background: #f8fafc;
    }

    main {
      flex: 1;
      padding-top: 80px; /* учесть фиксированный хедер */
    }

    .main-header {
      background: #ffffff;
      box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      z-index: 1000;
      height: 80px;
    }

    .header-content {
      max-width: 1200px;
      margin: 0 auto;
      padding: 1rem 2rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .branding {
      display: flex;
      align-items: center;
    }

    .logo-link {
      display: flex;
      align-items: center;
      text-decoration: none;
    }

    .logo-image {
      width: 60px;
      height: 60px;
      margin-right: 1rem;
      transition: transform 0.3s ease;
    }

    .logo-image:hover {
      transform: scale(1.05);
    }

    .logo-text {
      font-family: 'Playfair Display', serif;
      font-size: 1.5rem;
      color: #2c3e50;
      letter-spacing: 0.05em;
    }

    .main-nav ul {
      display: flex;
      gap: 2rem;
      list-style: none;
      margin: 0;
      padding: 0;
    }

    .nav-link {
      font-family: 'Roboto', sans-serif;
      font-weight: 500;
      color: #34495e;
      text-decoration: none;
      padding: 0.5rem 1rem;
      position: relative;
      transition: color 0.3s ease;
    }

    .nav-link::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      width: 0;
      height: 2px;
      background: #3498db;
      transition: width 0.3s ease;
    }

    .nav-link:hover::after {
      width: 100%;
    }

    .nav-link:hover {
      color: #3498db;
    }

    .hamburger {
      display: none;
      padding: 10px;
      background: none;
      border: none;
      cursor: pointer;
    }

    .hamburger-box {
      width: 30px;
      height: 24px;
      display: inline-block;
      position: relative;
    }

    .hamburger-inner,
    .hamburger-inner::before,
    .hamburger-inner::after {
      width: 100%;
      height: 2px;
      background: #2c3e50;
      position: absolute;
      left: 0;
      transition: transform 0.3s ease;
    }

    .hamburger-inner {
      top: 50%;
      transform: translateY(-50%);
    }

    .hamburger-inner::before {
      content: '';
      top: -8px;
    }

    .hamburger-inner::after {
      content: '';
      top: 8px;
    }

    .hamburger.active .hamburger-inner {
      transform: rotate(45deg);
    }

    .hamburger.active .hamburger-inner::before {
      transform: translateY(8px) rotate(-90deg);
    }

    .hamburger.active .hamburger-inner::after {
      opacity: 0;
    }
  </style>
</head>
<body>
<header class="main-header">
  <div class="header-content">
    <div class="branding">
      <a href="index.php" class="logo-link">
        <img src="images/logo.png" alt="Логотип" class="logo-image" />
        <span class="logo-text">Юридическое агентство</span>
      </a>
    </div>
    <nav class="main-nav">
      <ul class="nav-list">
        <li><a href="index.php" class="nav-link">Главная</a></li>
        <li><a href="news.php" class="nav-link">Новости</a></li>
        <li><a href="services.php" class="nav-link">Услуги</a></li>
        <?php if (isset($_SESSION['user'])): ?>
          <li><a href="profile.php" class="nav-link">Кабинет</a></li>
          <li><a href="logout.php" class="nav-link">Выход</a></li>
        <?php else: ?>
          <li><a href="register.php" class="nav-link">Регистрация</a></li>
          <li><a href="login.php" class="nav-link">Авторизация</a></li>
        <?php endif; ?>
      </ul>
    </nav>
    <button class="hamburger" aria-label="Меню">
      <span class="hamburger-box">
        <span class="hamburger-inner"></span>
      </span>
    </button>
  </div>
</header>

<main>
