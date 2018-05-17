<?php

use \Blockchain\Block;

function newBlock(string $data, string $prevBlockHash)
{
    return (new Block(time(), $data, $prevBlockHash))
        ->setHash();
}