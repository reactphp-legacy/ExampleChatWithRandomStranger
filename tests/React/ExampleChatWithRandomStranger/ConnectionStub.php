<?php

namespace React\ExampleChatWithRandomStranger;

use Evenement\EventEmitter;
use React\Socket\ConnectionInterface;
use React\Stream\WritableStreamInterface;
use React\Stream\Util;

class ConnectionStub extends EventEmitter implements ConnectionInterface
{
    public $closed = false;
    public $data;

    public function isReadable()
    {
        return !$this->closed;
    }

    public function pause()
    {
    }

    public function resume()
    {
    }

    public function pipe(WritableStreamInterface $dest, array $options = array())
    {
        Util::pipe($this, $dest, $options);

        return $dest;
    }

    public function isWritable()
    {
        return !$this->closed;
    }

    public function write($data)
    {
        $this->data .= $data;
    }

    public function end($data = null)
    {
        $this->write($data);
        $this->close();
    }

    public function close()
    {
        if ($this->closed) {
            return;
        }

        $this->closed = true;
        $this->emit('end', array($this));
        $this->emit('close', array($this));
        $this->removeAllListeners();
    }

    public function getRemoteAddress()
    {
        return '127.0.0.1';
    }
}
