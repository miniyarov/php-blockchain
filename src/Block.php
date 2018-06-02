<?php

namespace Blockchain;

class Block implements \JsonSerializable
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

    public function jsonSerialize()
    {
        return [
            $this->timestamp,
            $this->data,
            $this->prevBlockHash,
            $this->hash,
            $this->targetZeros,
            $this->nonce
        ];
    }

    public static function jsonDeserialize(array $decoded)
    {
        [$timestamp, $data, $prevBlockHash, $hash, $targetZeros, $nonce] = $decoded;

        $block = new self($timestamp, $data, $prevBlockHash);
        $block->hash = $hash;
        $block->targetZeros = $targetZeros;
        $block->nonce = $nonce;

        return $block;
    }
}