<?php

require 'vendor/autoload.php';

use \Blockchain\Blockchain;
use \Blockchain\ProofOfWork;

$blockchain = new Blockchain();

$blockchain->addBlock('Send 1 BTC to Ulugbek');
$blockchain->addBlock('Send 100 BTC to Ulugbek');

foreach ($blockchain->blocks as $block) {
    echo "Prev. hash: {$block->prevBlockHash}\n";
    echo "Data: {$block->data}\n";
    echo "Hash: {$block->hash}\n";
    echo "PoW: " . var_export((new ProofOfWork($block))->validate(), true) . PHP_EOL . PHP_EOL;
}