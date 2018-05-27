<?php

use \Blockchain\Block;
use \Blockchain\ProofOfWork;

function newBlock(string $data, string $prevBlockHash)
{
    $block = (new Block(time(), $data, $prevBlockHash));

    return (new ProofOfWork($block))->run();
}