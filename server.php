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

$socket->on('connection', function ($conn) use (&$i, $app) {
    $conn->id = ++$i;
    $app->connect($conn);
});

$socket->listen(4000);
$loop->run();
