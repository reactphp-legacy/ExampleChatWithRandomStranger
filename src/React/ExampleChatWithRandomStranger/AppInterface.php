<?php

namespace React\ExampleChatWithRandomStranger;

use React\Socket\ConnectionInterface;

interface AppInterface
{
    function connect(ConnectionInterface $conn);
}
