<?php

namespace Blockchain;

class Block
{
    public $timestamp;
    public $data;
    public $prevBlockHash;
    public $hash;

    public function __construct($timestamp, $data, $prevBlockHash)
    {
        $this->timestamp = $timestamp;
        $this->data = $data;
        $this->prevBlockHash = $prevBlockHash;
    }

    public function setHash()
    {
        $headers = $this->prevBlockHash . $this->data . $this->timestamp;
        $this->hash = hash('sha256', $headers);
        return $this;
    }
}