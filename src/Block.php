<?php

namespace Blockchain;

class Block
{
    public $timestamp;
    public $data;
    public $prevBlockHash;
    public $hash;
    public $targetZeros;
    public $nonce;

    public function __construct($timestamp, $data, $prevBlockHash)
    {
        $this->timestamp = $timestamp;
        $this->data = $data;
        $this->prevBlockHash = $prevBlockHash;
        $this->nonce = 0;
    }
}