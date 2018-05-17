<?php

namespace Blockchain;

class Blockchain
{
    /**
     * @var Block[]
     */
    public $blocks = [];

    public function __construct()
    {
        $this->blocks[] = newBlock('Genesis Block', '');
    }

    public function addBlock(string $data)
    {
        $prevBlock = end($this->blocks);

        $this->blocks[] = newBlock($data, $prevBlock->hash);
    }
}