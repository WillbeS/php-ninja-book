<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="<?= '/' . $baseUrl . 'jokes.css' ?>">
    <title><?=$title?></title>
  </head>
  <body>

  <header>
    <h1>Internet Joke Database</h1>
  </header>
  <nav>
    <ul>
      <li><a href="<?= '/' . $baseUrl ?>">Home</a></li>
      <li><a href="<?= '/' . $baseUrl . 'joke/list' ?>">Jokes List</a></li>
      <li><a href="<?= '/' . $baseUrl . 'joke/edit' ?>">Add a new Joke</a></li>
      <?php if ($loggedIn): ?>
        <li><a href="<?= '/' . $baseUrl . 'login/logout' ?>">Log out</a></li>
      <?php else: ?>
        <li><a href="<?= '/' . $baseUrl . 'login/login' ?>">Log in</a></li>
      <?php endif; ?>
    </ul>
  </nav>

  <main>
  <?=$output?>
  </main>

  <?php include 'footer.html.php'; ?>
  </body>
</html>