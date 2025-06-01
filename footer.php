<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <title>Юридическое агентство</title>
  <style>
    html, body {
      height: 100%;
      margin: 0;
      padding: 0;
    }
    body {
      display: flex;
      flex-direction: column;
      min-height: 100vh;
      font-family: 'Segoe UI', sans-serif;
      background: #f8fafc;
    }
    /* делаем «раздвижку» */
    .wrapper {
      flex: 1;
      display: flex;
      flex-direction: column;
    }
    main {
      flex: 1; /* заполняет всё доступное место внутри wrapper */
    }
    .footer {
      width: 100%;
      background-color: rgb(68, 93, 202);
      color: #fff;
      padding: 20px 0;
      font-size: 14px;
      line-height: 1.4;
    }
    .footer__container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 0 20px;
      box-sizing: border-box;
    }
    .footer__content {
      text-align: center;
    }
  </style>
</head>
<body>
  </main>
  

  <footer class="footer">
    <div class="footer__container">
      <div class="footer__content">
        <p>&copy; <?= date('Y'); ?> Юридическое агентство. Все права защищены.</p>
      </div>
    </div>
  </footer>
</body>
</html>
