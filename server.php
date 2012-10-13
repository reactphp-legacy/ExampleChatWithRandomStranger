<?php

// nc localhost 4000

require 'vendor/autoload.php';

$logger = new Monolog\Logger('chatroulette');
$logger->pushHandler(new Monolog\Handler\StreamHandler(STDOUT));

$chatroulette = new Igorw\Chatroulette\Chatroulette($logger);

$loop = React\EventLoop\Factory::create();
$socket = new React\Socket\Server($loop);

$i = 0;

$socket->on('connection', function ($conn) use (&$i, $chatroulette) {
    $conn->id = ++$i;
    $chatroulette->connect($conn);
});

$socket->listen(4000);
$loop->run();
