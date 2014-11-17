<?PHP
$time = microtime();
$time = explode(' ', $time);
$start = $time[1] + $time[0];
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <title>Example LAMPstack app</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/style.css">
  </head>
  <body>

    <h1>System info</h1>
    <?PHP
    echo '<b>PHP</b>: ' . exec("php -v | head -1") . '<br>';
    echo '<b>MySQL</b>: ' . exec("mysql -V") . '<br>';
    echo '<b>Apache2</b>: ' . exec("apache2 -v | grep version") . '<br>';
    echo '<b>Varnish</b>: ' . exec("varnishd -V 2>&1 | grep varnish") . '<br>';
    echo '<b>Memcached</b>: ' . exec("memcached -h | head -1 | awk '{print $2}'") . '<br>';
    ?>

    <h1>MySQL test:</h1>
    <?PHP

    if(isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] != '') {
      $ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
      $ip_address = $_SERVER['REMOTE_ADDR'];
    }
    $ip_address = explode(',', $ip_address);
    $ip_address = $ip_address[0];
    try {
      # open connection
      $db = new PDO('mysql:host=localhost;dbname=examplecom;charset=utf8', 'examplecom', 'kjb1jk4523bkj', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

      $query = $db->query('SELECT * FROM visitor_log');

      $visits = 1;
      $found = false;
      foreach ($query as $row) {
  if ($row['ip'] == $ip_address) {
    $visits = $row['visits'];
          $found = true;
  }
  echo $row['ip'] . ': ' . $row['visits'] . '<br>';
      }
      if ($found) {
  $save = $db->prepare("UPDATE visitor_log SET visits=? WHERE ip=? LIMIT 1");
      } else {
        $save = $db->prepare("INSERT INTO visitor_log (visits, ip) VALUES (?, ?)");
      }
      $save->execute(array(++$visits, $ip_address));

      # close connection
      $db = null;
    } catch(PDOException $e) {
      echo $e->getMessage();
    }

    echo '<h1>Memcached test:</h1>';
    $m = new Memcached();
    $m->addServer('localhost', 11211);
    $m->set('string', 'Memcached is working :)');
    var_dump($m->get('string'));
    echo '<br>';

    echo '<h1>Varnish test: </h1><a href="/?purge=yes">Purge cache</a><br>';

    $time = microtime();
    $time = explode(' ', $time);
    $time = $time[1] + $time[0];
    $finish = $time;
    $total_time = round(($finish - $start), 4);
    echo '<br>Page generated in '.$total_time.' seconds.';
    ?>
    <?PHP
      if (isset($_GET['purge'])) {
        $curl = curl_init("http://localhost");
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PURGE");
        curl_exec($curl);
      }
      echo "<br>" . date("D M j G:i:s T Y") . "<br>";
    ?>
  </body>
</html>