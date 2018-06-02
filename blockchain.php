<?php

require 'vendor/autoload.php';

use \Blockchain\Blockchain;
use \Blockchain\CLI;

$blockchain = new Blockchain();
$cli = new CLI($blockchain);
$cli->run($argv);
