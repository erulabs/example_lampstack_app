<!DOCTYPE html>
<html lang="en">
  <head>
    <title>Example LAMPstack app</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/style.css">
  </head>
  <body>
    <?PHP
    try {
      # open connection
      $db = new PDO('mysql:host=localhost;dbname=examplecom;charset=utf8', 'examplecom', 'kjb1jk4523bkj');

      $query = $db->query('SELECT * FROM visior_log');
      $result = $query->fetch(PDO::FETCH_ASSOC);

      echo '<pre>';
      print_r($result);
      echo '</pre>';

      # close connection
      $db = null;
    } catch(PDOException $e) {
      echo $e->getMessage();
    }
    ?>
  </body>
</html>