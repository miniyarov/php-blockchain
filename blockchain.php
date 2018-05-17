<?php

require 'vendor/autoload.php';

use \Blockchain\Blockchain;

$blockchain = new Blockchain();

$blockchain->addBlock('Send 1 BTC to Ulugbek');
$blockchain->addBlock('Send 100 BTC to Ulugbek');

foreach ($blockchain->blocks as $block) {
    echo "Prev. hash: {$block->prevBlockHash}\nData: {$block->data}\nHash: {$block->hash}\n\n";
}