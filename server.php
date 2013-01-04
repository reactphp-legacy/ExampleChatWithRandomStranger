<?php

// nc localhost 4000

use React\Chatroulette\AppInterface;
use React\Chatroulette\LoggingApp;
use React\Chatroulette\PairApp;
use React\Chatroulette\TextApp;

require 'vendor/autoload.php';

$logger = new Monolog\Logger('chatroulette');
$logger->pushHandler(new Monolog\Handler\StreamHandler(STDOUT));

$app = new LoggingApp(
    new TextApp(new PairApp()),
    $logger
);

$loop = React\EventLoop\Factory::create();
$socket = new React\Socket\Server($loop);

$i = 0;
$names = ['Alice', 'Bob', 'Carol', 'Dave', 'Erin', 'Frank', 'Eve',
          'Mallory', 'Oscar', 'Peggy', 'Trent', 'Walter'];

$socket->on('connection', function ($conn) use (&$i, $names, $app) {
    $conn->id = isset($names[$i]) ? $names[$i] : $i;
    $app->connect($conn);
    $i++;
});

$socket->listen(4000);
$loop->run();
