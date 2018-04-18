<?php
// test
require('../vendor/autoload.php');

$app = new Silex\Application();
//deuxieme ommentaire
//test local 2
//test git local github
// commentaire test github
$app['debug'] = true;

// Register the monolog logging service
$app->register(new Silex\Provider\MonologServiceProvider(), array(
  'monolog.logfile' => 'php://stderr',
));

// Register view rendering
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/views',
));

// Our web handlers

$app->get('/', function() use($app) {
  $app['monolog']->addDebug('logging output.');
  return $app['twig']->render('index.twig');
});

$app->get('/', function() use($app) {
  $app['monolog']->addDebug('logging output.');
  return str_repeat('Hello', getenv('TIMES'));
});

$app->get('/cowsay', function() use($app) {
  $app['monolog']->addDebug('cowsay');
  return "<pre>".\Cowsayphp\Cow::say("Cool beans")."</pre>";
});

$app->get('/hw', function() use($app) {
  $app['monolog']->addDebug('logging output.');
  return str_repeat('Hello World', 1);
});

$app->get('/hwt', function() use($app) {
  $app['monolog']->addDebug('logging output.');
  return ('Hello World');
});

$dbopts = parse_url(getenv('DATABASE_URL'));
$app->register(new Csanquer\Silex\PdoServiceProvider\Provider\PDOServiceProvider('pdo'),
               array(
                'pdo.server' => array(
                   'driver'   => 'pgsql',
                   'user' => $dbopts["user"],
                   'password' => $dbopts["pass"],
                   'host' => $dbopts["host"],
                   'port' => $dbopts["port"],
                   'dbname' => ltrim($dbopts["path"],'/')
                   )
               )
);

$app->get('/db/', function() use($app) {
  $st = $app['pdo']->prepare('SELECT name FROM test_table');
  $st->execute();

  $names = array();
  while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
    $app['monolog']->addDebug('Row ' . $row['name']);
    $names[] = $row;
  }

  return $app['twig']->render('database.twig', array(
    'names' => $names
  ));
});

$app->get('/db2/', function() use($app) {
  $st = $app['pdo']->prepare('SELECT sms FROM arduino_test');
  $st->execute();

  $names = array();
  while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
    $app['monolog']->addDebug('Row ' . $row['sms']);
    $names[] = $row;
  }

  return $app['twig']->render('database2.twig', array(
    'names' => $names
  ));
});

$app->get('/db3/', function() use($app) {
  $st = $app['pdo']->prepare("INSERT INTO test_table VALUES (2, 'arduino_test')");
  $st->execute();

  $app['monolog']->addDebug('logging output.');
  return ('Insert val');
});

$app->get('/db4/', function() use($app) {
  $st = $app['pdo']->prepare('SELECT name FROM test_incr');
  $st->execute();

  $names = array();
  while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
    $app['monolog']->addDebug('Row ' . $row['name']);
    $names[] = $row;
  }

  return $app['twig']->render('database3.twig', array(
    'names' => $names
  ));
});

$app->get('/db5/', function() use($app) {
  $st = $app['pdo']->prepare("INSERT INTO test_incr (name) VALUES ('arduino_test_db5')");
  $st->execute();

  $app['monolog']->addDebug('logging output.');
  return ('Insert val');
});

$app->get('/db6/', function() use($app) {
  $app['monolog']->addDebug('logging output.');
  $to = "florian.bawin@hotmail.com";
  //$message = $_GET['text'];
  return "done";
});

$app->get('/db7/', function() use($app) {
  $app['monolog']->addDebug('logging output.');
  $dbusername = "arduino";
  return $dbusername;
});

$app->get('/db8/', function() use($app) {
  $app['monolog']->addDebug('logging output.');
  $dbusername = "arduino";
  $message = $_GET['text'];
  return "$message";
});

$app->get('/db9/', function() use($app) {
  $app['monolog']->addDebug('logging output.');
  $dbusername = "florian.bawin@hotmail.com";
  return "$dbusername";
});

$app->get('/db10/', function() use($app) {
  $app['monolog']->addDebug('logging output.');
  $dbusername = "arduino";
  $message = $_GET["text"];
  return "$message";
});

$app->get('/db11/', function() use($app) {
  $app['monolog']->addDebug('logging output.');
  $dbusername = "arduino";
  $message = $_GET["text"];
  
  $st = $app['pdo']->prepare("INSERT INTO test_incr (name) VALUES ('$dbusername')");
  $st->execute();
  
  return "$message";
});

$app->get('/db12/', function() use($app) {
  $app['monolog']->addDebug('logging output.');
  $dbusername = "arduino";
  $message = $_GET["text"];
  
  $st = $app['pdo']->prepare("INSERT INTO test_incr (name) VALUES ('$message')");
  $st->execute();
  
  return "$message";
});


// heroku don't support send email
$app->get('/email', function() use($app) {
  $app['monolog']->addDebug('logging output.');
  $to = "florian.bawin@hotmail.com";       //Receiver email.
  $subject = "Arduino";
  $message = "Test arduino";
  //$message = $_GET['text'];               //Text variable will be the message content
  $header = "From: Arduino\r\n";
  $header .= "Cc:\r\n";
  $header .= "MIME-Version: 1.0\r\n";
  $header .= "Content-type: text/html\r\n";
  $email = "arduino";
  //$retval = mail ($to,$subject,$message,$header);
  $retval = mail ($to,"$subject",$message, "From:" . $email);
  
  if( $retval == true ) {
            return "done";
  }else {
            return "echec";
        }
});

$app->run();
