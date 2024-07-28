<?php

try {
    $pdo = new PDO('mysql:host=localhost;dbname=ijdb;charset=utf8mb4', 'ijdbuser', 'mypassword');
    
    // $sql = 'UPDATE joke SET jokedate="2024-01-01"
    //   WHERE joketext LIKE "%programmer%"';

    // $affectedRows = $pdo->exec($sql);

    // $output = 'Updated ' . $affectedRows .' rows.';

    $sql = 'SELECT `joketext` FROM `joke`';
    $result = $pdo->query($sql);

    foreach ($result as $row) {
      $jokes[] = $row['joketext'];
    }
}

catch (PDOException $e) {
  $error = 'Unable to connect to the database server: ' . $e->getMessage() . ' in ' .
  $e->getFile() . ':' . $e->getLine();
}
  
include  __DIR__ . '/../templates/jokes.html.php';